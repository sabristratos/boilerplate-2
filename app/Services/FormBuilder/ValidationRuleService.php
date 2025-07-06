<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Enums\FormElementType;

class ValidationRuleService
{
    /**
     * Get all available validation rules from config
     */
    public function getAllRules(): array
    {
        return config('forms.validation.rules', []);
    }

    /**
     * Get default validation messages from config
     */
    public function getDefaultMessages(): array
    {
        return config('forms.validation.default_messages', []);
    }

    /**
     * Get relevant validation rules for a specific field type
     */
    public function getRelevantRules(string $fieldType): array
    {
        $allRules = $this->getAllRules();
        $fieldRules = $this->getFieldTypeRules($fieldType);

        $relevantRules = [];
        foreach ($fieldRules as $ruleKey) {
            if (isset($allRules[$ruleKey])) {
                $relevantRules[$ruleKey] = $allRules[$ruleKey];
            }
        }

        return $relevantRules;
    }

    /**
     * Get validation rules grouped by category for a field type
     */
    public function getRelevantRulesByCategory(string $fieldType): array
    {
        $relevantRules = $this->getRelevantRules($fieldType);
        $groupedRules = [];

        foreach ($relevantRules as $ruleKey => $rule) {
            $category = $rule['category'] ?? 'Other';
            $groupedRules[$category][$ruleKey] = $rule;
        }

        return $groupedRules;
    }

    /**
     * Get available categories for a field type
     */
    public function getAvailableCategories(string $fieldType): array
    {
        $groupedRules = $this->getRelevantRulesByCategory($fieldType);

        return array_keys($groupedRules);
    }

    /**
     * Get field-specific validation rules
     */
    private function getFieldTypeRules(string $fieldType): array
    {
        return match ($fieldType) {
            FormElementType::TEXT->value => [
                'required', 'min', 'max', 'alpha', 'alpha_num', 'alpha_dash', 'regex', 'url',
            ],
            FormElementType::TEXTAREA->value => [
                'required', 'min', 'max',
            ],
            FormElementType::EMAIL->value => [
                'required', 'email', 'max',
            ],
            FormElementType::SELECT->value => [
                'required',
            ],
            FormElementType::CHECKBOX->value => [
                'required',
            ],
            FormElementType::RADIO->value => [
                'required',
            ],
            FormElementType::DATE->value => [
                'required', 'date', 'date_after', 'date_before',
            ],
            FormElementType::NUMBER->value => [
                'required', 'numeric', 'min_value', 'max_value',
            ],
            FormElementType::PASSWORD->value => [
                'required', 'min', 'max', 'confirmed',
            ],
            FormElementType::FILE->value => [
                'required', 'file', 'image', 'mimes', 'max_file_size',
            ],
            default => ['required']
        };
    }

    /**
     * Generate validation rules for an element
     */
    public function generateRules(array $element): array
    {
        $rules = [];
        $validation = $element['validation'] ?? [];
        $selectedRules = $validation['rules'] ?? [];
        $ruleValues = $validation['values'] ?? [];
        $allRules = $this->getAllRules();

        foreach ($selectedRules as $ruleKey) {
            if (isset($allRules[$ruleKey])) {
                $rule = $allRules[$ruleKey];
                $ruleString = $rule['rule'];

                // Add value if the rule requires it
                if (($rule['has_value'] ?? false) && isset($ruleValues[$ruleKey])) {
                    $ruleString .= ':'.$ruleValues[$ruleKey];
                }

                $rules[] = $ruleString;
            }
        }

        return $rules;
    }

    /**
     * Generate validation messages for an element
     */
    public function generateMessages(array $element): array
    {
        $messages = [];
        $validation = $element['validation'] ?? [];
        $selectedRules = $validation['rules'] ?? [];
        $customMessages = $validation['messages'] ?? [];
        $ruleValues = $validation['values'] ?? [];
        $allRules = $this->getAllRules();
        $defaultMessages = $this->getDefaultMessages();

        foreach ($selectedRules as $ruleKey) {
            if (isset($allRules[$ruleKey])) {
                $rule = $allRules[$ruleKey];
                $fieldName = $element['properties']['label'] ?? 'field';

                // Use custom message if provided, otherwise generate default
                if (isset($customMessages[$ruleKey]) && ! empty($customMessages[$ruleKey])) {
                    $messages[$ruleKey] = $customMessages[$ruleKey];
                } else {
                    // Generate default message
                    $messages[$ruleKey] = $this->generateDefaultMessage($rule, $fieldName, $ruleValues[$ruleKey] ?? null, $defaultMessages);
                }
            }
        }

        return $messages;
    }

    /**
     * Generate default validation message for a rule
     */
    private function generateDefaultMessage(array $rule, string $fieldName, ?string $value = null, array $defaultMessages = []): string
    {
        $fieldName = strtolower($fieldName);

        // Try to get localized message first
        $localizedMessage = __("forms.validation.{$rule['rule']}", ['field' => $fieldName, 'value' => $value]);

        // Fall back to config message if no localization found
        if ($localizedMessage === "forms.validation.{$rule['rule']}") {
            $message = $defaultMessages[$rule['rule']] ?? 'The :field field is invalid.';
            $localizedMessage = str_replace([':field', ':value'], [$fieldName, $value], $message);
        }

        return $localizedMessage;
    }
}
