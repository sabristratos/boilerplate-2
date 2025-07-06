<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Forms\DiscardFormDraftAction;
use App\Actions\Forms\PublishFormAction;
use App\Actions\Forms\SaveDraftFormAction;
use App\Enums\FormElementType;
use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
use App\Services\FormBuilder\ElementManager;
use App\Services\FormBuilder\IconService;
use App\Services\FormBuilder\PrebuiltForms\PrebuiltFormRegistry;
use App\Services\FormBuilder\ValidationService;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Livewire component for building forms.
 *
 * @property Form $form
 * @property array $elements
 * @property array $draftElements
 * @property array $settings
 * @property array $draftName
 * @property string|null $selectedElementId
 * @property string $activeBreakpoint
 * @property string $tab
 * @property string $propertiesTab
 * @property bool $isPreviewMode
 * @property array $previewFormData
 */
#[Layout('components.layouts.editors')]
class FormBuilder extends Component
{
    use WithConfirmationModal, WithToastNotifications;

    public Form $form;

    public array $elements = [];

    public array $draftElements = [];

    public array $settings = [];

    public array $draftName = [];

    public ?string $selectedElementId = null;

    public string $activeBreakpoint = 'desktop';

    public string $tab = 'toolbox';

    public string $propertiesTab = 'basic';

    public bool $isPreviewMode = false;

    // Add regular properties for selected element data
    public ?array $selectedElement = null;
    public ?int $selectedElementIndex = null;

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

    /**
     * Boot the component with dependencies.
     */
    public function boot(
        ElementManager $elementManager,
        ValidationService $validationService,
        IconService $iconService,
        ElementFactory $elementFactory
    ) {
        $this->elementManager = $elementManager;
        $this->validationService = $validationService;
        $this->iconService = $iconService;
        $this->elementFactory = $elementFactory;
    }

    /**
     * Mount the component with the given form.
     *
     * @return void
     */
    public function mount(Form $form)
    {
        $this->form = $form;
        $this->elements = $form->getCurrentElements();
        $this->draftElements = $form->getCurrentElements();
        $this->settings = $form->getCurrentSettings();
        $this->draftName = $form->getCurrentName();

        // Initialize selected element data
        $this->updateSelectedElementData();

        // Ensure proper structure for all elements
        $this->ensureValidationStructure();
        $this->ensurePropertiesStructure();
    }

    /**
     * Hydrate the component when it's loaded from the database.
     */
    public function hydrate()
    {
        // Ensure properties structure is maintained after hydration
        $this->ensurePropertiesStructure();

        // Ensure draft elements are in sync with saved elements if not already set
        if (empty($this->draftElements) && ! empty($this->elements)) {
            $this->draftElements = $this->elements;
        }
    }

    /**
     * Ensure all elements have proper validation structure.
     */
    private function ensureValidationStructure(): void
    {
        // Ensure saved elements have proper validation structure
        foreach ($this->elements as $index => $element) {
            if (! isset($this->elements[$index]['validation'])) {
                $this->elements[$index]['validation'] = config('forms.elements.default_validation');
            } else {
                // Ensure all required validation keys exist
                $defaultValidation = config('forms.elements.default_validation');
                foreach ($defaultValidation as $key => $defaultValue) {
                    if (! isset($this->elements[$index]['validation'][$key])) {
                        $this->elements[$index]['validation'][$key] = $defaultValue;
                    }
                }
            }
        }

        // Ensure draft elements have proper validation structure
        foreach ($this->draftElements as $index => $element) {
            if (! isset($this->draftElements[$index]['validation'])) {
                $this->draftElements[$index]['validation'] = config('forms.elements.default_validation');
            } else {
                // Ensure all required validation keys exist
                $defaultValidation = config('forms.elements.default_validation');
                foreach ($defaultValidation as $key => $defaultValue) {
                    if (! isset($this->draftElements[$index]['validation'][$key])) {
                        $this->draftElements[$index]['validation'][$key] = $defaultValue;
                    }
                }
            }
        }
    }

