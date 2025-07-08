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
use App\Services\FormBuilder\FormValidationService;
use App\Services\FormBuilder\FormPreviewService;
use App\Services\Contracts\FormServiceInterface;
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

    private IconService $iconService;

    private ElementFactory $elementFactory;

    private OptionParserService $optionParser;

    private FieldNameGeneratorService $fieldNameGenerator;
    private FormServiceInterface $formService;

    private FormValidationService $formValidationService;

    private FormPreviewService $formPreviewService;

    /**
     * Boot the component with dependencies.
     */
    public function boot(
        ElementManager $elementManager,
        IconService $iconService,
        ElementFactory $elementFactory,
        OptionParserService $optionParser,
        FieldNameGeneratorService $fieldNameGenerator,
        FormServiceInterface $formService,
        FormValidationService $formValidationService,
        FormPreviewService $formPreviewService
    ): void {
        $this->elementManager = $elementManager;
        $this->iconService = $iconService;
        $this->elementFactory = $elementFactory;
        $this->optionParser = $optionParser;
        $this->fieldNameGenerator = $fieldNameGenerator;
        $this->formService = $formService;
        $this->formValidationService = $formValidationService;
        $this->formPreviewService = $formPreviewService;
    }

    /**
     * Mount the component with the given form ID.
     */
    public function mount(int $id): void
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
        $this->elements = $this->formValidationService->ensureValidationStructure($this->elements);
        $this->elements = $this->elementManager->ensurePropertiesStructure($this->elements);
    }

    /**
     * Hydrate the component when it's loaded from the database.
     */
    public function hydrate(): void
    {
        try {
            // Ensure preview form data is properly synchronized
            $this->previewFormData = $this->formPreviewService->synchronizePreviewFormData($this->elements, $this->previewFormData);
            
            // Ensure validation structure is maintained
            $this->elements = $this->formValidationService->ensureValidationStructure($this->elements);
            
            // Ensure properties structure is maintained
            $this->elements = $this->elementManager->ensurePropertiesStructure($this->elements);
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
     */
    public function dehydrate(): void
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
     * Add a new element of the given type.
     */
    public function addElement(string $type): void
    {
        try {
            // Add to elements array using the service (modifies by reference)
            $this->elementManager->addElement($this->elements, $type);

            // Select the newly added element (last element)
            $lastElement = end($this->elements);
            $this->selectElement($lastElement['id']);

            // Initialize preview form data for the new element (skip submit button)
            if ($type !== 'submit_button') {
                $this->previewFormData = $this->formPreviewService->addPreviewFormData($this->previewFormData, $lastElement);
            }
        } catch (\Exception $e) {
            $this->addError('element_creation', $e->getMessage());
            $this->showErrorToast($e->getMessage());
        }
    }

    /**
     * Delete an element by its ID.
     */
    #[On('deleteElement')]
    public function deleteElement(string $elementId): void
    {
        // Find the element before deletion to get its field name
        $element = $this->elementManager->findElement($this->elements, $elementId);
        
        // Remove from elements array (modifies by reference)
        $this->elementManager->deleteElement($this->elements, $elementId);

        // Clear selection if the deleted element was selected
        if ($this->selectedElementId === $elementId) {
            $this->selectedElementId = null;
        }

        // Clean up preview form data for the deleted element
        if ($element) {
            $this->previewFormData = $this->formPreviewService->removePreviewFormData($this->previewFormData, $element);
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

        // Update the options string in the element directly
        if (isset($this->elements[$elementIndex])) {
            $this->elements[$elementIndex]['properties'][$propertyPath] = $optionsString;
        }
        
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
        $this->elementManager->reorderElements($this->elements, $orderedOrders);
    }

    /**
     * Save the form as a draft.
     */
    public function saveDraft(): void
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
                $this->addError('form', $formDto->getValidationErrorsAsString());
                return;
            }

            // Save as draft using the action
            $action = app(SaveDraftFormAction::class);
            $action->execute($this->form, $formDto);

            $this->showSuccessToast(__('forms.toast_draft_saved'));

        } catch (\Exception $e) {
            logger()->error('Error saving form draft', [
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->addError('form', __('forms.errors.failed_to_save_draft'));
            $this->showErrorToast(__('forms.errors.failed_to_save_draft'));
        }
    }

    /**
     * Publish the form.
     */
    public function publish(): void
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
                $this->addError('form', $formDto->getValidationErrorsAsString());
                return;
            }

            // Publish using the action
            $action = app(PublishFormAction::class);
            $action->execute($this->form, $formDto);

            $this->showSuccessToast(__('forms.toast_form_published'));

        } catch (\Exception $e) {
            logger()->error('Error publishing form', [
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->addError('form', __('forms.errors.failed_to_publish_form'));
            $this->showErrorToast(__('forms.errors.failed_to_publish_form'));
        }
    }

    /**
     * Discard draft changes and revert to the latest published version.
     */
    public function discardDraft(): void
    {
        try {
            $action = app(DiscardFormDraftAction::class);
            $action->execute($this->form);

            // Reload the form data from the published revision
            $this->reloadFormData();

            $this->showSuccessToast(__('forms.toast_draft_discarded'));

        } catch (\Exception $e) {
            logger()->error('Error discarding form draft', [
                'form_id' => $this->form->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->addError('form', __('forms.errors.failed_to_discard_draft'));
            $this->showErrorToast(__('forms.errors.failed_to_discard_draft'));
        }
    }

    /**
     * Reload form data from the latest revision.
     */
    private function reloadFormData(): void
    {
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

        // Update selected element data
        $this->updateSelectedElementData();
    }

    public function updatedname($value, $key): void
    {
        // Handle draft name updates
        $this->dispatch('draft-name-updated', key: $key, value: $value);
    }

    public function updatedelements($value, $key): void
    {
        try {
            // Clear options cache when elements are updated
            $this->clearOptionsCache();

            // Parse the key once to avoid multiple explode() calls
            $parts = explode('.', (string) $key);
            $elementIndex = null;
            
            if (is_numeric($parts[0])) {
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
            if (str_contains((string) $key, 'properties.locale') && $elementIndex !== null && empty($value)) {
                $this->elements[$elementIndex]['properties']['locale'] = 'en';
            }

            // Handle checkbox and radio field preview data initialization
            if ($elementIndex !== null && isset($this->elements[$elementIndex])) {
                $element = $this->elements[$elementIndex];
                $fieldName = $this->generateFieldName($element);
                
                // Ensure checkbox and radio fields have array values in preview data
                if (in_array($element['type'], ['checkbox', 'radio']) && (!isset($this->previewFormData[$fieldName]) || !is_array($this->previewFormData[$fieldName]))) {
                    $this->previewFormData[$fieldName] = [];
                }
            }

            // Handle validation rule activation based on input values
            if (str_contains((string) $key, 'validation.values.') && count($parts) >= 4) {
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
                    $rules = array_values(array_filter($rules, fn ($rule): bool => $rule !== $ruleKey));

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
            $this->elementManager->ensurePropertiesStructure($this->elements);

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

    public function refreshPreviewElement($elementIndex): void
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

    public function refreshEditElement($elementIndex): void
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
        try {
            $this->elementManager->updateElementWidth($this->elements, $elementId, $breakpoint, $width);
        } catch (\Exception) {
            // Silently handle the error as this is a UI update
        }
    }

    public function updateElementAlignment(string $elementId, string $breakpoint, string $alignment): void
    {
        try {
            $this->elementManager->updateElementAlignment($this->elements, $elementId, $breakpoint, $alignment);
            
            // Refresh the preview and edit elements
            $elementIndex = $this->elementManager->findElementIndex($this->elements, $elementId);
            if ($elementIndex !== null) {
                $this->refreshPreviewElement($elementIndex);
                $this->refreshEditElement($elementIndex);
                
                // Force refresh of rendered elements to update the canvas
                $this->refreshRenderedElements();
            }
        } catch (\Exception) {
            // Silently handle the error as this is a UI update
        }
    }

    public function updateValidationRules(string $elementId, array $rules): void
    {
        $this->formValidationService->updateValidationRules($this->elements, $elementId, $rules);
    }

    public function updateValidationMessage(string $elementId, string $rule, string $message): void
    {
        $this->formValidationService->updateValidationMessage($this->elements, $elementId, $rule, $message);
    }

    public function updateValidationRuleValue(string $elementId, string $rule, string $value): void
    {
        $this->formValidationService->updateValidationRuleValue($this->elements, $elementId, $rule, $value);
    }

    public function toggleValidationRule(string $elementId, string $ruleKey): void
    {
        $elementIndex = $this->elementManager->findElementIndex($this->elements, $elementId);
        if ($elementIndex === null) {
            return; // Element not found, do nothing
        }
        $this->elements = $this->formValidationService->toggleValidationRule($this->elements, $elementIndex, $ruleKey);
    }

    public function getValidationPlaceholder(string $ruleKey): string
    {
        return $this->formValidationService->getValidationPlaceholder($ruleKey);
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

        if ($this->selectedElement === null || $this->selectedElement === [] || $this->selectedElement['type'] !== 'select') {
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
        if ($this->selectedElement === null || $this->selectedElement === [] || ! in_array($this->selectedElement['type'], ['select', 'checkbox', 'radio'])) {
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
        if (!isset($this->elements[$elementIndex])) {
            return [];
        }

        $options = $this->elements[$elementIndex]['properties']['options'] ?? '';

        if (is_array($options)) {
            return $options;
        }

        // Parse options from string format using injected OptionParserService
        return $this->optionParser->parseOptions($options);
    }

    public function parseOptionsForPreview($options): array
    {
        return $this->optionParser->parseOptionsForPreview($options);
    }

    #[Computed]
    public function availableValidationRules(): array
    {
        if ($this->selectedElement === null || $this->selectedElement === []) {
            return [];
        }

        return $this->formValidationService->getAvailableValidationRules($this->selectedElement['type']);
    }

    #[Computed]
    public function availableIcons(): array
    {
        return $this->iconService->getAvailableIcons();
    }

    public function generateValidationRules(array $element): array
    {
        return $this->formValidationService->generateValidationRules($element);
    }

    public function generateValidationMessages(array $element): array
    {
        return $this->formValidationService->generateValidationMessages($element);
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
            $this->previewFormData = $this->formPreviewService->initializePreviewFormData($this->elements);
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

    public function confirmDelete(...$params): void
    {
        $this->confirmDeleteForm(...$params);
    }


    public function submitPreview(): void
    {
        $result = $this->formPreviewService->submitPreview($this->form, $this->previewFormData);

        if ($result['success']) {
            $this->showSuccessToast('Form submitted successfully! (Preview Mode)');
            $this->previewFormData = $this->formPreviewService->initializePreviewFormData($this->elements);
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
        return $this->formPreviewService->hasChanges($this->form, $this->name, $this->elements, $this->settings);
    }

    #[Computed]
    public function hasDraftChanges(): bool
    {
        return $this->form->hasDraftChanges();
    }

    #[Computed]
    public function isPublished(): bool
    {
        return $this->form->isPublished();
    }

    #[Computed]
    public function isDraft(): bool
    {
        return $this->form->isDraft();
    }

    #[Computed]
    public function canPublish(): bool
    {
        // Can publish if form has elements and is not already published
        return !empty($this->elements) && !$this->form->isPublished();
    }

    #[Computed]
    public function canDiscardDraft(): bool
    {
        // Can discard if there are draft changes
        return $this->form->hasDraftChanges();
    }

    #[Computed]
    public function renderedElements(): array
    {
        return $this->formPreviewService->renderElements($this->elements, $this->activeBreakpoint);
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

        try {
            // Find the element and update its state
            $index = $this->elementManager->findElementIndex($this->elements, $elementId);
            if ($index !== null) {
                $this->elements[$index] = array_merge($this->elements[$index], $state);
                
                // Refresh preview and edit elements
                $this->refreshPreviewElement($index);
                $this->refreshEditElement($index);
            }
        } catch (\Exception $e) {
            $this->addError('draft_save', $e->getMessage());
        }
    }

    /**
     * Load element data for JavaScript editing.
     */
    #[On('loadElementData')]
    public function loadElementData(array $data): void
    {
        $elementId = $data['elementId'] ?? null;

        if (! $elementId) {
            $this->addError('element_load', __('forms.errors.element_id_required'));
            return;
        }

        try {
            $element = $this->elementManager->findElement($this->elements, $elementId);

            $this->dispatch('element-data-loaded', [
                'elementId' => $elementId,
                'elementState' => $element
            ]);
        } catch (\Exception $e) {
            $this->addError('element_load', $e->getMessage());
        }
    }



    /**
     * Validate the form structure and ensure data integrity.
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
            if (!$this->elementFactory->getRenderer($element['type']) instanceof \App\Services\FormBuilder\Contracts\ElementRendererInterface) {
                unset($this->elements[$index]);
                continue;
            }
        }

        // Re-index elements array
        $this->elements = array_values($this->elements);
    }

    /**
     * Clear the options cache when elements are updated.
     */
    private function clearOptionsCache(): void
    {
        $this->cachedOptions = [];
    }

    public function render()
    {
        return view('livewire.form-builder');
    }

    public function duplicateElement(string $elementId): void
    {
        try {
            // Duplicate the element using the service (modifies by reference)
            $this->elementManager->duplicateElement($this->elements, $elementId);
            
            // Get the last element (the duplicated one)
            $lastElement = end($this->elements);
            
            // Select the newly duplicated element
            $this->selectElement($lastElement['id']);
            
            $this->showSuccessToast(__('forms.buttons.duplicate') . ' ' . __('forms.ui.form_submitted'));
        } catch (\Exception $e) {
            $this->addError('element_duplication', $e->getMessage());
            $this->showErrorToast($e->getMessage());
        }
    }

    #[On('duplicateElement')]
    public function handleDuplicateElement($elementId): void
    {
        $this->duplicateElement($elementId);
    }
}
