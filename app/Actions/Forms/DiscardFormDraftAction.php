<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Models\Form;

/**
 * Action to discard form draft changes.
 *
 * This action discards any unpublished changes by reverting to the latest
 * published revision, effectively removing draft work.
 */
class DiscardFormDraftAction
{
    /**
     * Discard draft changes by reverting to the latest published revision.
     *
     * @param Form $form The form to discard drafts for
     * @return Form The updated form
     */
    public function execute(Form $form): Form
    {
        $latestPublishedRevision = $form->latestPublishedRevision();

        if (!$latestPublishedRevision) {
            // If no published revision exists, create an initial published revision
            $form->createRevision(
                'initial_publish',
                'Initial form creation',
                [
                    'action' => 'initial_publish',
                    'timestamp' => now()->toISOString(),
                ],
                true // Published
            );
        } else {
            // Revert to the latest published revision
            $form->revertToRevision($latestPublishedRevision);
        }

        // Create a revision to track the discard action
        $form->createRevision(
            'discard_draft',
            'Draft changes discarded',
            [
                'action' => 'discard_draft',
                'timestamp' => now()->toISOString(),
            ],
            false // Not published
        );

        return $form;
    }
} 