    /**
     * Ensure all elements have proper properties structure.
     */
    private function ensurePropertiesStructure(): void
    {
        // Ensure saved elements have proper properties structure
        foreach ($this->elements as $index => $element) {
            if (! isset($this->elements[$index]['properties'])) {
                $this->elements[$index]['properties'] = [];
            }

            // Get the renderer for this element type to ensure proper default properties
            $renderer = $this->elementFactory->getRenderer($element['type']);
            if ($renderer) {
                $defaultProperties = $renderer->getDefaultProperties();
                foreach ($defaultProperties as $key => $defaultValue) {
                    if (! isset($this->elements[$index]['properties'][$key])) {
                        $this->elements[$index]['properties'][$key] = $defaultValue;
                    }
                }
            }

            // Special handling for date elements to ensure locale is always valid
            if ($element['type'] === 'date') {
                if (empty($this->elements[$index]['properties']['locale'])) {
                    $this->elements[$index]['properties']['locale'] = 'en';
                }
            }
        }

        // Ensure draft elements have proper properties structure
        foreach ($this->draftElements as $index => $element) {
            if (! isset($this->draftElements[$index]['properties'])) {
                $this->draftElements[$index]['properties'] = [];
            }

            // Get the renderer for this element type to ensure proper default properties
            $renderer = $this->elementFactory->getRenderer($element['type']);
            if ($renderer) {
                $defaultProperties = $renderer->getDefaultProperties();
                foreach ($defaultProperties as $key => $defaultValue) {
                    if (! isset($this->draftElements[$index]['properties'][$key])) {
                        $this->draftElements[$index]['properties'][$key] = $defaultValue;
                    }
                }
            }

            // Special handling for date elements to ensure locale is always valid
            if ($element['type'] === 'date') {
                if (empty($this->draftElements[$index]['properties']['locale'])) {
                    $this->draftElements[$index]['properties']['locale'] = 'en';
                }
            }
        }
    }

    /**
     * Add a new element of the given type.
     *
     * @return void
     */
    public function addElement(string $type)
    {
        // Add to both saved and draft elements
        $this->elementManager->addElement($this->elements, $type);
        $this->elementManager->addElement($this->draftElements, $type);

        // Select the newly added element (last element)
        $lastElement = end($this->draftElements);
        $this->selectElement($lastElement['id']);

        // Ensure properties structure is maintained
        $this->ensurePropertiesStructure();
    }

    /**
     * Delete an element by its ID.
     */
    #[On('deleteElement')]
    public function deleteElement(string $elementId): void
    {
        // Remove from both saved and draft elements
        $this->elementManager->deleteElement($this->elements, $elementId);
        $this->elementManager->deleteElement($this->draftElements, $elementId);

        // Clear selection if the deleted element was selected
        if ($this->selectedElementId === $elementId) {
            $this->selectedElementId = null;
        }

        // Update selected element data
        $this->updateSelectedElementData();
    }

    #[On('options-updated')]
    public function handleOptionsUpdated(array $data): void
    {
        $elementIndex = $data['elementIndex'];
        $propertyPath = $data['propertyPath'];
        $optionsString = $data['optionsString'];

        // Update the options string in the element
        data_set($this->elements, "{$elementIndex}.properties.{$propertyPath}", $optionsString);
        data_set($this->draftElements, "{$elementIndex}.properties.{$propertyPath}", $optionsString);
        
        // Refresh the preview and edit elements
        if (isset($elementIndex)) {
            $this->refreshPreviewElement($elementIndex);
            $this->refreshEditElement($elementIndex);
        }
    }

    #[On('debounced-options-update')]
    public function handleDebouncedOptionsUpdate(array $data): void
    {
        // This method can be used for additional processing if needed
        // For now, we'll just log the debounced update
        logger()->debug('Debounced options update', $data);
    }

    public function handleReorder($orderedOrders)
    {
        if (is_array($orderedOrders)) {
            $this->elementManager->reorderElements($this->elements, $orderedOrders);
            $this->elementManager->reorderElements($this->draftElements, $orderedOrders);
        }
    }

    public function save()
    {
        // Save draft data using the SaveDraftFormAction
        $saveDraftAction = app(SaveDraftFormAction::class);
        $this->saveDraft($saveDraftAction);
    }

    public function updatedDraftName($value, $key)
    {
        // Handle draft name updates
        $this->dispatch('draft-name-updated', key: $key, value: $value);
    }

