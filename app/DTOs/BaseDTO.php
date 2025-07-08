<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Base Data Transfer Object class.
 *
 * This abstract class provides common functionality for all DTOs,
 * including JSON serialization, array conversion, and validation.
 * All DTOs should extend this class to ensure consistency.
 */
abstract class BaseDTO implements Arrayable, Jsonable, JsonSerializable, \Stringable
{
    /**
     * Get the DTO as an array.
     *
     * @return array<string, mixed> The DTO data as array
     */
    abstract public function toArray(): array;

    /**
     * Get the DTO as JSON.
     *
     * @param int $options JSON encoding options
     * @return string The JSON representation
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Get the DTO for JSON serialization.
     *
     * @return array<string, mixed> The JSON serializable data
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    abstract public function validate(): array;

    /**
     * Check if the DTO is valid.
     *
     * @return bool True if the DTO is valid
     */
    public function isValid(): bool
    {
        return $this->validate() === [];
    }

    /**
     * Get validation errors as a string.
     *
     * @return string The validation errors as a string
     */
    public function getValidationErrorsAsString(): string
    {
        $errors = $this->validate();
        
        if ($errors === []) {
            return '';
        }

        $errorMessages = [];
        foreach ($errors as $field => $message) {
            if (is_array($message)) {
                foreach ($message as $msg) {
                    $errorMessages[] = "{$field}: {$msg}";
                }
            } else {
                $errorMessages[] = "{$field}: {$message}";
            }
        }

        return implode(', ', $errorMessages);
    }

    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return static A new DTO with the changes applied
     */
    abstract public function with(array $changes): static;

    /**
     * Convert the DTO to a string representation.
     *
     * @return string The string representation
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Get a debug representation of the DTO.
     *
     * @return array<string, mixed> The debug data
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }
} 