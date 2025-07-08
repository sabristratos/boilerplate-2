<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Revision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing revisions across the application.
 *
 * This service handles the creation, management, and restoration
 * of revisions for any model that uses the HasRevisions trait.
 */
class RevisionService
{
    /**
     * Create a new revision for a model.
     *
     * @param  Model  $model  The model to create a revision for
     * @param  string  $action  The action that triggered the revision
     * @param  string|null  $description  Optional description of the revision
     * @param  array<string, mixed>  $metadata  Additional metadata for the revision
     * @param  bool  $is_published  Whether the revision should be published
     * @return Revision The created revision
     */
    public function createRevision(
        Model $model,
        string $action,
        ?string $description = null,
        array $metadata = [],
        bool $is_published = false
    ): Revision {
        $revisionData = $this->prepareRevisionData($model, $action);

        return DB::transaction(function () use ($model, $action, $description, $metadata, $revisionData, $is_published) {
            $revision = Revision::create([
                'revisionable_type' => $model::class,
                'revisionable_id' => $model->id,
                'user_id' => Auth::id() ?: null,
                'action' => $action,
                'version' => $this->generateVersion($model, $action),
                'data' => $revisionData['data'],
                'changes' => $revisionData['changes'],
                'metadata' => array_merge($metadata, [
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'session_id' => session()->getId(),
                ]),
                'description' => $description ?? $this->generateDescription($model, $action),
                'is_published' => $is_published,
                'published_at' => $is_published ? now() : null,
            ]);

            // Dispatch event for other systems to listen to
            event(new \App\Events\RevisionCreated($revision));

            return $revision;
        });
    }

    /**
     * Create a manual revision (for custom actions).
     *
     * @param  Model  $model  The model to create a revision for
     * @param  string  $action  The action that triggered the revision
     * @param  string|null  $description  Optional description of the revision
     * @param  array<string, mixed>  $metadata  Additional metadata for the revision
     * @param  bool  $is_published  Whether the revision should be published
     * @return Revision The created revision
     */
    public function createManualRevision(
        Model $model,
        string $action,
        ?string $description = null,
        array $metadata = [],
        bool $is_published = false
    ): Revision {
        $revision = $this->createRevision($model, $action, $description, $metadata, $is_published);

        // If this is a published revision, update the model's attributes
        if ($is_published) {
            $this->updateModelFromRevision($model, $revision);
        }

        return $revision;
    }

    /**
     * Revert a model to a specific revision.
     *
     * @param  Model  $model  The model to revert
     * @param  Revision  $revision  The revision to revert to
     * @return bool True if the revert was successful
     */
    public function revertToRevision(Model $model, Revision $revision): bool
    {
        $data = $revision->data;
        $excluded = $model->getRevisionExcludedFields();

        // Prevent automatic revision on update
        $model->skipRevision = true;

        foreach ($data as $key => $value) {
            if (in_array($key, $excluded, true)) {
                continue;
            }
            // For translatable fields, ensure associative array assignment
            if (method_exists($model, 'getTranslatableAttributes') && in_array($key, $model->getTranslatableAttributes(), true)) {
                if (! is_array($value) || array_keys($value) === range(0, count($value) - 1)) {
                    // If not associative, wrap with current locale
                    $value = [$model->getLocale() => $value];
                }
                $model->setTranslations($key, $value);
            } else {
                $model->{$key} = $value;
            }
        }
        $model->save();
        $model->skipRevision = false;
        $model->createManualRevision('revert', 'Reverted to revision '.$revision->version);

        return true;
    }

    /**
     * Get the revision history for a model.
     *
     * @param  Model  $model  The model to get revisions for
     * @param  int|null  $limit  Maximum number of revisions to return
     * @return \Illuminate\Database\Eloquent\Collection<Revision>
     */
    public function getRevisionHistory(Model $model, ?int $limit = null)
    {
        $query = $model->revisions();

        if ($limit !== null && $limit !== 0) {
            $query->limit($limit);
        }

        return $query->with('user')->get();
    }

    /**
     * Compare two revisions and return the differences.
     *
     * @param  Revision  $revision1  The first revision
     * @param  Revision  $revision2  The second revision
     * @return array<string, mixed> The differences between the revisions
     */
    public function compareRevisions(Revision $revision1, Revision $revision2): array
    {
        $data1 = $revision1->data ?? [];
        $data2 = $revision2->data ?? [];

        $differences = [];

        foreach ($data1 as $key => $value1) {
            $value2 = $data2[$key] ?? null;

            if ($value1 !== $value2) {
                $differences[$key] = [
                    'from' => $value1,
                    'to' => $value2,
                ];
            }
        }

        // Check for new fields in revision2
        foreach ($data2 as $key => $value2) {
            if (! isset($data1[$key])) {
                $differences[$key] = [
                    'from' => null,
                    'to' => $value2,
                ];
            }
        }

        return $differences;
    }

