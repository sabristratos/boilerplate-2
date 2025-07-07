<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Revision;
use App\Services\RevisionService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait for models that support revision tracking.
 *
 * This trait provides revision functionality for any model,
 * allowing it to track changes, create snapshots, and manage versions.
 */
trait HasRevisions
{
    /**
     * If true, skip automatic revision creation for this model instance.
     */
    public bool $skipRevision = false;

    /**
     * Boot the trait and register event listeners.
     */
    protected static function bootHasRevisions(): void
    {
        static::created(function ($model): void {
            $model->createRevision('create', 'Initial creation', [], true); // Published on create
        });

        static::updated(function ($model): void {
            if (! ($model->skipRevision ?? false)) {
                $model->createRevision('update'); // Draft by default
            }
        });

        static::deleted(function ($model): void {
            $model->createRevision('delete'); // Draft by default
        });
    }

    /**
     * Get all revisions for this model.
     *
     * @return MorphMany<Revision>
     */
    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest revision.
     */
    public function latestRevision(): ?Revision
    {
        return $this->revisions()->first();
    }

    /**
     * Get the latest published revision.
     */
    public function latestPublishedRevision(): ?Revision
    {
        return $this->revisions()->published()->first();
    }

    /**
     * Create a new revision for this model.
     */
    public function createRevision(string $action, ?string $description = null, array $metadata = [], bool $is_published = false): Revision
    {
        $revisionService = app(RevisionService::class);

        return $revisionService->createRevision(
            $this,
            $action,
            $description,
            $metadata,
            $is_published
        );
    }

    /**
     * Create a manual revision (for custom actions like publish, revert).
     */
    public function createManualRevision(string $action, ?string $description = null, array $metadata = [], bool $is_published = false): Revision
    {
        $revisionService = app(RevisionService::class);

        return $revisionService->createManualRevision(
            $this,
            $action,
            $description,
            $metadata,
            $is_published
        );
    }

    /**
     * Revert to a specific revision.
     */
    public function revertToRevision(Revision $revision): bool
    {
        $revisionService = app(RevisionService::class);

        return $revisionService->revertToRevision($this, $revision);
    }

    /**
     * Get the revision data that should be tracked.
     *
     * Override this method in your model to customize what data is tracked.
     *
     * @return array<string, mixed>
     */
    public function getRevisionData(): array
    {
        return $this->toArray();
    }

    /**
     * Get the fields that should be excluded from revision tracking.
     *
     * Override this method in your model to exclude specific fields.
     *
     * @return array<string>
     */
    public function getRevisionExcludedFields(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    /**
     * Get the fields that should be included in change detection.
     *
     * Override this method in your model to include specific fields only.
     *
     * @return array<string>|null
     */
    public function getRevisionTrackedFields(): ?array
    {
        return null; // Track all fields by default
    }

    /**
     * Check if the model has any revisions.
     */
    public function hasRevisions(): bool
    {
        return $this->revisions()->exists();
    }

    /**
     * Get the revision count.
     */
    public function getRevisionCountAttribute(): int
    {
        return $this->revisions()->count();
    }

    /**
     * Get the published revision count.
     */
    public function getPublishedRevisionCountAttribute(): int
    {
        return $this->revisions()->published()->count();
    }
}
