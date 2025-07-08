<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Models\Form;
use App\Services\FormBuilder\FormSubmissionErrorHandler;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling form preview functionality.
 *
 * This service extracts preview-related business logic from the FormBuilder component,
 * providing a clean separation of concerns for preview operations.
 */
class FormPreviewService
{
    public function __construct(
        private readonly ElementFactory $elementFactory,
        private readonly FieldNameGeneratorService $fieldNameGenerator
    ) {}

    /**
     * Initialize preview form data for all elements.
     *
     * @param array $elements The elements array
     * @return array The initialized preview form data
     */
    public function initializePreviewFormData(array $elements): array
    {
        $previewFormData = [];

        foreach ($elements as $element) {
            // Skip submit button as it's not a form field that collects data
            if ($element['type'] === 'submit_button') {
                continue;
            }
            
            $fieldName = $this->fieldNameGenerator->generateFieldName($element);
            
            // Initialize checkbox and radio fields with empty arrays
            // since they can have multiple selected values
            $previewFormData[$fieldName] = in_array($element['type'], ['checkbox', 'radio']) ? [] : '';
        }

        return $previewFormData;
    }

    /**
     * Synchronize preview form data with current elements.
     *
     * @param array $elements The elements array
     * @param array $previewFormData The current preview form data
     * @return array The synchronized preview form data
     */
    public function synchronizePreviewFormData(array $elements, array $previewFormData): array
    {
        // Initialize preview form data for all current elements
        foreach ($elements as $element) {
            // Skip submit button as it's not a form field that collects data
            if ($element['type'] === 'submit_button') {
                continue;
            }
            
            $fieldName = $this->fieldNameGenerator->generateFieldName($element);
            
            // Initialize checkbox and radio fields with empty arrays
            // since they can have multiple selected values
            if (in_array($element['type'], ['checkbox', 'radio'])) {
                if (!isset($previewFormData[$fieldName]) || !is_array($previewFormData[$fieldName])) {
                    $previewFormData[$fieldName] = [];
                }
            } elseif (!isset($previewFormData[$fieldName])) {
                $previewFormData[$fieldName] = '';
            }
        }

        return $previewFormData;
    }

    /**
     * Submit preview form data.
     *
     * @param Form $form The form model
     * @param array $previewFormData The preview form data
     * @return array The submission result
     */
    public function submitPreview(Form $form, array $previewFormData): array
    {
        $errorHandler = app(FormSubmissionErrorHandler::class);
        $result = $errorHandler->handleSubmission($form, $previewFormData);

        if ($result['success']) {
            Log::info('Preview form submitted successfully', [
                'form_id' => $form->id,
                'preview_data' => $previewFormData,
            ]);
        } else {
            Log::warning('Preview form submission failed', [
                'form_id' => $form->id,
                'errors' => $result['errors'] ?? [],
                'message' => $result['message'] ?? 'Unknown error',
            ]);
        }

        return $result;
    }

    /**
     * Render elements for preview.
     *
     * @param array $elements The elements array
     * @param string $activeBreakpoint The active breakpoint
     * @return array The rendered elements
     */
    public function renderElements(array $elements, string $activeBreakpoint): array
    {
        return collect($elements)->map(function (array $element) use ($activeBreakpoint): string {
            $fieldName = $this->fieldNameGenerator->generateFieldName($element);
            
            // For submit button elements, we need to pass the active breakpoint
            if ($element['type'] === 'submit_button') {
                // Store the active breakpoint in the element data temporarily
                $elementWithBreakpoint = $element;
                $elementWithBreakpoint['properties']['_activeBreakpoint'] = $activeBreakpoint;
                return $this->elementFactory->renderElement($elementWithBreakpoint, 'preview', $fieldName);
            }
            
            return $this->elementFactory->renderElement($element, 'preview', $fieldName);
        })->toArray();
    }

