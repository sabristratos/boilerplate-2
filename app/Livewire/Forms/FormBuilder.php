<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Forms\FieldTypeManager;
use App\Services\FormService;
use App\Models\Form;
use App\Models\FormField;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Flux\Flux;
use Illuminate\Support\Str;
use Exception;

class FormBuilder extends Component
{
    use WithToastNotifications;

    public Form $form;

    public array $formState = [];

    public ?FormField $selectedField = null;

    public ?int $fieldToDeleteId = null;

    public array $fieldData = [];

    public array $selectedRules = [];

    public string $activeLocale;

    public string $activeTab = 'fields';

    public bool $nameManuallyEdited = false;

    public array $fieldComponentOptions = [];

    public string $activeFieldTab = 'general';

    public string $breakpoint = 'desktop';

    protected FieldTypeManager $fieldTypeManager;
    protected FormService $formService;

    public function boot(FieldTypeManager $fieldTypeManager, FormService $formService)
    {
        $this->fieldTypeManager = $fieldTypeManager;
        $this->formService = $formService;
    }

    protected function rules(): array
    {
        $locale = $this->activeLocale;

        return [
            "formState.name.{$locale}" => 'required|string|max:255',
            "formState.title.{$locale}" => 'required|string|max:255',
            "formState.description.{$locale}" => 'nullable|string',
            "formState.success_message.{$locale}" => 'nullable|string',
            'formState.is_active' => 'boolean',
            'formState.has_captcha' => 'boolean',
            'formState.send_notification' => 'boolean',
            'formState.recipient_email' => 'nullable|email',
            'formState.submit_button_options' => 'nullable|array',
            'fieldData.name' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'fieldData.label' => 'required|string|max:255',
            'fieldData.placeholder' => 'nullable|string|max:255',
            'fieldData.validation_rules' => 'nullable|string',
            'fieldData.options' => 'nullable|array',
            'fieldData.options.*.label' => 'required|string|max:255',
            'fieldData.options.*.value' => 'required|string|max:255',
        ];
    }

    public function mount(Form $form)
    {
        $this->form = $form;
        $this->formState = $form->toArray();

        foreach ($form->getTranslatableAttributes() as $attribute) {
            $this->formState[$attribute] = $form->getTranslations($attribute);
        }

        if (empty($this->formState['submit_button_options']['align'])) {
            data_set($this->formState, 'submit_button_options.align', [
                'desktop' => 'left',
                'tablet' => 'left',
                'mobile' => 'left',
            ]);
        }

        $this->form->load('fields');
        $this->activeLocale = app()->getLocale();
    }

    public function getAvailableLocalesProperty(): array
    {
        return config('app.available_locales', []);
    }

    public function switchLocale(string $locale)
    {
        $this->activeLocale = $locale;
        if ($this->selectedField) {
            $this->selectField($this->selectedField->id);
        }
    }

    public function getPredefinedRulesProperty(): array
    {
        return [
            'required', 'email', 'numeric', 'url', 'string', 'boolean', 'date', 'image', 'alpha', 'alpha_dash', 'alpha_num'
        ];
    }

    public function getFieldTypesProperty()
    {
        return $this->fieldTypeManager->all();
    }

    public function getPreviewComponent(FormField $field): string
    {
        return $this->fieldTypeManager->find($field->type->value)->getPreviewComponent();
    }

    public function addField(string $type)
    {
        $fieldType = $this->fieldTypeManager->find($type);

        $field = $this->formService->createField($this->form, [
            'type' => $type,
            'label' => 'New ' . $fieldType->getLabel(),
        ]);

        $this->form->load('fields');
        $this->selectField($field->id);
        $this->showSuccessToast('Field added successfully.');
    }

