<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\DTOs\FormDTO;
use App\Enums\FormStatus;
use App\Models\Form;
use App\Services\Contracts\FormServiceInterface;

/**
 * Action to publish a form.
 *
 * This action publishes the current form state by creating a published revision
 * and updating the form status to PUBLISHED.
 */
class PublishFormAction
{
    public function __construct(
        private readonly FormServiceInterface $formService
    ) {
    }

    /**
     * Publish the form.
     *
     * @param Form $form The form to publish
     * @param FormDTO $formDto The form data to publish
     * @return Form The updated form
     */
    public function execute(Form $form, FormDTO $formDto): Form
    {
        // Update the form with the new data
        $this->formService->updateForm($form, $formDto);

        // Update form status to published
        $form->status = FormStatus::PUBLISHED;
        $form->save();

        // Create a published revision
        $form->createRevision(
            'publish',
            'Form published',
            [
                'action' => 'publish',
                'timestamp' => now()->toISOString(),
                'previous_status' => $form->getOriginal('status'),
            ],
            true // Published
        );

        return $form;
    }
} 