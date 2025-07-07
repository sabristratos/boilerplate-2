<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Forms\DiscardFormDraftAction;
use App\Actions\Forms\PublishFormAction;
use App\Actions\Forms\SaveDraftFormAction;
use App\DTOs\FormDTO;
use App\DTOs\DTOFactory;
use App\Enums\FormElementType;
use App\Models\Form;
use App\Services\FormBuilder\ElementFactory;
use App\Services\FormBuilder\ElementManager;
use App\Services\FormBuilder\FieldNameGeneratorService;
use App\Services\FormBuilder\IconService;
use App\Services\FormBuilder\OptionParserService;
use App\Services\FormBuilder\ValidationService;
use App\Services\FormService;
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
 * @property array $settings
 * @property array $name
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

    public array $settings = [];
    
    public array $name = [];

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

    private array $cachedOptions = [];

    private ElementManager $elementManager;

    private ValidationService $validationService;

    private IconService $iconService;

    private ElementFactory $elementFactory;

    private OptionParserService $optionParser;

    private FieldNameGeneratorService $fieldNameGenerator;
    private FormService $formService;

    /**
     * Boot the component with dependencies.
     *
     * @param ElementManager $elementManager
     * @param ValidationService $validationService
     * @param IconService $iconService
     * @param ElementFactory $elementFactory
     * @param OptionParserService $optionParser
     * @param FieldNameGeneratorService $fieldNameGenerator
     * @param FormService $formService
     * @return void
     */
    public function boot(
        ElementManager $elementManager,
        ValidationService $validationService,
        IconService $iconService,
        ElementFactory $elementFactory,
        OptionParserService $optionParser,
        FieldNameGeneratorService $fieldNameGenerator,
        FormService $formService
    ) {
        $this->elementManager = $elementManager;
        $this->validationService = $validationService;
        $this->iconService = $iconService;
        $this->elementFactory = $elementFactory;
        $this->optionParser = $optionParser;
        $this->fieldNameGenerator = $fieldNameGenerator;
        $this->formService = $formService;
    }

    /**
     * Mount the component with the given form ID.
     *
     * @param int $id
     * @return void
     */
    public function mount(int $id)
    {
        $this->form = Form::findOrFail($id);
        $latestRevision = $this->form->latestRevision();

        if ($latestRevision) {
            $data = $latestRevision->data;
            $this->elements = $data['elements'] ?? [];
            $this->settings = $data['settings'] ?? [];
            $this->name = $data['name'] ?? [];
        } else {
            $this->elements = $this->form->elements ?? [];
            $this->settings = $this->form->settings ?? [];
            $this->name = $this->form->getTranslations('name');
        }

        // Validate form structure
        $this->validateFormStructure();

        // Initialize selected element data
        $this->updateSelectedElementData();

        // Ensure proper structure for all elements
        $this->ensureValidationStructure();
        $this->ensurePropertiesStructure();
    }

    /**
     * Hydrate the component when it's loaded from the database.
     *
     * @return void
     */
    public function hydrate()
    {
        try {
            // Ensure preview form data is properly synchronized
            $this->synchronizePreviewFormData();
            
            // Ensure validation structure is maintained
            $this->ensureValidationStructure();
            
            // Ensure properties structure is maintained
            $this->ensurePropertiesStructure();
        } catch (\Exception $e) {
            logger()->error('Error during FormBuilder hydration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Reset to a safe state
            $this->resetToSafeState();
        }
    }

    /**
     * Clean up component state before serialization.
     *
     * @return void
     */
    public function dehydrate()
    {
        try {
            // Ensure all arrays are properly initialized to prevent serialization issues
            if (!is_array($this->elements)) {
                $this->elements = [];
            }
            
            if (!is_array($this->settings)) {
                $this->settings = config('forms.builder.default_settings');
            }
            
            if (!is_array($this->name)) {
                $this->name = [];
            }
            
            if (!is_array($this->previewFormData)) {
                $this->previewFormData = [];
            }
            
            // Clear any temporary data that shouldn't be persisted
            $this->clearOptionsCache();
            
        } catch (\Exception $e) {
            logger()->error('Error during FormBuilder dehydration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Reset the component to a safe state when errors occur.
     */
    private function resetToSafeState(): void
    {
        // Ensure basic arrays are initialized
        if (!is_array($this->elements)) {
            $this->elements = [];
        }
        
        if (!is_array($this->settings)) {
            $this->settings = config('forms.builder.default_settings');
        }
        
        if (!is_array($this->name)) {
            $this->name = [];
        }
        
        if (!is_array($this->previewFormData)) {
            $this->previewFormData = [];
        }
        
        // Clear selection if it's invalid
        if ($this->selectedElementId && !$this->elementManager->findElement($this->elements, $this->selectedElementId)) {
            $this->selectedElementId = null;
            $this->selectedElement = null;
            $this->selectedElementIndex = null;
        }
    }

    /**
     * Ensure all elements have proper validation structure.
     */
    private function ensureValidationStructure(): void
    {
        // Ensure all elements have proper validation structure
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
    }

    /**
     * Ensure all elements have proper properties structure.
     */
    private function ensurePropertiesStructure(): void
    {
        // Ensure all elements have proper properties structure
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
    }

    /**
     * Add a new element of the given type.
     *
     * @param string $type
     * @return void
     */
    public function addElement(string $type): void
    {
        // Validate element type
        if (! $this->elementFactory->getRenderer($type)) {
            $this->handleValidationError(__('forms.errors.invalid_element_type', ['type' => $type]), 'element_type');
            return;
        }

        try {
            // Add to elements array
            $this->elementManager->addElement($this->elements, $type);

            // Select the newly added element (last element)
            $lastElement = end($this->elements);
            $this->selectElement($lastElement['id']);

            // Ensure properties structure is maintained
            $this->ensurePropertiesStructure();
            
            // Initialize preview form data for the new element (skip submit button)
            if ($type !== 'submit_button') {
                $fieldName = $this->generateFieldName($lastElement);
                if (in_array($type, ['checkbox', 'radio'])) {
                    $this->previewFormData[$fieldName] = [];
                } else {
                    $this->previewFormData[$fieldName] = '';
                }
            }
        } catch (\Exception $e) {
            $this->handleValidationError($e->getMessage(), 'element_creation');
        }
    }

    /**
     * Delete an element by its ID.
     *
     * @param string $elementId
     * @return void
     */
    #[On('deleteElement')]
    public function deleteElement(string $elementId): void
    {
        // Find the element before deletion to get its field name
        $element = $this->elementManager->findElement($this->elements, $elementId);
        $fieldName = $element ? $this->generateFieldName($element) : null;
        
        // Remove from elements array
        $this->elementManager->deleteElement($this->elements, $elementId);

        // Clear selection if the deleted element was selected
        if ($this->selectedElementId === $elementId) {
            $this->selectedElementId = null;
        }

        // Clean up preview form data for the deleted element
        if ($fieldName && isset($this->previewFormData[$fieldName])) {
            unset($this->previewFormData[$fieldName]);
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

    public function handleReorder(array $orderedOrders): void
    {
        if (is_array($orderedOrders)) {
            $this->elementManager->reorderElements($this->elements, $orderedOrders);
        }
    }

    public function save(): void
    {
        try {
            // Create a DTO for the form data
            $formDto = DTOFactory::createFormDTOForCreation(
                $this->name,
                $this->elements,
                $this->settings,
                $this->form->user_id
            );

            // Validate the DTO
            if (!$formDto->isValid()) {
                $this->handleValidationError($formDto->getValidationErrorsAsString(), 'form');
                return;
            }

            // Update the form using the service
            $this->formService->updateForm($this->form, $formDto);

            // Create a revision for this save action
            $this->form->createRevision(
                'update',
                'Form saved manually',
                [],
                true // Published
            );

            $this->showSuccessToast(__('forms.toast_form_saved'));

        } catch (\Exception $e) {
            logger()->error('Error saving form', [
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->handleValidationError(__('forms.errors.failed_to_save_form'), 'form');
        }
    }

    public function updatedname($value, $key)
    {
        // Handle draft name updates
        $this->dispatch('draft-name-updated', key: $key, value: $value);
    }

    public function updatedelements($value, $key)
    {
        try {
            // Clear options cache when elements are updated
            $this->clearOptionsCache();

            // Parse the key once to avoid multiple explode() calls
            $parts = explode('.', $key);
            $elementIndex = null;
            
            if (count($parts) >= 1 && is_numeric($parts[0])) {
                $elementIndex = (int) $parts[0];
            }

            // Validate element index exists
            if ($elementIndex !== null && !isset($this->elements[$elementIndex])) {
                logger()->warning('Element index not found during update', [
                    'elementIndex' => $elementIndex,
                    'key' => $key,
                    'elementsCount' => count($this->elements)
                ]);
                return;
            }

            // Handle locale property updates to ensure it's never empty
            if (str_contains($key, 'properties.locale') && $elementIndex !== null) {
                if (empty($value)) {
                    $this->elements[$elementIndex]['properties']['locale'] = 'en';
                }
            }

            // Handle checkbox and radio field preview data initialization
            if ($elementIndex !== null && isset($this->elements[$elementIndex])) {
                $element = $this->elements[$elementIndex];
                $fieldName = $this->generateFieldName($element);
                
                // Ensure checkbox and radio fields have array values in preview data
                if (in_array($element['type'], ['checkbox', 'radio'])) {
                    if (!isset($this->previewFormData[$fieldName]) || !is_array($this->previewFormData[$fieldName])) {
                        $this->previewFormData[$fieldName] = [];
                    }
                }
            }

            // Handle validation rule activation based on input values
            if (str_contains($key, 'validation.values.') && count($parts) >= 4) {
                $ruleKey = $parts[3];

                // Ensure the validation structure exists
                if (! isset($this->elements[$elementIndex]['validation'])) {
                    $this->elements[$elementIndex]['validation'] = config('forms.elements.default_validation');
                }
                if (! isset($this->elements[$elementIndex]['validation']['rules'])) {
                    $this->elements[$elementIndex]['validation']['rules'] = [];
                }

                $rules = $this->elements[$elementIndex]['validation']['rules'];
                $inputValue = $this->elements[$elementIndex]['validation']['values'][$ruleKey] ?? '';

                // If input has a value, add the rule; if empty, remove the rule
                if (! empty($inputValue)) {
                    if (! in_array($ruleKey, $rules)) {
                        $rules[] = $ruleKey;
                    }
                } else {
                    // Remove the rule if input is empty
                    $rules = array_values(array_filter($rules, fn ($rule) => $rule !== $ruleKey));

                    // Also remove any associated messages
                    if (isset($this->elements[$elementIndex]['validation']['messages'][$ruleKey])) {
                        unset($this->elements[$elementIndex]['validation']['messages'][$ruleKey]);
                    }
                }

                $this->elements[$elementIndex]['validation']['rules'] = $rules;
            }

            // Refresh preview and edit elements if we have a valid element index
            if ($elementIndex !== null) {
                $this->refreshPreviewElement($elementIndex);
                $this->refreshEditElement($elementIndex);
            }

            // Force refresh of rendered elements to update the canvas
            $this->refreshRenderedElements();

            // Ensure properties structure is maintained after updates
            $this->ensurePropertiesStructure();

            // Dispatch event for real-time updates with more detailed information
            $this->dispatch('element-updated', [
                'key' => $key,
                'value' => $value,
                'elementIndex' => $elementIndex,
                'elementId' => $elementIndex !== null ? ($this->elements[$elementIndex]['id'] ?? null) : null,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            logger()->error('Error updating elements', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't add error to avoid breaking the UI, just log it
        }
    }



    /**
     * Select an element by its ID.
     *
     * @param string $elementId
     * @return void
     */
    public function selectElement(string $elementId): void
    {
        try {
            logger()->debug('selectElement called', [
                'elementId' => $elementId,
                'currentSelectedElementId' => $this->selectedElementId,
                'elementsCount' => count($this->elements)
            ]);
            
            // Validate element exists
            $element = $this->elementManager->findElement($this->elements, $elementId);
            if (! $element) {
                logger()->warning('Element not found during selection', ['elementId' => $elementId]);
                $this->addError('element_selection', __('forms.errors.element_not_found', ['id' => $elementId]));
                return;
            }
            
            $this->selectedElementId = $elementId;
            $this->updateSelectedElementData();
        } catch (\Exception $e) {
            logger()->error('Error selecting element', [
                'elementId' => $elementId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clear selection on error
            $this->selectedElementId = null;
            $this->selectedElement = null;
            $this->selectedElementIndex = null;
            
            $this->addError('element_selection', __('forms.errors.error_selecting_element'));
        }
    }

    private function updateSelectedElementData(): void
    {
        logger()->debug('updateSelectedElementData called', [
            'selectedElementId' => $this->selectedElementId,
            'elementsCount' => count($this->elements),
            'draftElementIds' => collect($this->elements)->pluck('id')->toArray()
        ]);
        
        if ($this->selectedElementId === null) {
            $this->selectedElement = null;
            $this->selectedElementIndex = null;
            logger()->debug('Selected element cleared');
            return;
        }

        $this->selectedElement = $this->elementManager->findElement($this->elements, $this->selectedElementId);
        $this->selectedElementIndex = $this->elementManager->findElementIndex($this->elements, $this->selectedElementId);
        
        // Debug logging
        logger()->debug('Selected element data updated', [
            'selectedElementId' => $this->selectedElementId,
            'selectedElement' => $this->selectedElement ? 'found' : 'not found',
            'selectedElementIndex' => $this->selectedElementIndex,
        ]);
    }

    public function refreshPreviewElement($elementIndex)
    {
        if (isset($this->elements[$elementIndex])) {
            $element = $this->elements[$elementIndex];
            $fieldName = $this->generateFieldName($element);
            
            // For submit button elements, pass the active breakpoint
            if ($element['type'] === 'submit_button') {
                $elementWithBreakpoint = $element;
                $elementWithBreakpoint['properties']['_activeBreakpoint'] = $this->activeBreakpoint;
                $previewHtml = $this->elementFactory->renderElement($elementWithBreakpoint, 'preview', $fieldName);
            } else {
                $previewHtml = $this->elementFactory->renderElement($element, 'preview', $fieldName);
            }

            $this->dispatch('preview-element-updated', [
                'elementIndex' => $elementIndex,
                'elementId' => $element['id'],
                'html' => $previewHtml,
            ]);
        }
    }

    public function refreshEditElement($elementIndex)
    {
        if (isset($this->elements[$elementIndex])) {
            $element = $this->elements[$elementIndex];
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
        $this->elementManager->updateElementWidth($this->elements, $elementId, $breakpoint, $width);
    }

    public function updateElementAlignment(string $elementId, string $breakpoint, string $alignment): void
    {
        $elementIndex = $this->elementManager->findElementIndex($this->elements, $elementId);
        
        if ($elementIndex !== null) {
            // Ensure the styles structure exists
            if (!isset($this->elements[$elementIndex]['styles'])) {
                $this->elements[$elementIndex]['styles'] = [];
            }
            if (!isset($this->elements[$elementIndex]['styles'][$breakpoint])) {
                $this->elements[$elementIndex]['styles'][$breakpoint] = [];
            }
            
            // Update the alignment
            $this->elements[$elementIndex]['styles'][$breakpoint]['alignment'] = $alignment;
            
            // Refresh the preview and edit elements
            $this->refreshPreviewElement($elementIndex);
            $this->refreshEditElement($elementIndex);
            
            // Force refresh of rendered elements to update the canvas
            $this->refreshRenderedElements();
        }
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
        if (! isset($this->elements[$elementIndex]['validation'])) {
            $this->elements[$elementIndex]['validation'] = config('forms.elements.default_validation');
        }

        if (! isset($this->elements[$elementIndex]['validation']['rules'])) {
            $this->elements[$elementIndex]['validation']['rules'] = [];
        }

        $rules = $this->elements[$elementIndex]['validation']['rules'];

        // Toggle the rule
        if (in_array($ruleKey, $rules)) {
            // Remove the rule
            $rules = array_values(array_filter($rules, fn ($rule) => $rule !== $ruleKey));

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
        // Cache key based on selected element
        $cacheKey = $this->selectedElementId . '_' . ($this->selectedElementIndex ?? 'null');
        
        // Return cached value if available
        if (isset($this->cachedOptions[$cacheKey])) {
            return $this->cachedOptions[$cacheKey];
        }

        if (! $this->selectedElement || $this->selectedElement['type'] !== 'select') {
            $this->cachedOptions[$cacheKey] = [];
            return [];
        }

        $options = $this->elements[$this->selectedElementIndex]['properties']['options'] ?? '';

        if (is_array($options)) {
            $this->cachedOptions[$cacheKey] = $options;
            return $options;
        }

        // Parse options from string format using injected OptionParserService
        $parsedOptions = $this->optionParser->parseOptions($options);
        $this->cachedOptions[$cacheKey] = $parsedOptions;
        
        return $parsedOptions;
    }

    #[Computed]
    public function selectedElementOptionsArray(): array
    {
        if (! $this->selectedElement || ! in_array($this->selectedElement['type'], ['select', 'checkbox', 'radio'])) {
            return [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }

        return $this->getElementOptionsArray($this->selectedElementIndex);
    }

    public function getElementOptionsArray(int $elementIndex): array
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

        // If options is a string, parse it using the injected OptionParserService
        return $this->optionParser->parseOptions($options);
    }

    public function parseOptionsForPreview($options): array
    {
        // If options is already an array, return it directly
        if (is_array($options)) {
            return $options;
        }

        // If options is a string, parse it using the injected OptionParserService
        return $this->optionParser->parseOptionsForPreview($options);
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
    public function elementTypes(): array
    {
        return FormElementType::cases();
    }

    public function togglePreview(): void
    {
        $this->isPreviewMode = ! $this->isPreviewMode;

        if ($this->isPreviewMode) {
            $this->initializePreviewFormData();
        }
    }

    /**
     * Confirm deletion of the form.
     */
    public function confirmDeleteForm(): void
    {
        $this->confirmAction(
            __('messages.forms.form_builder_interface.delete_form_title'),
            __('messages.forms.form_builder_interface.delete_form_confirmation'),
            'deleteForm'
        );
    }

    /**
     * Delete the form and redirect to forms index.
     */
    #[On('deleteForm')]
    public function deleteForm(): void
    {
        try {
            logger()->info('Attempting to delete form', ['form_id' => $this->form->id]);
            
            // Store the form ID for logging after deletion
            $formId = $this->form->id;
            
            // Delete the form using the service
            $deleted = $this->formService->deleteForm($this->form);
            
            logger()->info('Form deletion result', ['deleted' => $deleted, 'form_id' => $formId]);
            
            if ($deleted) {
                $this->showSuccessToast(__('messages.forms.form_builder_interface.form_deleted'));
                
                // Use redirectRoute with navigate: true for better reliability
                $this->redirectRoute('admin.forms.index', navigate: true);
            } else {
                $this->showErrorToast(__('messages.forms.form_builder_interface.delete_error'));
            }
        } catch (\Exception $e) {
            logger()->error('Error deleting form', [
                'form_id' => $this->form->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->showErrorToast(__('messages.forms.form_builder_interface.delete_error'));
        }
    }

    private function initializePreviewFormData(): void
    {
        $this->previewFormData = [];

        foreach ($this->elements as $element) {
            // Skip submit button as it's not a form field that collects data
            if ($element['type'] === 'submit_button') {
                continue;
            }
            
            $fieldName = $this->generateFieldName($element);
            
            // Initialize checkbox and radio fields with empty arrays
            // since they can have multiple selected values
            if (in_array($element['type'], ['checkbox', 'radio'])) {
                $this->previewFormData[$fieldName] = [];
            } else {
                $this->previewFormData[$fieldName] = '';
            }
        }
    }

    private function synchronizePreviewFormData(): void
    {
        // Initialize preview form data for all current elements
        foreach ($this->elements as $element) {
            // Skip submit button as it's not a form field that collects data
            if ($element['type'] === 'submit_button') {
                continue;
            }
            
            $fieldName = $this->generateFieldName($element);
            
            // Initialize checkbox and radio fields with empty arrays
            // since they can have multiple selected values
            if (in_array($element['type'], ['checkbox', 'radio'])) {
                if (!isset($this->previewFormData[$fieldName]) || !is_array($this->previewFormData[$fieldName])) {
                    $this->previewFormData[$fieldName] = [];
                }
            } else {
                if (!isset($this->previewFormData[$fieldName])) {
                    $this->previewFormData[$fieldName] = '';
                }
            }
        }
    }

    private function generateFieldName(array $element): string
    {
        return $this->fieldNameGenerator->generateFieldName($element);
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
    public function hasChanges(): bool
    {
        $latestRevision = $this->form->latestRevision();

        if (! $latestRevision) {
            // If there are no revisions, check if there are any elements or settings.
            return ! empty($this->elements) || ! empty($this->settings) || ! empty($this->name);
        }

        $revisionData = $latestRevision->data;

        $currentData = [
            'name' => $this->name,
            'elements' => $this->elements,
            'settings' => $this->settings,
        ];

        // Normalize data for comparison
        $normalizedRevisionData = json_decode(json_encode($revisionData), true);
        $normalizedCurrentData = json_decode(json_encode($currentData), true);
        
        return $normalizedRevisionData !== $normalizedCurrentData;
    }

    #[Computed]
    public function renderedElements(): array
    {
        return collect($this->elements)->map(function ($element, $index) {
            $fieldName = $this->generateFieldName($element);
            
            // For submit button elements, we need to pass the active breakpoint
            if ($element['type'] === 'submit_button') {
                // Store the active breakpoint in the element data temporarily
                $elementWithBreakpoint = $element;
                $elementWithBreakpoint['properties']['_activeBreakpoint'] = $this->activeBreakpoint;
                return $this->elementFactory->renderElement($elementWithBreakpoint, 'preview', $fieldName);
            }
            
            return $this->elementFactory->renderElement($element, 'preview', $fieldName);
        })->toArray();
    }

    /**
     * Force refresh of rendered elements.
     * This method can be called to force the computed property to recalculate.
     */
    public function refreshRenderedElements(): void
    {
        // Force the computed property to recalculate by accessing it
        $this->renderedElements();
    }

    /**
     * Save element draft data from JavaScript.
     *
     * @param array $data
     * @return void
     */
    #[On('saveElementDraft')]
    public function saveElementDraft(array $data): void
    {
        $elementId = $data['elementId'] ?? null;
        $state = $data['state'] ?? [];

        if (! $elementId) {
            $this->addError('draft_save', __('forms.errors.element_id_required'));
            return;
        }

        if (empty($state)) {
            $this->addError('draft_save', 'Element state cannot be empty');
            return;
        }

        $index = $this->elementManager->findElementIndex($this->elements, $elementId);
        
        if ($index === null) {
            $this->addError('draft_save', __('forms.errors.element_not_found', ['id' => $elementId]));
            return;
        }

        // Update the element with the new state
        $this->elements[$index] = array_merge($this->elements[$index], $state);
        
        // Ensure properties structure is maintained
        $this->ensurePropertiesStructure();
        
        // Refresh preview and edit elements
        $this->refreshPreviewElement($index);
        $this->refreshEditElement($index);
        
        logger()->debug('Element draft saved', ['elementId' => $elementId]);
    }

    /**
     * Load element data for JavaScript editing.
     *
     * @param array $data
     * @return void
     */
    #[On('loadElementData')]
    public function loadElementData(array $data): void
    {
        $elementId = $data['elementId'] ?? null;

        if (! $elementId) {
            $this->addError('element_load', __('forms.errors.element_id_required'));
            return;
        }

        $element = $this->elementManager->findElement($this->elements, $elementId);
        
        if (! $element) {
            $this->addError('element_load', __('forms.errors.element_not_found', ['id' => $elementId]));
            return;
        }

        $this->dispatch('element-data-loaded', [
            'elementId' => $elementId,
            'elementState' => $element
        ]);
        
        logger()->debug('Element data loaded', ['elementId' => $elementId]);
    }

    /**
     * Handle form validation errors and provide user feedback.
     *
     * @param string $error
     * @param string $context
     * @return void
     */
    private function handleValidationError(string $error, string $context = 'form'): void
    {
        logger()->error("Form validation error in {$context}: {$error}");
        $this->addError($context, $error);
        
        // Show toast notification for user feedback
        $this->showErrorToast("Form error: {$error}");
    }

    /**
     * Validate the form structure and ensure data integrity.
     *
     * @return void
     */
    private function validateFormStructure(): void
    {
        // Validate elements array
        if (! is_array($this->elements)) {
            $this->elements = [];
        }

        // Validate settings array
        if (! is_array($this->settings)) {
            $this->settings = config('forms.builder.default_settings');
        }

        // Validate name array
        if (! is_array($this->name)) {
            $this->name = [];
        }

        // Validate each element
        foreach ($this->elements as $index => $element) {
            if (! is_array($element)) {
                unset($this->elements[$index]);
                continue;
            }

            // Ensure required fields exist
            if (empty($element['id'])) {
                $this->elements[$index]['id'] = (string) Str::uuid();
            }

            if (empty($element['type'])) {
                unset($this->elements[$index]);
                continue;
            }

            // Validate element type
            if (! $this->elementFactory->getRenderer($element['type'])) {
                unset($this->elements[$index]);
                continue;
            }
        }

        // Re-index elements array
        $this->elements = array_values($this->elements);
    }

    /**
     * Clear the options cache when elements are updated.
     *
     * @return void
     */
    private function clearOptionsCache(): void
    {
        $this->cachedOptions = [];
    }

    public function render()
    {
        return view('livewire.form-builder');
    }

    public function duplicateElement($elementId): void
    {
        try {
            $originalIndex = $this->elementManager->findElementIndex($this->elements, $elementId);
            if ($originalIndex === null) {
                $this->handleValidationError(__('forms.errors.element_not_found', ['id' => $elementId]), 'element_duplication');
                return;
            }
            $this->elementManager->duplicateElement($this->elements, $elementId);
            $this->ensurePropertiesStructure();
            $this->ensureValidationStructure();
            $newElement = end($this->elements);
            $this->selectElement($newElement['id']);
            $this->showSuccessToast(__('forms.buttons.duplicate') . ' ' . __('forms.ui.form_submitted'));
        } catch (\Exception $e) {
            $this->handleValidationError($e->getMessage(), 'element_duplication');
        }
    }

    #[On('duplicateElement')]
    public function handleDuplicateElement($elementId): void
    {
        $this->duplicateElement($elementId);
    }
}