    public function updatedDraftElements($value, $key)
    {
        // Parse the key once to avoid multiple explode() calls
        $parts = explode('.', $key);
        $elementIndex = null;
        
        if (count($parts) >= 1 && is_numeric($parts[0])) {
            $elementIndex = (int) $parts[0];
        }

        // Handle locale property updates to ensure it's never empty
        if (str_contains($key, 'properties.locale') && $elementIndex !== null) {
            if (empty($value)) {
                $this->draftElements[$elementIndex]['properties']['locale'] = 'en';
            }
        }

        // Handle validation rule activation based on input values
        if (str_contains($key, 'validation.values.') && count($parts) >= 4) {
            $ruleKey = $parts[3];

            // Ensure the validation structure exists
            if (! isset($this->draftElements[$elementIndex]['validation'])) {
                $this->draftElements[$elementIndex]['validation'] = config('forms.elements.default_validation');
            }
            if (! isset($this->draftElements[$elementIndex]['validation']['rules'])) {
                $this->draftElements[$elementIndex]['validation']['rules'] = [];
            }

            $rules = $this->draftElements[$elementIndex]['validation']['rules'];
            $inputValue = $this->draftElements[$elementIndex]['validation']['values'][$ruleKey] ?? '';

            // If input has a value, add the rule; if empty, remove the rule
            if (! empty($inputValue)) {
                if (! in_array($ruleKey, $rules)) {
                    $rules[] = $ruleKey;
                }
            } else {
                // Remove the rule if input is empty
                $rules = array_values(array_filter($rules, fn ($rule) => $rule !== $ruleKey));

                // Also remove any associated messages
                if (isset($this->draftElements[$elementIndex]['validation']['messages'][$ruleKey])) {
                    unset($this->draftElements[$elementIndex]['validation']['messages'][$ruleKey]);
                }
            }

            $this->draftElements[$elementIndex]['validation']['rules'] = $rules;
        }

        // Refresh preview and edit elements if we have a valid element index
        if ($elementIndex !== null) {
            $this->refreshPreviewElement($elementIndex);
            $this->refreshEditElement($elementIndex);
        }

        // Ensure properties structure is maintained after updates
        $this->ensurePropertiesStructure();

        // Dispatch event for real-time updates with more detailed information
        $this->dispatch('element-updated', [
            'key' => $key,
            'value' => $value,
            'elementIndex' => $elementIndex,
            'elementId' => $elementIndex !== null ? ($this->draftElements[$elementIndex]['id'] ?? null) : null,
            'timestamp' => now()->timestamp
        ]);
    }

    // Backward compatibility method
    public function updatedElements($value, $key)
    {
        $this->updatedDraftElements($value, $key);
    }

    /**
     * Select an element by its ID.
     */
    public function selectElement(string $elementId): void
    {
        logger()->debug('selectElement called', [
            'elementId' => $elementId,
            'currentSelectedElementId' => $this->selectedElementId,
            'draftElementsCount' => count($this->draftElements)
        ]);
        
        $this->selectedElementId = $elementId;
        $this->updateSelectedElementData();
    }

    private function updateSelectedElementData()
    {
        logger()->debug('updateSelectedElementData called', [
            'selectedElementId' => $this->selectedElementId,
            'draftElementsCount' => count($this->draftElements),
            'draftElementIds' => collect($this->draftElements)->pluck('id')->toArray()
        ]);
        
        if ($this->selectedElementId === null) {
            $this->selectedElement = null;
            $this->selectedElementIndex = null;
            logger()->debug('Selected element cleared');
            return;
        }

        $this->selectedElement = $this->elementManager->findElement($this->draftElements, $this->selectedElementId);
        $this->selectedElementIndex = $this->elementManager->findElementIndex($this->draftElements, $this->selectedElementId);
        
        // Debug logging
        logger()->debug('Selected element data updated', [
            'selectedElementId' => $this->selectedElementId,
            'selectedElement' => $this->selectedElement ? 'found' : 'not found',
            'selectedElementIndex' => $this->selectedElementIndex,
        ]);
    }

    public function refreshPreviewElement($elementIndex)
    {
        if (isset($this->draftElements[$elementIndex])) {
            $element = $this->draftElements[$elementIndex];
            $fieldName = $this->generateFieldName($element);
            $previewHtml = $this->elementFactory->renderElement($element, 'preview', $fieldName);

            $this->dispatch('preview-element-updated', [
                'elementIndex' => $elementIndex,
                'elementId' => $element['id'],
                'html' => $previewHtml,
            ]);
        }
    }

