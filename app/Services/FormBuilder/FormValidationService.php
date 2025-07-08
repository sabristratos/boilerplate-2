<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Services\FormBuilder\ValidationRuleService;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling form validation logic.
 *
 * This service extracts validation-related business logic from the FormBuilder component,
 * providing a clean separation of concerns for form validation operations.
 */
class FormValidationService
{
    public function __construct(
        private readonly ValidationRuleService $validationRuleService
    ) {}

    /**
     * Ensure all elements have proper validation structure.
     *
     * @param array $elements The elements array to validate
     * @return array The elements with proper validation structure
     */
    public function ensureValidationStructure(array $elements): array
    {
        foreach (array_keys($elements) as $index) {
            if (!isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            } else {
                // Ensure all required validation keys exist
                $defaultValidation = config('forms.elements.default_validation');
                foreach ($defaultValidation as $key => $defaultValue) {
                    if (!isset($elements[$index]['validation'][$key])) {
                        $elements[$index]['validation'][$key] = $defaultValue;
                    }
                }
            }
        }

        return $elements;
    }

    /**
     * Toggle a validation rule for an element.
     *
     * @param array $elements The elements array
     * @param int $elementIndex The element index
     * @param string $ruleKey The rule key to toggle
     * @return array The updated elements array
     */
    public function toggleValidationRule(array $elements, int $elementIndex, string $ruleKey): array
    {
        // Ensure the validation structure exists
        if (!isset($elements[$elementIndex]['validation'])) {
            $elements[$elementIndex]['validation'] = config('forms.elements.default_validation');
        }

        if (!isset($elements[$elementIndex]['validation']['rules'])) {
            $elements[$elementIndex]['validation']['rules'] = [];
        }

        $rules = $elements[$elementIndex]['validation']['rules'];

        // Toggle the rule
        if (in_array($ruleKey, $rules)) {
            // Remove the rule
            $rules = array_values(array_filter($rules, fn ($rule): bool => $rule !== $ruleKey));

            // Also remove any associated values and messages
            if (isset($elements[$elementIndex]['validation']['values'][$ruleKey])) {
                unset($elements[$elementIndex]['validation']['values'][$ruleKey]);
            }
            if (isset($elements[$elementIndex]['validation']['messages'][$ruleKey])) {
                unset($elements[$elementIndex]['validation']['messages'][$ruleKey]);
            }
        } else {
            // Add the rule
            $rules[] = $ruleKey;
        }

        $elements[$elementIndex]['validation']['rules'] = $rules;

        return $elements;
    }

    /**
     * Get validation placeholder text for a rule key.
     *
     * @param string $ruleKey The rule key
     * @return string The placeholder text
     */
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

    /**
     * Get available validation rules for an element type.
     *
     * @param string $elementType The element type
     * @return array The available validation rules
     */
    public function getAvailableValidationRules(string $elementType): array
    {
        return $this->validationRuleService->getRelevantRules($elementType);
    }

    /**
     * Generate validation rules for an element.
     *
     * @param array $element The element data
     * @return array The generated validation rules
     */
    public function generateValidationRules(array $element): array
    {
        return $this->validationRuleService->generateRules($element);
    }

    /**
     * Generate validation messages for an element.
     *
     * @param array $element The element data
     * @return array The generated validation messages
     */
    public function generateValidationMessages(array $element): array
    {
        return $this->validationRuleService->generateMessages($element);
    }

    /**
     * Validate form structure.
     *
     * @param array $elements The elements to validate
     * @return array Validation errors, empty if valid
     */
    public function validateFormStructure(array $elements): array
    {
        $errors = [];

        foreach ($elements as $index => $element) {
            // Check if element has required fields
            if (!isset($element['id']) || !isset($element['type'])) {
                $errors[] = "Element at index {$index} is missing required fields (id, type)";
                continue;
            }

            // Validate element type
            if (!in_array($element['type'], ['text', 'textarea', 'email', 'select', 'checkbox', 'radio', 'date', 'number', 'password', 'file', 'submit_button'])) {
                $errors[] = "Element at index {$index} has invalid type: {$element['type']}";
            }

            // Validate properties structure
            if (!isset($element['properties']) || !is_array($element['properties'])) {
                $errors[] = "Element at index {$index} is missing properties array";
            }

            // Validate validation structure
            if (!isset($element['validation']) || !is_array($element['validation'])) {
                $errors[] = "Element at index {$index} is missing validation array";
            }
        }

        return $errors;
    }

    /**
     * Update validation rules for an element.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @param array $rules The validation rules
     */
    public function updateValidationRules(array &$elements, string $elementId, array $rules): void
    {
        $index = $this->findElementIndex($elements, $elementId);
        if ($index !== null) {
            if (!isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }
            $elements[$index]['validation']['rules'] = $rules;
        }
    }

    /**
     * Update validation message for a rule.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @param string $rule The rule name
     * @param string $message The validation message
     */
    public function updateValidationMessage(array &$elements, string $elementId, string $rule, string $message): void
    {
        $index = $this->findElementIndex($elements, $elementId);
        if ($index !== null) {
            if (!isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }
            if (!isset($elements[$index]['validation']['messages'])) {
                $elements[$index]['validation']['messages'] = [];
            }
            $elements[$index]['validation']['messages'][$rule] = $message;
        }
    }

    /**
     * Update validation rule value.
     *
     * @param array $elements Reference to the elements array
     * @param string $elementId The element ID
     * @param string $rule The rule name
     * @param string $value The rule value
     */
    public function updateValidationRuleValue(array &$elements, string $elementId, string $rule, string $value): void
    {
        $index = $this->findElementIndex($elements, $elementId);
        if ($index !== null) {
            if (!isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }
            if (!isset($elements[$index]['validation']['values'])) {
                $elements[$index]['validation']['values'] = [];
            }
            $elements[$index]['validation']['values'][$rule] = $value;
        }
    }

    /**
     * Find element index by ID.
     *
     * @param array $elements The elements array
     * @param string $elementId The element ID
     * @return int|null The element index or null if not found
     */
    private function findElementIndex(array $elements, string $elementId): ?int
    {
        foreach ($elements as $index => $element) {
            if (isset($element['id']) && $element['id'] === $elementId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Handle validation error.
     *
     * @param string $error The error message
     * @param string $context The error context
     */
    public function handleValidationError(string $error, string $context = 'form'): void
    {
        Log::error("Form validation error in {$context}", [
            'error' => $error,
            'context' => $context,
        ]);
    }
} 