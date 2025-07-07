<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object for Form Submission data.
 *
 * This DTO encapsulates all data related to a form submission, including
 * the form data, metadata, and submission information. It provides
 * type-safe access to submission properties and includes validation
 * and transformation methods.
 */
class FormSubmissionDTO extends BaseDTO
{
    /**
     * Create a new FormSubmissionDTO instance.
     *
     * @param int|null $id The submission ID
     * @param int $formId The form ID this submission belongs to
     * @param array<string, mixed> $data The form submission data
     * @param string $ipAddress The IP address of the submitter
     * @param string $userAgent The user agent of the submitter
     * @param Carbon|null $createdAt When the submission was created
     * @param Carbon|null $updatedAt When the submission was last updated
     * @param array<string, mixed>|null $formData The form configuration data
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $formId,
        public readonly array $data,
        public readonly string $ipAddress,
        public readonly string $userAgent,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?array $formData = null,
    ) {
    }

    /**
     * Create a FormSubmissionDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            formId: $data['form_id'],
            data: $data['data'] ?? [],
            ipAddress: $data['ip_address'],
            userAgent: $data['user_agent'],
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            formData: $data['form_data'] ?? null,
        );
    }

    /**
     * Create a FormSubmissionDTO from a FormSubmission model.
     *
     * @param \App\Models\FormSubmission $submission The form submission model
     * @return self The created DTO
     */
    public static function fromModel(\App\Models\FormSubmission $submission): self
    {
        return new self(
            id: $submission->id,
            formId: $submission->form_id,
            data: $submission->data ?? [],
            ipAddress: $submission->ip_address,
            userAgent: $submission->user_agent,
            createdAt: $submission->created_at,
            updatedAt: $submission->updated_at,
            formData: $submission->form?->toArray(),
        );
    }

    /**
     * Create a FormSubmissionDTO for creating a new submission.
     *
     * @param int $formId The form ID
     * @param array<string, mixed> $data The form data
     * @param string $ipAddress The IP address
     * @param string $userAgent The user agent
     * @return self The created DTO
     */
    public static function forCreation(
        int $formId,
        array $data,
        string $ipAddress,
        string $userAgent,
    ): self {
        return new self(
            id: null,
            formId: $formId,
            data: $data,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }

    /**
     * Get a specific field value from the submission data.
     *
     * @param string $fieldName The field name
     * @param mixed $default The default value if field doesn't exist
     * @return mixed The field value
     */
    public function getFieldValue(string $fieldName, mixed $default = null): mixed
    {
        return $this->data[$fieldName] ?? $default;
    }

    /**
     * Check if a field exists in the submission data.
     *
     * @param string $fieldName The field name
     * @return bool True if the field exists
     */
    public function hasField(string $fieldName): bool
    {
        return isset($this->data[$fieldName]);
    }

    /**
     * Get all field names from the submission data.
     *
     * @return array<string> The field names
     */
    public function getFieldNames(): array
    {
        return array_keys($this->data);
    }

    /**
     * Get the submission data as a flat array of key-value pairs.
     *
     * @return array<string, mixed> The flattened data
     */
    public function getFlattenedData(): array
    {
        return $this->flattenArray($this->data);
    }

    /**
     * Get the submission data as a formatted string for display.
     *
     * @return string The formatted data
     */
    public function getFormattedData(): string
    {
        $formatted = [];
        
        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                $formatted[] = sprintf('%s: %s', $key, implode(', ', $value));
            } else {
                $formatted[] = sprintf('%s: %s', $key, (string) $value);
            }
        }
        
        return implode("\n", $formatted);
    }

    /**
     * Check if the submission contains sensitive data.
     *
     * @return bool True if sensitive data is present
     */
    public function containsSensitiveData(): bool
    {
        $sensitiveFields = ['password', 'credit_card', 'ssn', 'social_security'];
        
        foreach ($sensitiveFields as $field) {
            if ($this->hasField($field)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get a sanitized version of the submission data (removing sensitive information).
     *
     * @return array<string, mixed> The sanitized data
     */
    public function getSanitizedData(): array
    {
        $sensitiveFields = ['password', 'credit_card', 'ssn', 'social_security'];
        $sanitized = $this->data;
        
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '***REDACTED***';
            }
        }
        
        return $sanitized;
    }

    /**
     * Get the submission age in days.
     *
     * @return int|null The age in days, or null if no creation date
     */
    public function getAgeInDays(): ?int
    {
        if ($this->createdAt === null) {
            return null;
        }
        
        return $this->createdAt->diffInDays(now());
    }

    /**
     * Check if the submission is recent (within the last 24 hours).
     *
     * @return bool True if the submission is recent
     */
    public function isRecent(): bool
    {
        if ($this->createdAt === null) {
            return false;
        }
        
        return $this->createdAt->isAfter(now()->subDay());
    }

    /**
     * Get the submission as an array.
     *
     * @return array<string, mixed> The submission data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->formId,
            'data' => $this->data,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
            'form_data' => $this->formData,
        ];
    }



    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return self A new DTO with the changes applied
     */
    public function with(array $changes): self
    {
        return new self(
            id: $changes['id'] ?? $this->id,
            formId: $changes['form_id'] ?? $changes['formId'] ?? $this->formId,
            data: $changes['data'] ?? $this->data,
            ipAddress: $changes['ip_address'] ?? $changes['ipAddress'] ?? $this->ipAddress,
            userAgent: $changes['user_agent'] ?? $changes['userAgent'] ?? $this->userAgent,
            createdAt: $changes['created_at'] ?? $changes['createdAt'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $changes['updatedAt'] ?? $this->updatedAt,
            formData: $changes['form_data'] ?? $changes['formData'] ?? $this->formData,
        );
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->formId <= 0) {
            $errors['form_id'] = 'Valid form ID is required';
        }

        if (empty($this->data)) {
            $errors['data'] = 'Form data is required';
        }

        if (empty($this->ipAddress)) {
            $errors['ip_address'] = 'IP address is required';
        } elseif (! filter_var($this->ipAddress, FILTER_VALIDATE_IP)) {
            $errors['ip_address'] = 'Valid IP address is required';
        }

        if (empty($this->userAgent)) {
            $errors['user_agent'] = 'User agent is required';
        }

        return $errors;
    }



    /**
     * Flatten a nested array into a single level array.
     *
     * @param array<string, mixed> $array The array to flatten
     * @param string $prefix The prefix for nested keys
     * @return array<string, mixed> The flattened array
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }
} 