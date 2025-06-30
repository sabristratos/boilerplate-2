<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;

    public string $newFormName = '';

    public function openCreateModal(): void
    {
        $this->reset('newFormName');
        $this->resetErrorBag();
        Flux::modal('create-form')->show();
    }

    public function create()
    {
        $this->validate([
            'newFormName' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $form = new Form([
            'user_id' => auth()->id(),
        ]);

        $form->setTranslation('name', 'en', $this->newFormName)->save();

        Flux::modal('create-form')->close();

        $this->redirect(route('admin.forms.edit', $form));
    }

    public function render()
    {
        $forms = Form::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.admin.forms.index', [
            'forms' => $forms,
        ])->title('Forms');
    }
}
