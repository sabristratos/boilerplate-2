<?php

namespace App\Livewire;

use App\Enums\FormElementType;
use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
use App\Services\FormBuilder\ElementManager;
use App\Services\FormBuilder\IconService;
use App\Services\FormBuilder\ValidationService;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\FormBuilder\PrebuiltForms\PrebuiltFormRegistry;
use Illuminate\Support\Str;
use App\Services\FormBuilder\PreviewRenderer;

#[Layout('components.layouts.editors')]
class FormBuilder extends Component
{
    use WithConfirmationModal, WithToastNotifications;

    public Form $form;

    public array $elements = [];

    public array $settings = [];

    public ?string $selectedElementId = null;

    public string $activeBreakpoint = 'desktop';

    public string $tab = 'toolbox';

    public string $propertiesTab = 'basic';

    public bool $isPreviewMode = false;

    protected $queryString = [
        'tab' => ['except' => 'toolbox'],
        'propertiesTab' => ['except' => 'basic'],
        'activeBreakpoint' => ['except' => 'desktop'],
    ];

    public array $previewFormData = [];

    private ElementManager $elementManager;

    private ValidationService $validationService;

    private IconService $iconService;

    private ElementFactory $elementFactory;

    private PreviewRenderer $previewRenderer;

    public function boot(
        ElementManager $elementManager,
        ValidationService $validationService,
        IconService $iconService,
        ElementFactory $elementFactory,
        PreviewRenderer $previewRenderer
    ) {
        $this->elementManager = $elementManager;
        $this->validationService = $validationService;
        $this->iconService = $iconService;
        $this->elementFactory = $elementFactory;
        $this->previewRenderer = $previewRenderer;
    }

    public function mount(Form $form)
    {
        $this->form = $form;
        $this->elements = $form->elements ?? [];

        // Ensure all elements have an order field
        foreach ($this->elements as $index => $element) {
            if (! isset($element['order'])) {
                $this->elements[$index]['order'] = $index;
            }
        }

        // Ensure all elements have proper validation structure
        $this->ensureValidationStructure();

        $this->settings = $form->settings ?? config('forms.builder.default_settings');
    }

    /**
     * Ensure all elements have proper validation structure
     */
    private function ensureValidationStructure(): void
    {
        foreach ($this->elements as $index => $element) {
            if (!isset($element['validation'])) {
                $this->elements[$index]['validation'] = config('forms.elements.default_validation');
            } else {
                // Ensure all required validation keys exist
                $defaultValidation = config('forms.elements.default_validation');
                foreach ($defaultValidation as $key => $defaultValue) {
                    if (!isset($this->elements[$index]['validation'][$key])) {
                        $this->elements[$index]['validation'][$key] = $defaultValue;
                    }
                }
            }
        }
    }

    public function addElement(string $type)
    {
        $elementType = FormElementType::tryFrom($type);
        if (! $elementType) {
            return;
        }

        $this->elementManager->addElement($this->elements, $type);
        
        // Ensure the new element has proper validation structure
        $this->ensureValidationStructure();
    }

    #[On('deleteElement')]
    public function deleteElement(string $elementId): void
    {
        $this->elementManager->deleteElement($this->elements, $elementId);
        $this->selectedElementId = null;
        $this->showSuccessToast('Element deleted.');
    }

    public function handleReorder($orderedOrders)
    {
        if (is_array($orderedOrders)) {
            $this->elementManager->reorderElements($this->elements, $orderedOrders);
        }
    }

    public function save()
    {
        $this->form->update([
            'elements' => $this->elements,
            'settings' => $this->settings,
        ]);

        $this->showSuccessToast('Form saved successfully!');
    }

