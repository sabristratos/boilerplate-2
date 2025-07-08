<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object for Form data.
 *
 * This DTO encapsulates all data related to a form, including
 * its name, elements, settings, and metadata. It provides
 * type-safe access to form properties and includes validation
 * and transformation methods.
 */
class FormDTO extends BaseDTO
{
    /**
     * Create a new FormDTO instance.
     *
     * @param int|null $id The form ID
     * @param int|null $userId The user ID who owns the form
     * @param array<string, string> $name The form name (translatable)
     * @param array<string, mixed> $elements The form elements configuration
     * @param array<string, mixed> $settings The form settings
     * @param Carbon|null $createdAt When the form was created
     * @param Carbon|null $updatedAt When the form was last updated
     * @param array<string, mixed>|null $userData The user data associated with the form
     */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $userId,
        public readonly array $name,
        public readonly array $elements,
        public readonly array $settings,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?array $userData = null,
    ) {
    }

    /**
     * Create a FormDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'] ?? null,
            name: $data['name'] ?? [],
            elements: $data['elements'] ?? [],
            settings: $data['settings'] ?? [],
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            userData: $data['user_data'] ?? null,
        );
    }

    /**
     * Create a FormDTO from a Form model.
     *
     * @param \App\Models\Form $form The form model
     * @return self The created DTO
     */
    public static function fromModel(\App\Models\Form $form): self
    {
        return new self(
            id: $form->id,
            userId: $form->user_id,
            name: $form->getTranslations('name'),
            elements: $form->elements ?? [],
            settings: $form->settings ?? [],
            createdAt: $form->created_at,
            updatedAt: $form->updated_at,
            userData: $form->user?->toArray(),
        );
    }

    /**
     * Create a FormDTO for creating a new form.
     *
     * @param array<string, string> $name The form name
     * @param array<string, mixed> $elements The form elements
     * @param array<string, mixed> $settings The form settings
     * @param int|null $userId The user ID
     * @return self The created DTO
     */
    public static function forCreation(
        array $name,
        array $elements = [],
        array $settings = [],
        ?int $userId = null,
    ): self {
        return new self(
            id: null,
            userId: $userId,
            name: $name,
            elements: $elements,
            settings: $settings,
        );
    }

    /**
     * Get the form name for a specific locale.
     *
     * @param string|null $locale The locale to get name for
     * @return string|null The translated name
     */
    public function getNameForLocale(?string $locale = null): ?string
    {
        if ($locale === null) {
            $locale = app()->getLocale() ?: config('app.fallback_locale', 'en');
        }

        return $this->name[$locale] ?? null;
    }



    /**
     * Check if the form has a name for a specific locale.
     *
     * @param string $locale The locale to check
     * @return bool True if name exists for the locale
     */
    public function hasNameForLocale(string $locale): bool
    {
        return isset($this->name[$locale]);
    }

    /**
     * Get all available locales for this form.
     *
     * @return array<string> The available locales
     */
    public function getAvailableLocales(): array
    {
        return array_keys($this->name);
    }

    /**
     * Get a specific element by ID.
     *
     * @param string $elementId The element ID
     * @return array<string, mixed>|null The element data
     */
    public function getElement(string $elementId): ?array
    {
        foreach ($this->elements as $element) {
            if (($element['id'] ?? '') === $elementId) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Get all elements of a specific type.
     *
     * @param string $type The element type
     * @return array<array<string, mixed>> The elements of the specified type
     */
    public function getElementsByType(string $type): array
    {
        return array_filter($this->elements, fn($element): bool => ($element['type'] ?? '') === $type);
    }

    /**
     * Get all element types used in this form.
     *
     * @return array<string> The unique element types
     */
    public function getElementTypes(): array
    {
        $types = array_map(fn($element): mixed => $element['type'] ?? '', $this->elements);
        return array_unique(array_filter($types));
    }

    /**
     * Get the number of elements in the form.
     *
     * @return int The number of elements
     */
    public function getElementCount(): int
    {
        return count($this->elements);
    }

    /**
     * Check if the form has any elements.
     *
     * @return bool True if the form has elements
     */
    public function hasElements(): bool
    {
        return $this->elements !== [];
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key The setting key
     * @param mixed $default The default value if key doesn't exist
     * @return mixed The setting value
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key The setting key
     * @return bool True if the setting exists
     */
    public function hasSetting(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * Get all required field names from the form elements.
     *
     * @return array<string> The required field names
     */
    public function getRequiredFields(): array
    {
        $requiredFields = [];

        foreach ($this->elements as $element) {
            $validation = $element['validation'] ?? [];
            $rules = $validation['rules'] ?? [];

            if (in_array('required', $rules, true)) {
                $fieldName = $this->generateFieldName($element);
                $requiredFields[] = $fieldName;
            }
        }

        return $requiredFields;
    }

    /**
     * Get all field names from the form elements.
     *
     * @return array<string> The field names
     */
    public function getFieldNames(): array
    {
        return array_map(fn($element): string => $this->generateFieldName($element), $this->elements);
    }

    /**
     * Check if the form has file upload elements.
     *
     * @return bool True if the form has file uploads
     */
    public function hasFileUploads(): bool
    {
        return $this->getElementsByType('file') !== [];
    }

    /**
     * Get the form as an array.
     *
     * @return array<string, mixed> The form data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'name' => $this->name,
            'elements' => $this->elements,
            'settings' => $this->settings,
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
            'user_data' => $this->userData,
        ];
    }

    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return static A new DTO with the changes applied
     */
    public function with(array $changes): static
    {
        return new self(
            id: $changes['id'] ?? $this->id,
            userId: $changes['user_id'] ?? $changes['userId'] ?? $this->userId,
            name: $changes['name'] ?? $this->name,
            elements: $changes['elements'] ?? $this->elements,
            settings: $changes['settings'] ?? $this->settings,
            createdAt: $changes['created_at'] ?? $changes['createdAt'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $changes['updatedAt'] ?? $this->updatedAt,
            userData: $changes['user_data'] ?? $changes['userData'] ?? $this->userData,
        );
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validate(): array
    {
        $validationService = app(\App\Services\DTOValidationService::class);
        
        // Get validation rules
        $rules = [
            'id' => 'nullable|integer|min:1',
            'user_id' => 'nullable|integer|min:1',
        ];
        
        // Add translatable field rules
        $rules = array_merge($rules, $validationService->getTranslatableFieldRules('name', true));
        
        // Add form elements rules
        $rules = array_merge($rules, $validationService->getFormElementsRules($this->elements));
        
        // Add settings validation
        $rules['settings'] = 'nullable|array';
        
        // Get custom messages and attributes
        $messages = $validationService->getCustomValidationMessages();
        $attributes = $validationService->getCustomAttributeNames();
        
        // Validate using the service
        $errors = $validationService->validateDTO($this, $rules, $messages, $attributes);
        
        // Add custom validation for form elements
        $elementErrors = $validationService->validateFormElements($this->elements);
        $errors = array_merge($errors, $elementErrors);
        
        // Add custom validation for translatable fields
        $translatableErrors = $validationService->validateTranslatableField($this->toArray(), 'name');
        
        return array_merge($errors, $translatableErrors);
    }

    /**
     * Generate a field name for an element.
     *
     * @param array<string, mixed> $element The element data
     * @return string The generated field name
     */
    private function generateFieldName(array $element): string
    {
        $fieldNameGenerator = app(\App\Services\FormBuilder\FieldNameGeneratorService::class);
        return $fieldNameGenerator->generateFieldName($element);
    }
} 