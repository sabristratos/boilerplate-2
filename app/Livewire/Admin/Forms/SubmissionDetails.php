<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Forms;

use App\DTOs\FormDTO;
use App\DTOs\FormSubmissionDTO;
use App\DTOs\DTOFactory;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\Contracts\FormServiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Livewire component for displaying form submission details.
 *
 * This component shows detailed information about a specific form
 * submission, including the form data and metadata. It uses DTOs
 * and services for data handling and business logic.
 */
#[Layout('components.layouts.app')]
class SubmissionDetails extends Component
{
    public Form $form;

    public FormSubmission $submission;

    /**
     * Form service instance.
     */
    protected FormServiceInterface $formService;

    /**
     * Boot the component with dependencies.
     */
    public function boot(FormServiceInterface $formService): void
    {
        $this->formService = $formService;
    }

    /**
     * Mount the component with the form and submission.
     */
    public function mount(Form $form, FormSubmission $submission): void
    {
        $this->form = $form;
        $this->submission = $submission;

        // Ensure the submission belongs to the form
        if ($this->submission->form_id !== $this->form->id) {
            abort(404);
        }
    }

    /**
     * Delete the current submission.
     */
    public function deleteSubmission(): void
    {
        try {
            $this->formService->deleteSubmission($this->submission);
            
            $this->redirect(route('admin.forms.submissions', $this->form));
            
        } catch (\Exception $e) {
            logger()->error('Failed to delete submission', [
                'submission_id' => $this->submission->id,
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the form DTO.
     */
    public function getFormDTO(): FormDTO
    {
        return DTOFactory::createFormDTO($this->form);
    }

    /**
     * Get the submission DTO.
     */
    public function getSubmissionDTO(): FormSubmissionDTO
    {
        return DTOFactory::createFormSubmissionDTO($this->submission);
    }

    /**
     * Get formatted submission data for display.
     */
    public function getFormattedSubmissionData(): array
    {
        $submissionDto = $this->getSubmissionDTO();
        return $submissionDto->getFormattedData();
    }

    /**
     * Check if submission contains sensitive data.
     */
    public function hasSensitiveData(): bool
    {
        $submissionDto = $this->getSubmissionDTO();
        return $submissionDto->containsSensitiveData();
    }

    /**
     * Get submission age in human-readable format.
     */
    public function getSubmissionAge(): string
    {
        $submissionDto = $this->getSubmissionDTO();
        return $submissionDto->getAgeForHumans();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.forms.submission-details', [
            'formDto' => $this->getFormDTO(),
            'submissionDto' => $this->getSubmissionDTO(),
            'formattedData' => $this->getFormattedSubmissionData(),
            'hasSensitiveData' => $this->hasSensitiveData(),
            'submissionAge' => $this->getSubmissionAge(),
        ])->title(__('forms.submission_details').' - '.$this->form->getTranslation('name', app()->getLocale()));
    }
}
