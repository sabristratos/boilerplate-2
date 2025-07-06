<?php

declare(strict_types=1);

namespace App\Actions\Revisions;

use App\Models\Revision;
use App\Services\RevisionService;
use Illuminate\Database\Eloquent\Model;

/**
 * Action for creating revisions.
 *
 * This action handles the creation of revisions for any model,
 * providing a clean interface for revision management.
 */
class CreateRevisionAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        private RevisionService $revisionService
    ) {}

    /**
     * Execute the action to create a revision.
     *
     * @param  Model  $model  The model to create a revision for
     * @param  string  $action  The action that triggered the revision
     * @param  string|null  $description  Optional description of the revision
     * @param  array<string, mixed>  $metadata  Additional metadata for the revision
     * @return Revision The created revision
     */
    public function execute(
        Model $model,
        string $action,
        ?string $description = null,
        array $metadata = []
    ): Revision {
        return $this->revisionService->createRevision(
            $model,
            $action,
            $description,
            $metadata
        );
    }
} 