    /**
     * Check if form has changes compared to the latest revision.
     *
     * @param Form $form The form model
     * @param array $name The current name
     * @param array $elements The current elements
     * @param array $settings The current settings
     * @return bool True if there are changes, false otherwise
     */
    public function hasChanges(Form $form, array $name, array $elements, array $settings): bool
    {
        $latestRevision = $form->latestRevision();

        if (!$latestRevision instanceof \App\Models\Revision) {
            // If there are no revisions, check if there are any elements or settings.
            return $elements !== [] || $settings !== [] || $name !== [];
        }

        $revisionData = $latestRevision->data;

        $currentData = [
            'name' => $name,
            'elements' => $elements,
            'settings' => $settings,
        ];

        // Normalize data for comparison
        $normalizedRevisionData = json_decode(json_encode($revisionData), true);
        $normalizedCurrentData = json_decode(json_encode($currentData), true);
        
        return $normalizedRevisionData !== $normalizedCurrentData;
    }

    /**
     * Clean up preview form data for deleted elements.
     *
     * @param array $previewFormData The current preview form data
     * @param array $elements The current elements array
     * @return array The cleaned preview form data
     */
    public function cleanupPreviewFormData(array $previewFormData, array $elements): array
    {
        // Get all current field names
        $currentFieldNames = [];
        foreach ($elements as $element) {
            if ($element['type'] !== 'submit_button') {
                $currentFieldNames[] = $this->fieldNameGenerator->generateFieldName($element);
            }
        }

        // Remove preview form data for fields that no longer exist
        foreach (array_keys($previewFormData) as $fieldName) {
            if (!in_array($fieldName, $currentFieldNames)) {
                unset($previewFormData[$fieldName]);
            }
        }

        return $previewFormData;
    }

    /**
     * Add preview form data for a new element.
     *
     * @param array $previewFormData The current preview form data
     * @param array $element The new element
     * @return array The updated preview form data
     */
    public function addPreviewFormData(array $previewFormData, array $element): array
    {
        // Skip submit button as it's not a form field that collects data
        if ($element['type'] === 'submit_button') {
            return $previewFormData;
        }

        $fieldName = $this->fieldNameGenerator->generateFieldName($element);
        
        // Initialize checkbox and radio fields with empty arrays
        // since they can have multiple selected values
        $previewFormData[$fieldName] = in_array($element['type'], ['checkbox', 'radio']) ? [] : '';

        return $previewFormData;
    }

    /**
     * Remove preview form data for a deleted element.
     *
     * @param array $previewFormData The current preview form data
     * @param array $element The deleted element
     * @return array The updated preview form data
     */
    public function removePreviewFormData(array $previewFormData, array $element): array
    {
        $fieldName = $this->fieldNameGenerator->generateFieldName($element);
        
        if (isset($previewFormData[$fieldName])) {
            unset($previewFormData[$fieldName]);
        }

        return $previewFormData;
    }

    /**
     * Validate preview form data structure.
     *
     * @param array $previewFormData The preview form data
     * @param array $elements The elements array
     * @return array Validation errors, empty if valid
     */
    public function validatePreviewFormData(array $previewFormData, array $elements): array
    {
        $errors = [];

        foreach ($elements as $element) {
            if ($element['type'] === 'submit_button') {
                continue;
            }

            $fieldName = $this->fieldNameGenerator->generateFieldName($element);
            
            if (!isset($previewFormData[$fieldName])) {
                $errors[] = "Missing preview data for field: {$fieldName}";
                continue;
            }

            // Validate data type for checkbox and radio fields
            if (in_array($element['type'], ['checkbox', 'radio'])) {
                if (!is_array($previewFormData[$fieldName])) {
                    $errors[] = "Field {$fieldName} should be an array for {$element['type']} type";
                }
            } elseif (!is_string($previewFormData[$fieldName])) {
                $errors[] = "Field {$fieldName} should be a string for {$element['type']} type";
            }
        }

        return $errors;
    }
} 