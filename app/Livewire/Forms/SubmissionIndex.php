<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FormSubmission;

class SubmissionIndex extends Component
{
    use WithPagination;

    public Form $form;
    public ?FormSubmission $selectedSubmission = null;

    public function mount(Form $form)
    {
        $this->authorize('viewAny', $form);

        $this->form = $form;
    }

    public function selectSubmission(int $submissionId)
    {
        $this->selectedSubmission = $this->form->formSubmissions()->find($submissionId);
    }

    public function render()
    {
        $submissions = $this->form->formSubmissions()->latest('submitted_at')->paginate(10);

        return view('livewire.forms.submission-index', [
            'submissions' => $submissions,
        ]);
    }
}
