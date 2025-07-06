<?php

declare(strict_types=1);

namespace App\Actions\Revisions;

use App\Models\Revision;
use App\Services\RevisionService;
use Illuminate\Database\Eloquent\Model;

/**
 * Action for reverting a model to a specific revision.
 *
 * This action handles reverting models to previous states,
 * creating a new revision to track the revert operation.
 */
class RevertToRevisionAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        private RevisionService $revisionService
    ) {}

    /**
     * Execute the action to revert a model to a specific revision.
     *
     * @param  Model  $model  The model to revert
     * @param  Revision  $revision  The revision to revert to
     * @return bool True if the revert was successful
     */
    public function execute(Model $model, Revision $revision): bool
    {
        return $this->revisionService->revertToRevision($model, $revision);
    }
} 