<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

use App\Enums\FormElementType;

/**
 * Service for providing field-specific validation rules and categories for form elements.
 */
class FieldValidationService
{
    /**
     * Get relevant validation rules for a specific field type.
     */
    public function getRelevantRules(string $fieldType): array
    {
        return match ($fieldType) {
            FormElementType::TEXT->value => $this->getTextRules(),
            FormElementType::TEXTAREA->value => $this->getTextareaRules(),
            FormElementType::EMAIL->value => $this->getEmailRules(),
            FormElementType::SELECT->value => $this->getSelectRules(),
            FormElementType::CHECKBOX->value => $this->getCheckboxRules(),
            FormElementType::RADIO->value => $this->getRadioRules(),
            FormElementType::DATE->value => $this->getDateRules(),
            FormElementType::NUMBER->value => $this->getNumberRules(),
            FormElementType::PASSWORD->value => $this->getPasswordRules(),
            FormElementType::FILE->value => $this->getFileRules(),
            default => $this->getDefaultRules(),
        };
    }

    /**
     * Get validation rules for text input fields.
     */
    private function getTextRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'min' => [
                'label' => 'Minimum Length',
                'description' => 'Must be at least X characters',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Length',
            ],
            'max' => [
                'label' => 'Maximum Length',
                'description' => 'Must be no more than X characters',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Length',
            ],
            'alpha' => [
                'label' => 'Letters Only',
                'description' => 'Must contain only letters',
                'rule' => 'alpha',
                'icon' => 'document-text',
                'has_value' => false,
                'category' => 'Format',
            ],
            'alpha_num' => [
                'label' => 'Letters & Numbers',
                'description' => 'Must contain only letters and numbers',
                'rule' => 'alpha_num',
                'icon' => 'hashtag',
                'has_value' => false,
                'category' => 'Format',
            ],
            'alpha_dash' => [
                'label' => 'Letters, Numbers & Dashes',
                'description' => 'Must contain only letters, numbers, dashes and underscores',
                'rule' => 'alpha_dash',
                'icon' => 'minus',
                'has_value' => false,
                'category' => 'Format',
            ],
            'regex' => [
                'label' => 'Custom Pattern',
                'description' => 'Must match a specific pattern',
                'rule' => 'regex',
                'icon' => 'code-bracket',
                'has_value' => true,
                'category' => 'Advanced',
            ],
            'url' => [
                'label' => 'Valid URL',
                'description' => 'Must be a valid URL',
                'rule' => 'url',
                'icon' => 'link',
                'has_value' => false,
                'category' => 'Format',
            ],
        ];
    }

    /**
     * Get validation rules for textarea fields.
     */
    private function getTextareaRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'min' => [
                'label' => 'Minimum Length',
                'description' => 'Must be at least X characters',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Length',
            ],
            'max' => [
                'label' => 'Maximum Length',
                'description' => 'Must be no more than X characters',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Length',
            ],
        ];
    }

    /**
     * Get validation rules for email fields.
     */
    private function getEmailRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'email' => [
                'label' => 'Valid Email',
                'description' => 'Must be a valid email address',
                'rule' => 'email',
                'icon' => 'envelope',
                'has_value' => false,
                'category' => 'Format',
            ],
            'max' => [
                'label' => 'Maximum Length',
                'description' => 'Must be no more than X characters',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Length',
            ],
        ];
    }

    /**
     * Get validation rules for select fields.
     */
    private function getSelectRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
        ];
    }

    /**
     * Get validation rules for checkbox fields.
     */
    private function getCheckboxRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be checked',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
        ];
    }

    /**
     * Get validation rules for radio fields.
     */
    private function getRadioRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
        ];
    }

    /**
     * Get validation rules for date fields.
     */
    private function getDateRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'date' => [
                'label' => 'Valid Date',
                'description' => 'Must be a valid date',
                'rule' => 'date',
                'icon' => 'calendar',
                'has_value' => false,
                'category' => 'Format',
            ],
            'date_after' => [
                'label' => 'Date After',
                'description' => 'Must be after a specific date',
                'rule' => 'date_after',
                'icon' => 'calendar-days',
                'has_value' => true,
                'category' => 'Range',
            ],
            'date_before' => [
                'label' => 'Date Before',
                'description' => 'Must be before a specific date',
                'rule' => 'date_before',
                'icon' => 'calendar-days',
                'has_value' => true,
                'category' => 'Range',
            ],
            'date_after_today' => [
                'label' => 'Future Date',
                'description' => 'Must be a future date',
                'rule' => 'date_after:today',
                'icon' => 'calendar-days',
                'has_value' => false,
                'category' => 'Range',
            ],
            'date_before_today' => [
                'label' => 'Past Date',
                'description' => 'Must be a past date',
                'rule' => 'date_before:today',
                'icon' => 'calendar-days',
                'has_value' => false,
                'category' => 'Range',
            ],
        ];
    }

    /**
     * Get validation rules for number fields.
     */
    private function getNumberRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'numeric' => [
                'label' => 'Numeric',
                'description' => 'Must contain only numbers',
                'rule' => 'numeric',
                'icon' => 'calculator',
                'has_value' => false,
                'category' => 'Format',
            ],
            'integer' => [
                'label' => 'Integer',
                'description' => 'Must be a whole number (no decimals)',
                'rule' => 'integer',
                'icon' => 'hashtag',
                'has_value' => false,
                'category' => 'Format',
            ],
            'min_value' => [
                'label' => 'Minimum Value',
                'description' => 'Must be at least X (for numbers)',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Range',
            ],
            'max_value' => [
                'label' => 'Maximum Value',
                'description' => 'Must be no more than X (for numbers)',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Range',
            ],
            'positive' => [
                'label' => 'Positive Number',
                'description' => 'Must be greater than zero',
                'rule' => 'gt:0',
                'icon' => 'arrow-up',
                'has_value' => false,
                'category' => 'Range',
            ],
            'negative' => [
                'label' => 'Negative Number',
                'description' => 'Must be less than zero',
                'rule' => 'lt:0',
                'icon' => 'arrow-down',
                'has_value' => false,
                'category' => 'Range',
            ],
        ];
    }

    /**
     * Get validation rules for password fields.
     */
    private function getPasswordRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'min' => [
                'label' => 'Minimum Length',
                'description' => 'Must be at least X characters',
                'rule' => 'min',
                'icon' => 'arrow-down',
                'has_value' => true,
                'category' => 'Length',
            ],
            'max' => [
                'label' => 'Maximum Length',
                'description' => 'Must be no more than X characters',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Length',
            ],
            'confirmed' => [
                'label' => 'Password Confirmation',
                'description' => 'Must match the password confirmation field',
                'rule' => 'confirmed',
                'icon' => 'check-circle',
                'has_value' => false,
                'category' => 'Security',
            ],
        ];
    }

    /**
     * Get validation rules for file fields.
     */
    private function getFileRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
            'file' => [
                'label' => 'Valid File',
                'description' => 'Must be a valid file upload',
                'rule' => 'file',
                'icon' => 'document',
                'has_value' => false,
                'category' => 'Format',
            ],
            'image' => [
                'label' => 'Image File',
                'description' => 'Must be a valid image file',
                'rule' => 'image',
                'icon' => 'photo',
                'has_value' => false,
                'category' => 'Format',
            ],
            'mimes' => [
                'label' => 'File Type',
                'description' => 'Must be one of the specified file types',
                'rule' => 'mimes',
                'icon' => 'document-text',
                'has_value' => true,
                'category' => 'Format',
            ],
            'max_file_size' => [
                'label' => 'Maximum File Size',
                'description' => 'File size must not exceed X KB/MB',
                'rule' => 'max',
                'icon' => 'arrow-up',
                'has_value' => true,
                'category' => 'Size',
            ],
        ];
    }

    /**
     * Get default validation rules (fallback).
     */
    private function getDefaultRules(): array
    {
        return [
            'required' => [
                'label' => 'Required',
                'description' => 'Field must be filled out',
                'rule' => 'required',
                'icon' => 'exclamation-triangle',
                'has_value' => false,
                'category' => 'Basic',
            ],
        ];
    }

    /**
     * Get validation rules grouped by category for a field type.
     */
    public function getRelevantRulesByCategory(string $fieldType): array
    {
        $rules = $this->getRelevantRules($fieldType);
        $grouped = [];

        foreach ($rules as $key => $rule) {
            $category = $rule['category'] ?? 'Other';
            $grouped[$category][$key] = $rule;
        }

        return $grouped;
    }

    /**
     * Get available categories for a field type.
     */
    public function getAvailableCategories(string $fieldType): array
    {
        $rules = $this->getRelevantRules($fieldType);
        $categories = [];

        foreach ($rules as $rule) {
            $category = $rule['category'] ?? 'Other';
            if (! in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }
}
