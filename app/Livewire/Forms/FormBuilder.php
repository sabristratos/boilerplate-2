<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Flux\Flux;
use Illuminate\Support\Str;

class FormBuilder extends Component
{
    use WithToastNotifications;

    public Form $form;

    public ?FormField $selectedField = null;

    public ?int $fieldToDeleteId = null;

    public array $fieldData = [];

    public array $selectedRules = [];

    public ?string $min = null;

    public ?string $max = null;

    public string $activeLocale;

    public string $activeTab = 'fields';

    public bool $nameManuallyEdited = false;

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.is_active' => 'boolean',
            'form.has_captcha' => 'boolean',
            'form.sends_notifications' => 'boolean',
            'form.notification_email' => 'nullable|email',
            'fieldData.name' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'fieldData.label' => 'required|string|max:255',
            'fieldData.placeholder' => 'nullable|string|max:255',
            'fieldData.is_required' => 'boolean',
            'fieldData.validation_rules' => 'nullable|string',
            'fieldData.options' => 'nullable|array',
            'fieldData.options.*.label' => 'nullable|string',
            'fieldData.options.*.value' => 'nullable|string',
        ];
    }

    public function mount(Form $form)
    {
        $this->form = $form;
        $this->form->load('fields');
        $this->activeLocale = app()->getLocale();
    }

    public function getPredefinedRulesProperty(): array
    {
        return [
            'required', 'email', 'numeric', 'url', 'string', 'boolean', 'date', 'image', 'alpha', 'alpha_dash', 'alpha_num'
        ];
    }

    public function getFieldTypesProperty()
    {
        return FormFieldType::cases();
    }

    public function getPreviewComponent(FormField $field): string
    {
        return match ($field->type) {
            FormFieldType::TEXT, FormFieldType::EMAIL, FormFieldType::NUMBER, FormFieldType::DATE, FormFieldType::TIME => 'forms.previews.text',
            FormFieldType::TEXTAREA => 'forms.previews.textarea',
            FormFieldType::SELECT => 'forms.previews.select',
            FormFieldType::CHECKBOX => 'forms.previews.checkbox',
            FormFieldType::RADIO => 'forms.previews.radio',
            FormFieldType::FILE => 'forms.previews.file',
            FormFieldType::SECTION => 'forms.previews.section',
        };
    }

    public function addField(string $type)
    {
        $field = $this->form->fields()->create([
            'type' => $type,
            'name' => 'new_' . $type . '_' . uniqid(),
            'label' => 'New ' . Str::studly($type),
            'sort_order' => ($this->form->fields()->max('sort_order') ?? 0) + 1,
        ]);

        $this->form->load('fields');
        $this->selectField($field->id);
        $this->showSuccessToast('Field added successfully.');
    }

    public function getPredefinedRulesWithTooltipsProperty(): array
    {
        return [
            'email' => 'The field under validation must be formatted as an e-mail address.',
            'numeric' => 'The field under validation must be numeric.',
            'url' => 'The field under validation must be a valid URL.',
            'string' => 'The field under validation must be a string.',
            'boolean' => 'The field under validation must be able to be cast as a boolean. Accepted input are true, false, 1, 0, "1", and "0".',
            'date' => 'The field under validation must be a valid, non-relative date according to the strtotime PHP function.',
            'image' => 'The file under validation must be an image (jpg, jpeg, png, bmp, gif, svg, or webp).',
            'alpha' => 'The field under validation must be entirely alphabetic characters.',
            'alpha_dash' => 'The field under validation may have alpha-numeric characters, as well as dashes and underscores.',
            'alpha_num' => 'The field under validation must be entirely alpha-numeric characters.',
        ];
    }

    public function updated($name, $value)
    {
        if ($name === 'form.name') {
            $this->form->slug = Str::slug($value);
        }
        if (in_array($name, ['selectedRules', 'min', 'max'])) {
            $this->syncValidationRules();
        }
    }

    public function syncValidationRules()
    {
        $rules = $this->selectedRules;
        if ($this->min) {
            $rules[] = 'min:' . $this->min;
        }
        if ($this->max) {
            $rules[] = 'max:' . $this->max;
        }

        $this->fieldData['validation_rules'] = implode('|', array_unique($rules));
        $this->updatedFieldData($this->fieldData['validation_rules'], 'validation_rules');
    }

    public function updatedFieldData($value, string $key)
    {
        if ($key === 'name') {
            $this->nameManuallyEdited = true;
        }

        if ($key === 'label' && !$this->nameManuallyEdited) {
            $this->fieldData['name'] = Str::snake($value);
        }

        if ($key === 'validation_rules' && is_string($value)) {
            $rules = array_filter(explode('|', $value));
            $this->selectedRules = collect($rules)->filter(fn ($rule) => ! str_starts_with($rule, 'min:') && ! str_starts_with($rule, 'max:'))->values()->all();
            $this->min = str_replace('min:', '', collect($rules)->first(fn ($rule) => str_starts_with($rule, 'min:')));
            $this->max = str_replace('max:', '', collect($rules)->first(fn ($rule) => str_starts_with($rule, 'max:')));
            return;
        }

        if (in_array($key, $this->selectedField->getTranslatableAttributes(), true)) {
            $this->selectedField->setTranslation($key, $this->activeLocale, $value);
        } else {
            $this->selectedField->setAttribute($key, $value);
        }
    }

    public function selectField($fieldId = null)
    {
        if ($fieldId === null) {
            $this->selectedField = null;
            $this->fieldData = [];
            $this->selectedRules = [];
            $this->min = null;
            $this->max = null;
            Flux::modal('edit-field-modal')->close();
            return;
        }

        $this->nameManuallyEdited = false;
        $this->selectedField = $this->form->fields()->find($fieldId);
        $this->fieldData = $this->selectedField->only(['name', 'is_required', 'validation_rules']);
        foreach ($this->selectedField->getTranslatableAttributes() as $key) {
            $this->fieldData[$key] = $this->selectedField->getTranslation($key, $this->activeLocale, false);
        }
        
        $rules = $this->fieldData['validation_rules'] ? array_filter(explode('|', $this->fieldData['validation_rules'])) : [];
        $this->selectedRules = collect($rules)->filter(fn ($rule) => ! str_starts_with($rule, 'min:') && ! str_starts_with($rule, 'max:'))->values()->all();
        $this->min = str_replace('min:', '', collect($rules)->first(fn ($rule) => str_starts_with($rule, 'min:')));
        $this->max = str_replace('max:', '', collect($rules)->first(fn ($rule) => str_starts_with($rule, 'max:')));

        Flux::modal('edit-field-modal')->show();
    }

    public function deselectField()
    {
        $this->selectedField = null;
        $this->fieldData = [];
        $this->selectedRules = [];
        $this->min = null;
        $this->max = null;
    }

    public function confirmDelete(int $fieldId)
    {
        $this->fieldToDeleteId = $fieldId;
        Flux::modal('confirm-delete-modal')->show();
    }

    public function saveField()
    {
        if ($this->selectedField) {
            $this->syncValidationRules();
            $validatedData = $this->validate()['fieldData'];

            foreach ($validatedData as $key => $value) {
                if (in_array($key, $this->selectedField->getTranslatableAttributes(), true)) {
                    $this->selectedField->setTranslation($key, $this->activeLocale, $value);
                } else {
                    $this->selectedField->{$key} = $value;
                }
            }

            $this->selectedField->save();

            $this->dispatch('field-updated', fieldId: $this->selectedField->id);
            $this->showSuccessToast('Field saved successfully.');
            $this->selectField(null);
        }
    }

    public function deleteField()
    {
        if ($this->fieldToDeleteId) {
            $field = $this->form->fields()->find($this->fieldToDeleteId);
            if ($field) {
                $field->delete();
                $this->form->load('fields');
                if ($this->selectedField && $this->selectedField->id === $this->fieldToDeleteId) {
                    $this->selectedField = null;
                }
                $this->showSuccessToast('Field deleted successfully.');
            }
        }
        $this->fieldToDeleteId = null;
        Flux::modal('confirm-delete-modal')->close();
    }

    public function updateFieldOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            FormField::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        $this->form->load('fields');
        $this->showSuccessToast('Field order updated.');
    }

    public function saveForm()
    {
        $this->form->save();
        $this->showSuccessToast('Form settings saved successfully.');
    }

    public function addRepeaterItem(string $key)
    {
        $this->fieldData[$key][] = ['label' => '', 'value' => ''];
    }

    public function removeRepeaterItem(string $key, int $index)
    {
        unset($this->fieldData[$key][$index]);
        $this->fieldData[$key] = array_values($this->fieldData[$key]);
    }

    public function render()
    {
        return view('livewire.forms.form-builder')
            ->layout('components.layouts.app');
    }
} 