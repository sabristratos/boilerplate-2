<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\BaseDTO;
use App\Enums\FormElementType;
use App\Enums\PublishStatus;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Service for handling DTO validation logic.
 *
 * This service provides reusable validation logic for all DTOs,
 * including common validation rules, custom validation methods,
 * and consistent error handling.
 */
class DTOValidationService
{
    /**
     * Validate a DTO using Laravel's validator.
     *
     * @param BaseDTO $dto The DTO to validate
     * @param array<string, mixed> $rules The validation rules
     * @param array<string, string> $messages Custom validation messages
     * @param array<string, string> $attributes Custom attribute names
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validateDTO(BaseDTO $dto, array $rules, array $messages = [], array $attributes = []): array
    {
        try {
            Validator::make($dto->toArray(), $rules, $messages, $attributes)->validate();
            return [];
        } catch (ValidationException $e) {
            return $e->errors();
        }
    }

    /**
     * Get common validation rules for translatable fields.
     *
     * @param string $fieldName The field name
     * @param bool $required Whether the field is required
     * @param array<string> $locales The locales to validate
     * @return array<string, mixed> The validation rules
     */
    public function getTranslatableFieldRules(string $fieldName, bool $required = true, array $locales = ['en', 'fr']): array
    {
        $rules = [];
        
        foreach ($locales as $locale) {
            $rule = $required ? 'required|string|max:255' : 'nullable|string|max:255';
            $rules["{$fieldName}.{$locale}"] = $rule;
        }
        
        return $rules;
    }

    /**
     * Get validation rules for form elements.
     *
     * @param array<string, mixed> $elements The form elements
     * @return array<string, mixed> The validation rules
     */
    public function getFormElementsRules(array $elements): array
    {
        $rules = [];
        
        foreach ($elements as $index => $element) {
            $rules["elements.{$index}.id"] = 'required|string|max:255';
            $rules["elements.{$index}.type"] = 'required|string|in:' . implode(',', FormElementType::values());
            $rules["elements.{$index}.properties"] = 'required|array';
            $rules["elements.{$index}.validation"] = 'nullable|array';
            
            // Validate element-specific properties
            $elementType = $element['type'] ?? '';
            $rules = array_merge($rules, $this->getElementTypeSpecificRules($elementType, $index));
        }
        
        return $rules;
    }

    /**
     * Get element type-specific validation rules.
     *
     * @param string $elementType The element type
     * @param int $index The element index
     * @return array<string, mixed> The validation rules
     */
    private function getElementTypeSpecificRules(string $elementType, int $index): array
    {
        $rules = [];
        
        switch ($elementType) {
            case FormElementType::SELECT->value:
            case FormElementType::RADIO->value:
                $rules["elements.{$index}.properties.options"] = 'required|array|min:1';
                $rules["elements.{$index}.properties.options.*.label"] = 'required|string|max:255';
                $rules["elements.{$index}.properties.options.*.value"] = 'required|string|max:255';
                break;
                
            case FormElementType::FILE->value:
                $rules["elements.{$index}.properties.allowed_types"] = 'nullable|array';
                $rules["elements.{$index}.properties.max_size"] = 'nullable|integer|min:1|max:10240';
                break;
                
            case FormElementType::NUMBER->value:
                $rules["elements.{$index}.properties.min"] = 'nullable|numeric';
                $rules["elements.{$index}.properties.max"] = 'nullable|numeric';
                break;
                
            case FormElementType::TEXTAREA->value:
                $rules["elements.{$index}.properties.max_length"] = 'nullable|integer|min:1|max:10000';
                break;
        }
        
        return $rules;
    }