    public function refreshEditElement($elementIndex)
    {
        if (isset($this->draftElements[$elementIndex])) {
            $element = $this->draftElements[$elementIndex];
            $fieldName = $this->generateFieldName($element);
            $editHtml = $this->elementFactory->renderElement($element, 'edit');

            $this->dispatch('edit-element-updated', [
                'elementIndex' => $elementIndex,
                'elementId' => $element['id'],
                'html' => $editHtml,
            ]);
        }
    }

    public function updateElementWidth(string $elementId, string $breakpoint, string $width): void
    {
        $this->elementManager->updateElementWidth($this->draftElements, $elementId, $breakpoint, $width);
    }

    public function updateValidationRules(string $elementId, array $rules): void
    {
        $this->validationService->updateValidationRules($this->draftElements, $elementId, $rules);
    }

    public function updateValidationMessage(string $elementId, string $rule, string $message): void
    {
        $this->validationService->updateValidationMessage($this->draftElements, $elementId, $rule, $message);
    }

    public function updateValidationRuleValue(string $elementId, string $rule, string $value): void
    {
        $this->validationService->updateValidationRuleValue($this->draftElements, $elementId, $rule, $value);
    }

    public function toggleValidationRule(string $elementIndex, string $ruleKey): void
    {
        // Ensure the validation structure exists
        if (! isset($this->draftElements[$elementIndex]['validation'])) {
            $this->draftElements[$elementIndex]['validation'] = config('forms.elements.default_validation');
        }

        if (! isset($this->draftElements[$elementIndex]['validation']['rules'])) {
            $this->draftElements[$elementIndex]['validation']['rules'] = [];
        }

        $rules = $this->draftElements[$elementIndex]['validation']['rules'];

        // Toggle the rule
        if (in_array($ruleKey, $rules)) {
            // Remove the rule
            $rules = array_values(array_filter($rules, fn ($rule) => $rule !== $ruleKey));

            // Also remove any associated values and messages
            if (isset($this->draftElements[$elementIndex]['validation']['values'][$ruleKey])) {
                unset($this->draftElements[$elementIndex]['validation']['values'][$ruleKey]);
            }
            if (isset($this->draftElements[$elementIndex]['validation']['messages'][$ruleKey])) {
                unset($this->draftElements[$elementIndex]['validation']['messages'][$ruleKey]);
            }
        } else {
            // Add the rule
            $rules[] = $ruleKey;
        }

        $this->draftElements[$elementIndex]['validation']['rules'] = $rules;
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
    public function selectedElementSaved()
    {
        if ($this->selectedElementId === null) {
            return null;
        }

        return $this->elementManager->findElement($this->elements, $this->selectedElementId);
    }

    #[Computed]
    public function selectedElementIndexSaved()
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

        $options = $this->draftElements[$this->selectedElementIndex]['properties']['options'] ?? '';

        if (is_array($options)) {
            return $options;
        }

        // Parse options from string format
        return $this->elementManager->parseOptions($options);
    }

