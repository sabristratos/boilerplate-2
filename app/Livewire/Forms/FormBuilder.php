<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Services\SettingsManager;
use Flux\Flux;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\Component;

class FormBuilder extends Component
{
    public Form $form;

    public string $name;
    public ?int $editingFieldId = null;

    public array $editingFieldState = [];

    public string $activeLocale;

    public array $locales;

    public string $activeTab = 'fields';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'form.recipient_email' => 'nullable|email',
            'form.send_notification' => 'boolean',
            'form.title' => 'array',
            'form.description' => 'array',
            'form.success_message' => 'array',
        ];
    }

    public function mount(Request $request, Form $form)
    {
        $this->authorize('view forms');
        $this->form = $form;
        $this->name = $form->name;
        $this->locales = app(SettingsManager::class)->get('locales', ['en' => 'English']);
        $this->activeLocale = $request->query('locale', app()->getLocale());

        if (! array_key_exists($this->activeLocale, $this->locales)) {
            $this->activeLocale = app()->getLocale();
        }
    }

    public function save()
    {
        $this->authorize('edit forms');

        $this->validate([
            'name' => 'required|string|max:255',
            'form.recipient_email' => 'nullable|email',
            'form.send_notification' => 'boolean',
            'form.title' => 'array',
            'form.description' => 'array',
            'form.success_message' => 'array',
        ]);
        $this->form->name = $this->name;
        $this->form->save();

        Flux::toast(text: __('forms.toast_form_saved'), variant: 'success');
    }

    public function addField(string $type): void
    {
        $this->authorize('edit forms');

        $maxSortOrder = $this->form->formFields()->max('sort_order');

        $this->form->formFields()->create([
            'type' => $type,
            'name' => strtolower($type) . '_' . uniqid(),
            'label' => [app()->getLocale() => 'New ' . $type . ' field'],
            'sort_order' => $maxSortOrder + 1,
        ]);

        $this->form = $this->form->fresh('formFields');
        $this->dispatch('form-updated');
        $this->editField($this->form->formFields()->latest('id')->first()->id);
    }

    #[On('removeField')]
    public function removeField(int $fieldId): void
    {
        $this->authorize('edit forms');

        $field = $this->form->formFields()->find($fieldId);
        $field->delete();

        $this->form = $this->form->fresh('formFields');
        $this->dispatch('form-updated');

        if ($this->editingFieldId === $fieldId) {
            $this->editingFieldId = null;
        }

        Flux::toast(text: __('forms.toast_field_removed'), variant: 'success');
    }

    #[On('editField')]
    public function editField(int $fieldId)
    {
        $this->authorize('edit forms');
        $field = $this->form->formFields()->findOrFail($fieldId);
        $this->editingFieldId = $field->id;
        $this->editingFieldState = $field->toArray();

        $options = $this->editingFieldState['options'] ?? [];
        if (is_array($options)) {
            foreach ($options as $locale => $optionArray) {
                if (is_array($optionArray)) {
                    $options[$locale] = implode("\n", $optionArray);
                }
            }
        }
        $this->editingFieldState['options'] = $options;

        $this->editingFieldState['is_required'] = str_contains($field->validation_rules ?? '', 'required');
        Flux::modal('edit-field-modal')->show();
    }

    public function saveField(): void
    {
        $this->authorize('edit forms');

        if (! $this->editingFieldId) {
            return;
        }

        $this->validate([
            'editingFieldState.label.' . $this->activeLocale => 'required|string',
            'editingFieldState.name' => 'required|string|alpha_dash',
            'editingFieldState.type' => 'required',
            'editingFieldState.is_required' => 'boolean',
            'editingFieldState.validation_rules' => 'nullable|string',
        ]);

        $field = $this->form->formFields()->find($this->editingFieldId);
        if ($field) {
            // Manually handle translatable fields
            $field->setTranslations('label', $this->editingFieldState['label']);
            $field->setTranslations('placeholder', $this->editingFieldState['placeholder'] ?? []);

            $options = $this->editingFieldState['options'] ?? [];
            if (is_array($options)) {
                foreach ($options as $locale => $optionString) {
                    if (is_string($optionString)) {
                        $options[$locale] = array_values(array_filter(array_map('trim', explode("\n", $optionString))));
                    }
                }
            }
            $field->setTranslations('options', $options);
            $field->name = $this->editingFieldState['name'];
            $field->type = $this->editingFieldState['type'];
            $field->is_required = $this->editingFieldState['is_required'] ?? false;
            $field->validation_rules = $this->editingFieldState['validation_rules'] ?? '';
            
            $field->save();

            $this->form = $this->form->fresh('formFields');
            $this->editingFieldId = null;
            $this->dispatch('form-updated');
            $this->closeModal();
            Flux::toast(text: __('forms.toast_form_saved'), variant: 'success');
        }
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', name: 'edit-field-modal');
    }

    public function render()
    {
        return view('livewire.forms.form-builder')
            ->layout('components.layouts.app');
    }
}
