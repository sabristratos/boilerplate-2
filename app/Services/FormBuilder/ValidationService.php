<?php

namespace App\Services\FormBuilder;

<<<<<<< HEAD
use App\Services\FormBuilder\ElementManager;

class ValidationService
{
    private ValidationRuleService $validationRuleService;
    private ElementManager $elementManager;

    public function __construct(ValidationRuleService $validationRuleService, ElementManager $elementManager)
=======
/**
 * Service for handling form element validation rules and messages.
 */
class ValidationService
{
    /** @var array */
    private array $availableRules;

    /** @var array */
    private array $defaultMessages;

    /** @var FieldValidationService */
    private FieldValidationService $fieldValidationService;

    /**
     * ValidationService constructor.
     *
     * @param FieldValidationService $fieldValidationService
     */
    public function __construct(FieldValidationService $fieldValidationService)
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    {
        $this->validationRuleService = $validationRuleService;
        $this->elementManager = $elementManager;
    }

    /**
     * Generate validation rules for an element.
     *
     * @param array|ElementDTO $element
     * @return array
     */
    public function generateRules(array|ElementDTO $element): array
    {
<<<<<<< HEAD
        return $this->validationRuleService->generateRules($element);
=======
        // Convert array to ElementDTO if needed
        if (is_array($element)) {
            $element = new ElementDTO($element);
        }

        $rules = [];
        $validation = $element->validation ?? [];
        $selectedRules = $validation['rules'] ?? [];
        $ruleValues = $validation['values'] ?? [];

        foreach ($selectedRules as $ruleKey) {
            if (isset($this->availableRules[$ruleKey])) {
                $rule = $this->availableRules[$ruleKey];
                $ruleString = $rule['rule'];

                // Add value if the rule requires it
                if (($rule['has_value'] ?? false) && isset($ruleValues[$ruleKey])) {
                    $ruleString .= ':'.$ruleValues[$ruleKey];
                }

                $rules[] = $ruleString;
            }
        }

        return $rules;
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    }

    /**
     * Generate validation messages for an element.
     *
     * @param array|ElementDTO $element
     * @return array
     */
    public function generateMessages(array|ElementDTO $element): array
    {
<<<<<<< HEAD
        return $this->validationRuleService->generateMessages($element);
=======
        // Convert array to ElementDTO if needed
        if (is_array($element)) {
            $element = new ElementDTO($element);
        }

        $messages = [];
        $validation = $element->validation ?? [];
        $selectedRules = $validation['rules'] ?? [];
        $customMessages = $validation['messages'] ?? [];
        $ruleValues = $validation['values'] ?? [];

        foreach ($selectedRules as $ruleKey) {
            if (isset($this->availableRules[$ruleKey])) {
                $rule = $this->availableRules[$ruleKey];
                $fieldName = $element->properties['label'] ?? 'field';

                // Use custom message if provided, otherwise generate default
                if (isset($customMessages[$ruleKey]) && ! empty($customMessages[$ruleKey])) {
                    $messages[$ruleKey] = $customMessages[$ruleKey];
                } else {
                    // Generate default message
                    $messages[$ruleKey] = $this->generateDefaultMessage($rule, $fieldName, $ruleValues[$ruleKey] ?? null);
                }
            }
        }

        return $messages;
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    }

    /**
     * Get all available validation rules.
     *
     * @return array
     */
    public function getAvailableRules(): array
    {
        return $this->validationRuleService->getAllRules();
    }

    /**
     * Get relevant validation rules for a specific field type.
     *
     * @param string $fieldType
     * @return array
     */
    public function getRelevantRules(string $fieldType): array
    {
        return $this->validationRuleService->getRelevantRules($fieldType);
    }

    /**
     * Get relevant validation rules grouped by category for a field type.
     *
     * @param string $fieldType
     * @return array
     */
    public function getRelevantRulesByCategory(string $fieldType): array
    {
        return $this->validationRuleService->getRelevantRulesByCategory($fieldType);
    }

    /**
     * Get available categories for a field type.
     *
     * @param string $fieldType
     * @return array
     */
    public function getAvailableCategories(string $fieldType): array
    {
        return $this->validationRuleService->getAvailableCategories($fieldType);
    }

    /**
     * Update validation rules for an element.
     *
     * @param array $elements
     * @param string $elementId
     * @param array $rules
     * @return void
     */
    public function updateValidationRules(array &$elements, string $elementId, array $rules): void
    {
<<<<<<< HEAD
        $index = $this->elementManager->findElementIndex($elements, $elementId);
=======
        $elementManager = new ElementManager(new ElementFactory());
        $index = $elementManager->findElementIndex($elements, $elementId);
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d

        if ($index !== null) {
            // Ensure the validation structure exists
            if (! isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }

            $elements[$index]['validation']['rules'] = $rules;
        }
    }

    /**
     * Update validation message for a specific rule.
     *
     * @param array $elements
     * @param string $elementId
     * @param string $rule
     * @param string $message
     * @return void
     */
    public function updateValidationMessage(array &$elements, string $elementId, string $rule, string $message): void
    {
<<<<<<< HEAD
        $index = $this->elementManager->findElementIndex($elements, $elementId);
=======
        $elementManager = new ElementManager(new ElementFactory());
        $index = $elementManager->findElementIndex($elements, $elementId);
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d

        if ($index !== null) {
            // Ensure the validation structure exists
            if (! isset($elements[$index]['validation'])) {
                $elements[$index]['validation'] = config('forms.elements.default_validation');
            }

            $elements[$index]['validation']['messages'][$rule] = $message;
        }
    }

    /**
     * Update validation rule value.
     *
     * @param array $elements
     * @param string $elementId
     * @param string $rule
     * @param string $value
     * @return void
     */
    public function updateValidationRuleValue(array &$elements, string $elementId, string $rule, string $value): void
    {
<<<<<<< HEAD
        $index = $this->elementManager->findElementIndex($elements, $elementId);
=======
        $elementManager = new ElementManager(new ElementFactory());
        $index = $elementManager->findElementIndex($elements, $elementId);
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d

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

<<<<<<< HEAD
=======
    /**
     * Generate default validation message for a rule.
     *
     * @param array $rule
     * @param string $fieldName
     * @param string|null $value
     * @return string
     */
    private function generateDefaultMessage(array $rule, string $fieldName, ?string $value = null): string
    {
        $fieldName = strtolower($fieldName);
        $message = $this->defaultMessages[$rule['rule']] ?? 'The :field field is invalid.';
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d

}