    public function getPredefinedRulesWithTooltipsProperty(): array
    {
        return [
            'required' => 'This field must be filled out.',
            'min:3' => 'The field must have a minimum length of 3 characters.',
            'max:255' => 'The field must have a maximum length of 255 characters.',
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
        if (str_starts_with($name, 'formState.name.')) {
            $this->formState['slug'] = Str::slug($value);
        }
    }

    public function updatedSelectedRules()
    {
        $this->syncValidationRules();
    }

    public function syncValidationRules()
    {
        $this->fieldData['validation_rules'] = implode('|', array_unique($this->selectedRules));
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
            $this->selectedRules = array_filter(explode('|', $value));
            return;
        }

        if (str_contains($key, '.')) {
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
            $this->fieldComponentOptions = [];
            $this->activeFieldTab = 'general';
            $this->selectedRules = [];
            Flux::modal('edit-field-modal')->close();
            return;
        }

        $this->nameManuallyEdited = false;
        $this->activeFieldTab = 'general';
        $this->selectedField = $this->form->fields()->with('options')->find($fieldId);
        $this->fieldData = $this->selectedField->only(['name', 'validation_rules']);
        $this->fieldComponentOptions = $this->selectedField->component_options ?? [];
        $this->fieldData['layout_options'] = $this->selectedField->layout_options ?? [
            'desktop' => 'full',
            'tablet' => 'full',
            'mobile' => 'full',
        ];
        foreach ($this->selectedField->getTranslatableAttributes() as $key) {
            $this->fieldData[$key] = $this->selectedField->getTranslation($key, $this->activeLocale, false);
        }
        $this->fieldData['options'] = $this->selectedField->options->map(function ($option) {
            return [
                'id' => $option->id,
                'label' => $option->getTranslation('label', $this->activeLocale, false),
                'value' => $option->value,
            ];
        })->toArray();

        $this->selectedRules = $this->fieldData['validation_rules'] ? array_filter(explode('|', $this->fieldData['validation_rules'])) : [];

        Flux::modal('edit-field-modal')->show();
    }

    public function deselectField()
    {
        $this->selectedField = null;
        $this->fieldData = [];
        $this->selectedRules = [];
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

            $this->formService->updateField($this->selectedField, $validatedData, $this->activeLocale);
            $this->showSuccessToast('Field saved successfully.');
            $this->selectField(null);
        }
    }

    public function deleteField()
    {
        if ($this->fieldToDeleteId) {
            $field = $this->form->fields()->find($this->fieldToDeleteId);
            if ($field) {
                $this->formService->deleteField($field);
                $this->form->load('fields');
                if ($this->selectedField && $this->selectedField->id === $this->fieldToDeleteId) {
                    $this->selectedField = null;
                    Flux::modal('edit-field-modal')->close();
                }
                $this->showSuccessToast('Field deleted successfully.');
            }
        }
        $this->fieldToDeleteId = null;
        Flux::modal('confirm-delete-modal')->close();
    }

    public function getHasOptionsProperty(): bool
    {
        if (!$this->selectedField) {
            return false;
        }

        return in_array($this->selectedField->type->value, ['select', 'radio', 'checkbox-group']);
    }

    public function addOption()
    {
        $this->fieldData['options'][] = [
            'id' => null,
            'label' => '',
            'value' => '',
        ];
    }

    public function removeOption(int $index)
    {
        if (isset($this->fieldData['options'][$index]['id'])) {
            $optionId = $this->fieldData['options'][$index]['id'];
            $this->selectedField->options()->find($optionId)?->delete();
        }
        unset($this->fieldData['options'][$index]);
        $this->fieldData['options'] = array_values($this->fieldData['options']);
    }

    public function updateFieldOrder(array $orderedIds): void
    {
        try {
            foreach ($orderedIds as $index => $id) {
                FormField::where('id', (int)$id)->update(['sort_order' => $index + 1]);
            }
            $this->form->load('fields');

            $this->showSuccessToast('Field order updated successfully.');
        } catch (Exception $e) {
            logger()->error('Error updating field order: ' . $e->getMessage());
            $this->showErrorToast('Error updating field order.');
        }
    }

    public function saveForm()
    {
        $this->validate([
            "formState.name.{$this->activeLocale}" => 'required|string|max:255',
            "formState.title.{$this->activeLocale}" => 'required|string|max:255',
            "formState.description.{$this->activeLocale}" => 'nullable|string',
            "formState.success_message.{$this->activeLocale}" => 'nullable|string',
            'formState.recipient_email' => 'nullable|email',
            'formState.submit_button_options' => 'nullable|array',
            'formState.has_captcha' => 'boolean',
            'formState.send_notification' => 'boolean',
        ]);

        $this->form->update($this->formState);
        $this->showSuccessToast('Form saved successfully.');
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
