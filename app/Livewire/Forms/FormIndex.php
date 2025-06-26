<?php

namespace App\Livewire\Forms;

use App\Facades\Settings;
use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;

class FormIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public function getFormsProperty()
    {
        return Form::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);
    }

    public function getLocalesProperty()
    {
        return app('settings')->get('locales', ['en' => 'English']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create forms');

        $form = Form::create([
            'name' => 'New Form',
            'title' => [
                'en' => 'New Form',
            ],
            'success_message' => config('forms.defaults.success_message', []),
        ]);

        session()->flash('toast', ['text' => __('forms.toast_form_created'), 'variant' => 'success']);

        return $this->redirect(route('admin.forms.edit', [
            'form' => $form,
            'locale' => app()->getLocale(),
        ]), navigate: true);
    }

    public function deleteForm(Form $form): void
    {
        $this->authorize('delete', $form);
        $form->delete();
        session()->flash('toast', ['text' => __('forms.toast_form_deleted'), 'variant' => 'success']);
    }

    public function render()
    {
        return view('livewire.forms.form-index');
    }
}
