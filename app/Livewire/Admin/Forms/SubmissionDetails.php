<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Models\FormSubmission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubmissionDetails extends Component
{
    public Form $form;

    public FormSubmission $submission;

    public function mount(Form $form, FormSubmission $submission)
    {
        $this->form = $form;
        $this->submission = $submission;

        // Ensure the submission belongs to the form
        if ($this->submission->form_id !== $this->form->id) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.admin.forms.submission-details')
            ->title(__('forms.submission_details').' - '.$this->form->getTranslation('name', app()->getLocale()));
    }
}