    public function getElementOptionsArray(string $elementIndex): array
    {
        $element = $this->elements[$elementIndex] ?? null;
        if (! $element) {
            return [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }

        $options = $element['properties']['options'] ?? '';

        // If options is already an array, return it directly
        if (is_array($options)) {
            return $options;
        }

        // If options is a string, parse it using the OptionParserService
        $optionParser = app(\App\Services\FormBuilder\OptionParserService::class);

        return $optionParser->parseOptions($options);
    }

    public function parseOptionsForPreview($options): array
    {
        // If options is already an array, return it directly
        if (is_array($options)) {
            return $options;
        }

        // If options is a string, parse it using the OptionParserService
        $optionParser = app(\App\Services\FormBuilder\OptionParserService::class);

        return $optionParser->parseOptionsForPreview($options);
    }

    #[Computed]
    public function availableValidationRules(): array
    {
        if (! $this->selectedElement) {
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
            $this->elements = [];
            $this->draftElements = [];

            foreach ($elements as $i => $element) {
                if (! isset($element['id'])) {
                    $element['id'] = (string) Str::uuid();
                }
                $element['order'] = $i;

                // Ensure validation structure is properly initialized
                if (! isset($element['validation'])) {
                    $element['validation'] = config('forms.elements.default_validation');
                } else {
                    // Ensure all required validation keys exist
                    $defaultValidation = config('forms.elements.default_validation');
                    foreach ($defaultValidation as $key => $defaultValue) {
                        if (! isset($element['validation'][$key])) {
                            $element['validation'][$key] = $defaultValue;
                        }
                    }
                }

                $this->elements[] = $element;
                $this->draftElements[] = $element;
            }
            $this->settings = $prebuilt->getSettings();
            $this->showSuccessToast('Prebuilt form loaded!');
        }
    }

    public function togglePreview(): void
    {
        $this->isPreviewMode = ! $this->isPreviewMode;

        if ($this->isPreviewMode) {
            $this->initializePreviewFormData();
        }
    }

    private function initializePreviewFormData(): void
    {
        $this->previewFormData = [];

        foreach ($this->draftElements as $element) {
            $fieldName = $this->generateFieldName($element);
            $this->previewFormData[$fieldName] = '';
        }
    }

    private function generateFieldName(array $element): string
    {
        $fieldNameGenerator = app(\App\Services\FormBuilder\FieldNameGeneratorService::class);

        return $fieldNameGenerator->generateFieldName($element);
    }

    public function submitPreview(): void
    {
        $errorHandler = app(\App\Services\FormBuilder\FormSubmissionErrorHandler::class);
        $result = $errorHandler->handleSubmission($this->form, $this->previewFormData);

        if ($result['success']) {
            $this->showSuccessToast('Form submitted successfully! (Preview Mode)');
            $this->initializePreviewFormData();
        } else {
            // Handle validation errors
            if (! empty($result['errors'])) {
                foreach ($result['errors'] as $field => $messages) {
                    foreach ($messages as $message) {
                        $this->addError("previewFormData.{$field}", $message);
                    }
                }
            }

            // Show error message
            $this->addError('previewFormData.general', $result['message']);
        }
    }

    #[Computed]
    public function hasUnsavedChanges(): bool
    {
        return $this->elements !== $this->draftElements ||
               $this->settings !== $this->form->getCurrentSettings() ||
               $this->draftName !== $this->form->getCurrentName();
    }

    /**
     * Save the current draft data.
     */
    public function saveDraft(): void
    {
        $saveDraftAction = app(SaveDraftFormAction::class);
        $saveDraftAction->execute(
            $this->form,
            $this->draftElements,
            $this->settings,
            $this->draftName,
            app()->getLocale()
        );

        $this->showSuccessToast('Draft saved successfully!');
    }

    /**
     * Publish the draft changes.
     */
    public function publishDraft(): void
    {
        $publishAction = app(PublishFormAction::class);
        $publishAction->execute($this->form);

        // Refresh the component data after publishing
        $this->elements = $this->form->getCurrentElements();
        $this->draftElements = $this->form->getCurrentElements();
        $this->settings = $this->form->getCurrentSettings();
        $this->draftName = $this->form->getCurrentName();

        $this->showSuccessToast('Form published successfully!');
    }

    /**
     * Confirm discarding the draft.
     */
    public function confirmDiscardDraft(): void
    {
        $this->confirmAction(
            'Discard Draft Changes',
            'Are you sure you want to discard all draft changes? This action cannot be undone.',
            'discardDraft'
        );
    }

    /**
     * Discard the draft changes.
     */
    #[On('discardDraft')]
    public function discardDraft(): void
    {
        $discardAction = app(DiscardFormDraftAction::class);
        $discardAction->execute($this->form);

        // Refresh the component data after discarding
        $this->elements = $this->form->getCurrentElements();
        $this->draftElements = $this->form->getCurrentElements();
        $this->settings = $this->form->getCurrentSettings();
        $this->draftName = $this->form->getCurrentName();

        $this->showSuccessToast('Draft discarded successfully!');
    }

    /**
     * Check if the form has draft changes.
     */
    #[Computed]
    public function hasDraftChanges(): bool
    {
        return $this->form->hasDraftChanges();
    }

    public function render()
    {
        $renderedElements = collect($this->draftElements)->map(fn ($element) => $this->elementFactory->renderElement($element));

        return view('livewire.form-builder', [
            'elementTypes' => FormElementType::cases(),
            'renderedElements' => $renderedElements,
            'availablePrebuiltForms' => $this->availablePrebuiltForms,
        ]);
    }
}