    public function updatedElements($value, $key)
    {
        // Handle validation rule activation based on input values
        if (str_contains($key, 'validation.values.')) {
            $parts = explode('.', $key);
            if (count($parts) >= 4) {
                $elementIndex = $parts[0];
                $ruleKey = $parts[3];
                
                // Ensure the validation structure exists
                if (!isset($this->elements[$elementIndex]['validation'])) {
                    $this->elements[$elementIndex]['validation'] = config('forms.elements.default_validation');
                }
                if (!isset($this->elements[$elementIndex]['validation']['rules'])) {
                    $this->elements[$elementIndex]['validation']['rules'] = [];
                }

                $rules = $this->elements[$elementIndex]['validation']['rules'];
                $inputValue = $this->elements[$elementIndex]['validation']['values'][$ruleKey] ?? '';

                // If input has a value, add the rule; if empty, remove the rule
                if (!empty($inputValue)) {
                    if (!in_array($ruleKey, $rules)) {
                        $rules[] = $ruleKey;
                    }
                } else {
                    // Remove the rule if input is empty
                    $rules = array_values(array_filter($rules, fn($rule) => $rule !== $ruleKey));
                    
                    // Also remove any associated messages
                    if (isset($this->elements[$elementIndex]['validation']['messages'][$ruleKey])) {
                        unset($this->elements[$elementIndex]['validation']['messages'][$ruleKey]);
                    }
                }

                $this->elements[$elementIndex]['validation']['rules'] = $rules;
            }
        }
    }

    public function updateElementWidth(string $elementId, string $breakpoint, string $width): void
    {
        $this->elementManager->updateElementWidth($this->elements, $elementId, $breakpoint, $width);
    }

    public function updateValidationRules(string $elementId, array $rules): void
    {
        $this->validationService->updateValidationRules($this->elements, $elementId, $rules);
    }

    public function updateValidationMessage(string $elementId, string $rule, string $message): void
    {
        $this->validationService->updateValidationMessage($this->elements, $elementId, $rule, $message);
    }

    public function updateValidationRuleValue(string $elementId, string $rule, string $value): void
    {
        $this->validationService->updateValidationRuleValue($this->elements, $elementId, $rule, $value);
    }

    public function toggleValidationRule(string $elementIndex, string $ruleKey): void
    {
        // Ensure the validation structure exists
        if (!isset($this->elements[$elementIndex]['validation'])) {
            $this->elements[$elementIndex]['validation'] = config('forms.elements.default_validation');
        }

        if (!isset($this->elements[$elementIndex]['validation']['rules'])) {
            $this->elements[$elementIndex]['validation']['rules'] = [];
        }

        $rules = $this->elements[$elementIndex]['validation']['rules'];

        // Toggle the rule
        if (in_array($ruleKey, $rules)) {
            // Remove the rule
            $rules = array_values(array_filter($rules, fn($rule) => $rule !== $ruleKey));
            
            // Also remove any associated values and messages
            if (isset($this->elements[$elementIndex]['validation']['values'][$ruleKey])) {
                unset($this->elements[$elementIndex]['validation']['values'][$ruleKey]);
            }
            if (isset($this->elements[$elementIndex]['validation']['messages'][$ruleKey])) {
                unset($this->elements[$elementIndex]['validation']['messages'][$ruleKey]);
            }
        } else {
            // Add the rule
            $rules[] = $ruleKey;
        }

        $this->elements[$elementIndex]['validation']['rules'] = $rules;
    }

    public function getValidationPlaceholder(string $ruleKey): string
    {
        return match ($ruleKey) {
            'min' => 'e.g., 3 (minimum characters)',
            'max' => 'e.g., 50 (maximum characters)',
            'min_value' => 'e.g., 0 (minimum value)',
            'max_value' => 'e.g., 100 (maximum value)',
            'date_after' => 'e.g., 2024-01-01 (date after)',
            'date_before' => 'e.g., 2024-12-31 (date before)',
            'regex' => 'e.g., ^[A-Za-z]+$ (letters only)',
            'mimes' => 'e.g., jpg,png,pdf (file types)',
            'max_file_size' => 'e.g., 2048 (kilobytes)',
            default => 'Enter value...',
        };
    }

    #[Computed]
    public function selectedElement()
    {
        if ($this->selectedElementId === null) {
            return null;
        }

        return $this->elementManager->findElement($this->elements, $this->selectedElementId);
    }

    #[Computed]
    public function selectedElementIndex()
    {
        if ($this->selectedElementId === null) {
            return null;
        }

        return $this->elementManager->findElementIndex($this->elements, $this->selectedElementId);
    }

