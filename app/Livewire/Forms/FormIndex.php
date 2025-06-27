<?php

namespace App\Livewire\Forms;

use App\Facades\Settings;
use App\Models\Form;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Livewire\WithPagination;

class FormIndex extends Component
{
    use WithPagination, WithToastNotifications;

    public string $search = '';
    public int $perPage = 10;

    public function getFormsProperty()
    {
        $locale = app()->getLocale();

        return Form::query()
            ->when($this->search, fn ($query) => $query->where('name->' . $locale, 'like', '%' . $this->search . '%'))
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
            'name' => [
                'en' => 'New Form',
            ],
            'title' => [
                'en' => 'New Form',
            ],
            'success_message' => config('forms.defaults.success_message', []),
        ]);

        $this->showSuccessToast(__('forms.toast_form_created'));

        return $this->redirect(route('admin.forms.edit', [
            'form' => $form,
            'locale' => app()->getLocale(),
        ]), navigate: true);
    }

    public function deleteForm(Form $form): void
    {
        $this->authorize('delete', $form);
        $form->delete();
        $this->showSuccessToast(__('forms.toast_form_deleted'));
    }

    public function render()
    {
        return view('livewire.forms.form-index');
    }
}
