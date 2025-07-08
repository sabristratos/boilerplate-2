<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\SettingType;
use Illuminate\Support\Collection;

/**
 * Interface for settings manager operations.
 *
 * This interface defines the contract for application settings management,
 * including CRUD operations, caching, and settings retrieval.
 */
interface SettingsManagerInterface
{
    /**
     * Get a setting value by key.
     *
     * @param string $key The setting key
     * @param mixed $default The default value if setting doesn't exist
     * @return mixed The setting value
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set a setting value.
     *
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @param SettingType $type The setting type
     * @return bool True if the setting was saved successfully
     */
    public function set(string $key, mixed $value, SettingType $type = SettingType::STRING): bool;

    /**
     * Check if a setting exists.
     *
     * @param string $key The setting key
     * @return bool True if the setting exists
     */
    public function has(string $key): bool;

    /**
     * Delete a setting.
     *
     * @param string $key The setting key
     * @return bool True if the setting was deleted successfully
     */
    public function delete(string $key): bool;

    /**
     * Get all settings.
     *
     * @return Collection The collection of all settings
     */
    public function all(): Collection;

    /**
     * Get settings by group.
     *
     * @param string $group The settings group
     * @return Collection The collection of settings in the group
     */
    public function getGroup(string $group): Collection;

    /**
     * Get settings by type.
     *
     * @param SettingType $type The setting type
     * @return Collection The collection of settings of the specified type
     */
    public function getByType(SettingType $type): Collection;

    /**
     * Set multiple settings at once.
     *
     * @param array<string, mixed> $settings Array of key-value pairs
     * @param SettingType $type The setting type for all settings
     * @return bool True if all settings were saved successfully
     */
    public function setMultiple(array $settings, SettingType $type = SettingType::STRING): bool;

    /**
     * Clear the settings cache.
     *
     * @return bool True if the cache was cleared successfully
     */
    public function clearCache(): bool;

    /**
     * Refresh the settings cache.
     *
     * @return bool True if the cache was refreshed successfully
     */
    public function refreshCache(): bool;

    /**
     * Get setting metadata.
     *
     * @param string $key The setting key
     * @return array<string, mixed>|null The setting metadata or null if not found
     */
    public function getMetadata(string $key): ?array;

    /**
     * Set setting metadata.
     *
     * @param string $key The setting key
     * @param array<string, mixed> $metadata The metadata to set
     * @return bool True if the metadata was saved successfully
     */
    public function setMetadata(string $key, array $metadata): bool;

    /**
     * Get settings with their metadata.
     *
     * @return Collection The collection of settings with metadata
     */
    public function allWithMetadata(): Collection;

    /**
     * Validate a setting value.
     *
     * @param string $key The setting key
     * @param mixed $value The value to validate
     * @return bool True if the value is valid
     */
    public function validate(string $key, mixed $value): bool;

    /**
     * Get validation rules for a setting.
     *
     * @param string $key The setting key
     * @return array<string> Array of validation rules
     */
    public function getValidationRules(string $key): array;

    /**
     * Get setting description.
     *
     * @param string $key The setting key
     * @return string|null The setting description or null if not found
     */
    public function getDescription(string $key): ?string;

    /**
     * Set setting description.
     *
     * @param string $key The setting key
     * @param string $description The description to set
     * @return bool True if the description was saved successfully
     */
    public function setDescription(string $key, string $description): bool;
} 