    #[Computed]
    public function selectedElementOptions(): array
    {
        if (! $this->selectedElement || $this->selectedElement['type'] !== 'select') {
            return [];
        }

        $options = $this->elements[$this->selectedElementIndex()]['properties']['options'] ?? '';

        if (is_array($options)) {
            return $options;
        }

        return array_filter(explode(PHP_EOL, $options));
    }

    #[Computed]
    public function availableValidationRules(): array
    {
        if (!$this->selectedElement) {
            return [];
        }

        return $this->validationService->getRelevantRules($this->selectedElement['type']);
    }

    #[Computed]
    public function availableIcons(): array
    {
        return $this->iconService->getAvailableIcons();
    }

    public function generateValidationRules(array $element): array
    {
        return $this->validationService->generateRules($element);
    }

    public function generateValidationMessages(array $element): array
    {
        return $this->validationService->generateMessages($element);
    }

    #[Computed]
    public function availablePrebuiltForms(): array
    {
        return PrebuiltFormRegistry::all();
    }

    public function loadPrebuiltForm(string $class): void
    {
        $prebuilt = PrebuiltFormRegistry::find($class);
        if ($prebuilt) {
            $elements = $prebuilt->getElements();
            foreach ($elements as $i => &$element) {
                if (!isset($element['id'])) {
                    $element['id'] = (string) Str::uuid();
                }
                $element['order'] = $i;
                
                // Ensure validation structure is properly initialized
                if (!isset($element['validation'])) {
                    $element['validation'] = config('forms.elements.default_validation');
                } else {
                    // Ensure all required validation keys exist
                    $defaultValidation = config('forms.elements.default_validation');
                    foreach ($defaultValidation as $key => $defaultValue) {
                        if (!isset($element['validation'][$key])) {
                            $element['validation'][$key] = $defaultValue;
                        }
                    }
                }
            }
            $this->elements = $elements;
            $this->settings = $prebuilt->getSettings();
            $this->showSuccessToast('Prebuilt form loaded!');
        }
    }

    public function togglePreview(): void
    {
        $this->isPreviewMode = !$this->isPreviewMode;
        
        if ($this->isPreviewMode) {
            $this->initializePreviewFormData();
        }
    }

    private function initializePreviewFormData(): void
    {
        $this->previewFormData = [];
        
        foreach ($this->elements as $element) {
            $fieldName = $this->generateFieldName($element);
            $this->previewFormData[$fieldName] = '';
        }
    }

    private function generateFieldName($element): string
    {
        $label = $element['properties']['label'] ?? 'field';
        // Create a more readable field name based on the label
        $fieldName = Str::slug($label, '_');
        return $fieldName ?: 'field_' . $element['id'];
    }

    public function submitPreview(): void
    {
        // Generate validation rules from form elements
        $rules = $this->generatePreviewValidationRules();
        
        // Create a validator instance to validate the preview form data
        $validator = \Validator::make($this->previewFormData, $rules);
        
        if ($validator->fails()) {
            // Add validation errors to the component
            foreach ($validator->errors()->getMessages() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError("previewFormData.{$field}", $message);
                }
            }
            return;
        }
        
        // Save the form submission to the database
        $this->form->submissions()->create([
            'data' => $this->previewFormData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        // Show success message
        $this->showSuccessToast('Form submitted successfully! (Preview Mode)');
        
        // Reset form data
        $this->initializePreviewFormData();
    }

    private function generatePreviewValidationRules(): array
    {
        $rules = [];
        
        foreach ($this->elements as $element) {
            $fieldName = $this->generateFieldName($element);
            
            // Use the ValidationService to generate proper rules
            $elementRules = $this->validationService->generateRules($element);
            
            if (!empty($elementRules)) {
                $rules[$fieldName] = $elementRules;
            }
        }
        
        return $rules;
    }

    public function render()
    {
        $renderedElements = collect($this->elements)->map(fn ($element) => $this->elementFactory->renderElement($element));
        
        $previewElements = collect($this->elements)->map(function ($element) {
            $fieldName = $this->generateFieldName($element);
            return $this->previewRenderer->renderPreviewElement($element, $fieldName);
        });

        return view('livewire.form-builder', [
            'elementTypes' => FormElementType::cases(),
            'renderedElements' => $renderedElements,
            'previewElements' => $previewElements,
        ]);
    }
}
