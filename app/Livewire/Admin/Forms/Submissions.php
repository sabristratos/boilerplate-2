<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Models\FormSubmission;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Submissions extends Component
{
    use WithPagination;

    public Form $form;

    public function mount(Form $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $submissions = $this->form->submissions()
            ->latest()
            ->paginate(20);

        return view('livewire.admin.forms.submissions', [
            'submissions' => $submissions,
        ])->title('Form Submissions - ' . $this->form->getTranslation('name', 'en'));
    }
} 