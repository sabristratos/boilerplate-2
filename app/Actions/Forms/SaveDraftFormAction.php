<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\DTOs\FormDTO;
use App\Models\Form;
use App\Services\Contracts\FormServiceInterface;

/**
 * Action to save a form as a draft.
 *
 * This action saves the current form state as an unpublished revision,
 * allowing users to work on changes without affecting the published version.
 */
class SaveDraftFormAction
{
    public function __construct(
        private readonly FormServiceInterface $formService
    ) {
    }

    /**
     * Save the form as a draft.
     *
     * @param Form $form The form to save
     * @param FormDTO $formDto The form data to save
     * @return Form The updated form
     */
    public function execute(Form $form, FormDTO $formDto): Form
    {
        // Update the form with the new data
        $this->formService->updateForm($form, $formDto);

        // Create an unpublished revision for the draft
        $form->createRevision(
            'draft',
            'Form saved as draft',
            [
                'action' => 'draft_save',
                'timestamp' => now()->toISOString(),
            ],
            false // Not published
        );

        return $form;
    }
} 