    /**
     * Prepare revision data for storage.
     *
     * @param  Model  $model  The model to prepare data for
     * @param  string  $action  The action being performed
     * @return array<string, mixed> The prepared revision data
     */
    protected function prepareRevisionData(Model $model, string $action): array
    {
        $data = $model->getRevisionData();
        $excludedFields = $model->getRevisionExcludedFields();
        $trackedFields = $model->getRevisionTrackedFields();

        // Filter out excluded fields
        $data = array_diff_key($data, array_flip($excludedFields));

        // Filter to only tracked fields if specified
        if ($trackedFields !== null) {
            $data = array_intersect_key($data, array_flip($trackedFields));
        }

        $changes = [];

        if ($action === 'update' && method_exists($model, 'getDirty')) {
            $dirty = $model->getDirty();
            $changes = array_diff_key($dirty, array_flip($excludedFields));

            if ($trackedFields !== null) {
                $changes = array_intersect_key($changes, array_flip($trackedFields));
            }
        }

        return [
            'data' => $data,
            'changes' => $changes,
        ];
    }

    /**
     * Generate a version number for the revision.
     *
     * @param  Model  $model  The model
     * @param  string  $action  The action
     * @return string The version number
     */
    protected function generateVersion(Model $model, string $action): string
    {
        $latestRevision = $model->revisions()->first();

        if (! $latestRevision) {
            return '1.0.0';
        }

        $currentVersion = $latestRevision->version ?? '1.0.0';
        $parts = explode('.', $currentVersion);

        if (count($parts) >= 3) {
            $major = (int) $parts[0];
            $minor = (int) $parts[1];
            $patch = (int) $parts[2];

            return match ($action) {
                'create' => '1.0.0',
                'update' => "{$major}.{$minor}.".($patch + 1),
                'publish' => ($major + 1).'.0.0',
                'revert' => "{$major}.{$minor}.".($patch + 1),
                default => "{$major}.{$minor}.".($patch + 1),
            };
        }

        return '1.0.0';
    }

    /**
     * Generate a description for the revision.
     *
     * @param  Model  $model  The model
     * @param  string  $action  The action
     * @return string The generated description
     */
    protected function generateDescription(Model $model, string $action): string
    {
        $modelName = class_basename($model);

        return match ($action) {
            'create' => __('revisions.descriptions.created', ['model' => $modelName]),
            'update' => __('revisions.descriptions.updated', ['model' => $modelName]),
            'delete' => __('revisions.descriptions.deleted', ['model' => $modelName]),
            'publish' => __('revisions.descriptions.published', ['model' => $modelName]),
            'revert' => __('revisions.descriptions.reverted', ['model' => $modelName]),
            default => __('revisions.descriptions.modified', ['model' => $modelName]),
        };
    }

    /**
     * Restore a model from a revision.
     *
     * @param  Model  $model  The model to restore
     * @param  Revision  $revision  The revision to restore from
     */
    protected function restoreModelFromRevision(Model $model, Revision $revision): void
    {
        $data = $revision->data ?? [];

        foreach ($data as $key => $value) {
            if (in_array($key, $model->getRevisionExcludedFields())) {
                continue;
            }

            $model->setAttribute($key, $value);
        }

        $model->save();
    }

    /**
     * Update the model's attributes from a revision.
     *
     * @param  Model  $model  The model to update
     * @param  Revision  $revision  The revision to apply
     */
    protected function updateModelFromRevision(Model $model, Revision $revision): void
    {
        $data = $revision->data;
        $excluded = $model->getRevisionExcludedFields();

        // Prevent automatic revision on update
        $model->skipRevision = true;

        foreach ($data as $key => $value) {
            if (in_array($key, $excluded, true)) {
                continue;
            }

            // For translatable fields, ensure associative array assignment
            if (method_exists($model, 'getTranslatableAttributes') && in_array($key, $model->getTranslatableAttributes(), true)) {
                if (! is_array($value) || array_keys($value) === range(0, count($value) - 1)) {
                    // If not associative, wrap with current locale
                    $value = [$model->getLocale() => $value];
                }
                $model->setTranslations($key, $value);
            } else {
                $model->{$key} = $value;
            }
        }

        $model->save();
        $model->skipRevision = false;
    }
}
