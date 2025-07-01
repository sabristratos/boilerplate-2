<?php

namespace App\Services\FormBuilder;

use App\Services\FormBuilder\ElementManager;

class ValidationService
{
    private ValidationRuleService $validationRuleService;
    private ElementManager $elementManager;

    public function __construct(ValidationRuleService $validationRuleService, ElementManager $elementManager)
    {
        $this->validationRuleService = $validationRuleService;
        $this->elementManager = $elementManager;
    }

    /**
     * Generate validation rules for an element
     */
    public function generateRules(array $element): array
    {
        return $this->validationRuleService->generateRules($element);
    }

    /**
     * Generate validation messages for an element
     */
    public function generateMessages(array $element): array
    {
        return $this->validationRuleService->generateMessages($element);
    }

    /**
     * Get all available validation rules
     */
    public function getAvailableRules(): array
    {
        return $this->validationRuleService->getAllRules();
    }

    /**
     * Get relevant validation rules for a specific field type
     */
    public function getRelevantRules(string $fieldType): array
    {
        return $this->validationRuleService->getRelevantRules($fieldType);
    }

    /**
     * Get relevant validation rules grouped by category for a field type
     */
    public function getRelevantRulesByCategory(string $fieldType): array
    {
        return $this->validationRuleService->getRelevantRulesByCategory($fieldType);
    }

    /**
     * Get available categories for a field type
     */
    public function getAvailableCategories(string $fieldType): array
    {
        return $this->validationRuleService->getAvailableCategories($fieldType);
    }

    /**
     * Update validation rules for an element
     */
    public function updateValidationRules(array &$elements, string $elementId, array $rules): void
    {
        $index = $this->elementManager->findElementIndex($elements, $elementId);

        if ($index !== null) {
            // Ensure the validation structure exists
            if (! isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }

            $elements[$index]['validation']['rules'] = $rules;
        }
    }

    /**
     * Update validation message for a specific rule
     */
    public function updateValidationMessage(array &$elements, string $elementId, string $rule, string $message): void
    {
        $index = $this->elementManager->findElementIndex($elements, $elementId);

        if ($index !== null) {
            // Ensure the validation structure exists
            if (! isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }

            $elements[$index]['validation']['messages'][$rule] = $message;
        }
    }

    /**
     * Update validation rule value
     */
    public function updateValidationRuleValue(array &$elements, string $elementId, string $rule, string $value): void
    {
        $index = $this->elementManager->findElementIndex($elements, $elementId);

        if ($index !== null) {
            // Ensure the validation structure exists
            if (! isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }

            if (! isset($elements[$index]['validation']['values'])) {
                $elements[$index]['validation']['values'] = [];
            }

            $elements[$index]['validation']['values'][$rule] = $value;
        }
    }


}