    /**
     * Get validation rules for user data.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getUserDataRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'role' => 'nullable|string|in:' . implode(',', UserRole::values()),
        ];
    }

    /**
     * Get validation rules for page data.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getPageDataRules(): array
    {
        return [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
            'status' => 'required|string|in:' . implode(',', PublishStatus::values()),
            'meta_title' => 'nullable|array',
            'meta_description' => 'nullable|array',
            'meta_keywords' => 'nullable|array',
            'og_title' => 'nullable|array',
            'og_description' => 'nullable|array',
            'og_image' => 'nullable|string|url',
            'twitter_title' => 'nullable|array',
            'twitter_description' => 'nullable|array',
            'twitter_image' => 'nullable|string|url',
            'twitter_card_type' => 'nullable|string|in:summary,summary_large_image,app,player',
            'canonical_url' => 'nullable|string|url',
            'structured_data' => 'nullable|array',
            'no_index' => 'boolean',
            'no_follow' => 'boolean',
            'no_archive' => 'boolean',
            'no_snippet' => 'boolean',
        ];
    }

    /**
     * Get validation rules for content block data.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getContentBlockDataRules(): array
    {
        return [
            'type' => 'required|string|max:255',
            'data' => 'nullable|array',
            'settings' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_visible' => 'boolean',
            'page_id' => 'required|integer|exists:pages,id',
        ];
    }

    /**
     * Get validation rules for media data.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getMediaDataRules(): array
    {
        return [
            'file' => 'required|file|max:10240',
            'name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'collection' => 'nullable|string|max:255',
            'disk' => 'nullable|string|max:255',
            'conversions' => 'nullable|array',
            'custom_properties' => 'nullable|array',
        ];
    }

    /**
     * Get validation rules for form submission data.
     *
     * @return array<string, mixed> The validation rules
     */
    public function getFormSubmissionDataRules(): array
    {
        return [
            'form_id' => 'required|integer|exists:forms,id',
            'data' => 'required|array',
            'ip_address' => 'nullable|string|ip',
            'user_agent' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Validate translatable fields.
     *
     * @param array<string, mixed> $data The data to validate
     * @param string $fieldName The field name
     * @param array<string> $locales The locales to validate
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validateTranslatableField(array $data, string $fieldName, array $locales = ['en', 'fr']): array
    {
        $errors = [];
        
        // Check if at least one locale has a value
        $hasValue = false;
        foreach ($locales as $locale) {
            if (!empty($data[$fieldName][$locale] ?? '')) {
                $hasValue = true;
                break;
            }
        }
        
        if (!$hasValue) {
            $errors[$fieldName] = __('validation.required', ['attribute' => $fieldName]);
        }
        
        // Validate each locale value
        foreach ($locales as $locale) {
            $value = $data[$fieldName][$locale] ?? '';
            if (!empty($value) && !is_string($value)) {
                $errors["{$fieldName}.{$locale}"] = __('validation.string', ['attribute' => "{$fieldName} ({$locale})"]);
            }
            
            if (!empty($value) && strlen((string) $value) > 255) {
                $errors["{$fieldName}.{$locale}"] = __('validation.max.string', ['attribute' => "{$fieldName} ({$locale})", 'max' => 255]);
            }
        }
        
        return $errors;
    }

    /**
     * Validate form elements structure.
     *
     * @param array<string, mixed> $elements The elements to validate
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validateFormElements(array $elements): array
    {
        $errors = [];
        
        if (!is_array($elements)) {
            $errors['elements'] = __('validation.array', ['attribute' => 'elements']);
            return $errors;
        }
        
        foreach ($elements as $index => $element) {
            if (!is_array($element)) {
                $errors["elements.{$index}"] = __('validation.array', ['attribute' => "element {$index}"]);
                continue;
            }
            
            // Validate required fields
            if (empty($element['id'] ?? '')) {
                $errors["elements.{$index}.id"] = __('validation.required', ['attribute' => "element {$index} ID"]);
            }
            
            if (empty($element['type'] ?? '')) {
                $errors["elements.{$index}.type"] = __('validation.required', ['attribute' => "element {$index} type"]);
            } elseif (!in_array($element['type'], FormElementType::values())) {
                $errors["elements.{$index}.type"] = __('validation.in', ['attribute' => "element {$index} type"]);
            }
            
            // Validate properties
            if (!isset($element['properties']) || !is_array($element['properties'])) {
                $errors["elements.{$index}.properties"] = __('validation.array', ['attribute' => "element {$index} properties"]);
            }
            
            // Validate element-specific properties
            $elementErrors = $this->validateElementProperties($element, $index);
            $errors = array_merge($errors, $elementErrors);
        }
        
        return $errors;
    }

    /**
     * Validate element-specific properties.
     *
     * @param array<string, mixed> $element The element data
     * @param int $index The element index
     * @return array<string, string> Validation errors, empty if valid
     */
    private function validateElementProperties(array $element, int $index): array
    {
        $errors = [];
        $type = $element['type'] ?? '';
        $properties = $element['properties'] ?? [];
        
        switch ($type) {
            case FormElementType::SELECT->value:
            case FormElementType::RADIO->value:
                if (empty($properties['options'] ?? [])) {
                    $errors["elements.{$index}.properties.options"] = __('validation.required', ['attribute' => 'options']);
                } elseif (!is_array($properties['options'])) {
                    $errors["elements.{$index}.properties.options"] = __('validation.array', ['attribute' => 'options']);
                } else {
                    foreach ($properties['options'] as $optionIndex => $option) {
                        if (empty($option['label'] ?? '')) {
                            $errors["elements.{$index}.properties.options.{$optionIndex}.label"] = __('validation.required', ['attribute' => 'option label']);
                        }
                        if (empty($option['value'] ?? '')) {
                            $errors["elements.{$index}.properties.options.{$optionIndex}.value"] = __('validation.required', ['attribute' => 'option value']);
                        }
                    }
                }
                break;
                
            case FormElementType::FILE->value:
                if (isset($properties['max_size']) && (!is_numeric($properties['max_size']) || $properties['max_size'] < 1)) {
                    $errors["elements.{$index}.properties.max_size"] = __('validation.integer', ['attribute' => 'max file size']);
                }
                break;
                
            case FormElementType::NUMBER->value:
                if (isset($properties['min']) && !is_numeric($properties['min'])) {
                    $errors["elements.{$index}.properties.min"] = __('validation.numeric', ['attribute' => 'minimum value']);
                }
                if (isset($properties['max']) && !is_numeric($properties['max'])) {
                    $errors["elements.{$index}.properties.max"] = __('validation.numeric', ['attribute' => 'maximum value']);
                }
                break;
        }
        
        return $errors;
    }

    /**
     * Get custom validation messages for DTOs.
     *
     * @return array<string, string> The custom validation messages
     */
    public function getCustomValidationMessages(): array
    {
        return [
            'title.required' => __('dto.validation.title_required'),
            'title.en.required' => __('dto.validation.title_required_en'),
            'name.required' => __('dto.validation.name_required'),
            'name.en.required' => __('dto.validation.name_required_en'),
            'slug.required' => __('dto.validation.slug_required'),
            'slug.regex' => __('dto.validation.slug_format'),
            'elements.required' => __('dto.validation.elements_required'),
            'elements.array' => __('dto.validation.elements_array'),
            'user_id.required' => __('dto.validation.user_id_required'),
            'user_id.integer' => __('dto.validation.user_id_integer'),
            'status.required' => __('dto.validation.status_required'),
            'status.in' => __('dto.validation.status_invalid'),
            'type.required' => __('dto.validation.type_required'),
            'type.in' => __('dto.validation.type_invalid'),
            'file.required' => __('dto.validation.file_required'),
            'file.file' => __('dto.validation.file_invalid'),
            'file.max' => __('dto.validation.file_too_large'),
            'form_id.required' => __('dto.validation.form_id_required'),
            'form_id.exists' => __('dto.validation.form_id_exists'),
            'data.required' => __('dto.validation.data_required'),
            'data.array' => __('dto.validation.data_array'),
        ];
    }

    /**
     * Get custom attribute names for validation messages.
     *
     * @return array<string, string> The custom attribute names
     */
    public function getCustomAttributeNames(): array
    {
        return [
            'title' => __('dto.attributes.title'),
            'name' => __('dto.attributes.name'),
            'slug' => __('dto.attributes.slug'),
            'elements' => __('dto.attributes.elements'),
            'user_id' => __('dto.attributes.user_id'),
            'status' => __('dto.attributes.status'),
            'type' => __('dto.attributes.type'),
            'file' => __('dto.attributes.file'),
            'form_id' => __('dto.attributes.form_id'),
            'data' => __('dto.attributes.data'),
        ];
    }
} 