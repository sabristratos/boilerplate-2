<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SettingType;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\Contracts\SettingsManagerInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SettingsManager implements SettingsManagerInterface
{
    /**
     * Check if a setting exists.
     */
    public function has(string $key): bool
    {
        $settings = $this->getAll();

        return isset($settings[$key]);
    }

    /**
     * Get a setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getAll();

        $value = $settings[$key]['value'] ?? null;
        $dbValueFound = isset($settings[$key]);

        if (! $dbValueFound) {
            $settingConfig = Config::get("settings.settings.{$key}");
            if (isset($settingConfig['config'])) {
                return Config::get($settingConfig['config'], $default);
            }

            return $settingConfig['default'] ?? $default;
        }

        return $value;
    }

    /**
     * Get a translated setting value.
     */
    public function getTranslation(string $key, string $locale, mixed $default = null): mixed
    {
        $setting = $this->get($key, $default);

        if (is_array($setting) && isset($setting[$locale])) {
            return $setting[$locale];
        }

        return $default;
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value, SettingType $type = SettingType::STRING): bool
    {
        // Use direct array access instead of dot notation to handle keys with dots
        $settingsConfig = Config::get('settings.settings', []);
        $settingConfig = $settingsConfig[$key] ?? null;

        if (! $settingConfig) {
            throw new \InvalidArgumentException("Setting configuration not found for key: {$key}");
        }

        // Validate permissions if specified
        if (isset($settingConfig['permission']) && (! auth()->user() || ! auth()->user()->can($settingConfig['permission']))) {
            throw new AuthorizationException('Insufficient permissions to modify this setting.');
        }

        // Validate value against rules if provided
        if (isset($settingConfig['rules'])) {
            $validator = Validator::make([$key => $value], [$key => $settingConfig['rules']]);
            if ($validator->fails()) {
                throw new \InvalidArgumentException($validator->errors()->first());
            }
        }

        $groupKey = $this->getGroupKey($key);
        $settingGroup = SettingGroup::where('key', $groupKey)->first();

        if (! $settingGroup) {
            throw new \InvalidArgumentException(__('settings.errors.group_not_found', ['groupKey' => $groupKey]));
        }

        $setting = Setting::firstOrNew(['key' => $key]);

        if (! $setting->exists) {
            $setting->setting_group_id = $settingGroup->id;
            $setting->type = $settingConfig['type'] ?? SettingType::TEXT->value;
            $setting->cast = $settingConfig['cast'] ?? 'string';
            $setting->label = $settingConfig['label'] ?? [];
            $setting->description = $settingConfig['description'] ?? null;
            $setting->permission = $settingConfig['permission'] ?? null;
            $setting->config_key = $settingConfig['config'] ?? null;
            $setting->rules = $settingConfig['rules'] ?? null;
            $setting->options = $this->processOptions($settingConfig['options'] ?? null, $key);
            $setting->subfields = $settingConfig['subfields'] ?? null;
            $setting->callout = $settingConfig['callout'] ?? null;
            $setting->default = $settingConfig['default'] ?? null;
            $setting->warning = $settingConfig['warning'] ?? null;
            $setting->order = $settingConfig['order'] ?? 0;
        }

        $setting->value = $value;
        $setting->save();

        $this->clearCache();
        
        return true;
    }

    /**
     * Set a translated setting value.
     */
    public function setTranslation(string $key, string $locale, mixed $value): bool
    {
        $currentValue = $this->get($key, []);

        if (! is_array($currentValue)) {
            $currentValue = [];
        }

        $currentValue[$locale] = $value;
        return $this->set($key, $currentValue);
    }

    /**
     * Get all settings.
     */
    public function getAll(): array
    {
        $cacheKey = $this->getCacheKey();

        if ($this->supportsCacheTags()) {
            return Cache::tags(['settings'])->remember($cacheKey, now()->addMinutes(30), fn(): array => $this->loadSettingsFromDatabase());
        }

        return Cache::remember($cacheKey, now()->addMinutes(30), fn(): array => $this->loadSettingsFromDatabase());
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): bool
    {
        try {
            if ($this->supportsCacheTags()) {
                \Illuminate\Support\Facades\Cache::tags(['settings'])->flush();
            } else {
                \Illuminate\Support\Facades\Cache::forget($this->getCacheKey());
            }
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get the cache key for settings.
     */
    protected function getCacheKey(): string
    {
        return Config::get('settings.cache_key', 'settings');
    }

    /**
     * Get the group key from a setting key.
     */
    protected function getGroupKey(string $key): string
    {
        $parts = explode('.', $key);

        if (count($parts) === 1) {
            return $key;
        }

        return $parts[0];
    }

    /**
     * Load settings from database.
     */
    protected function loadSettingsFromDatabase(): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        $settings = Setting::with('settingGroup')->get();
        $result = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            switch ($setting->cast) {
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    break;
                case 'integer':
                    $value = is_null($value) ? null : (int) $value;
                    break;
                case 'array':
                case 'json':
                    $value = is_string($value) ? json_decode($value, true) : $value;
                    break;
                    // Add more casts as needed
            }
            $result[$setting->key] = [
                'value' => $value,
                'type' => $setting->type,
                'cast' => $setting->cast,
                'label' => $setting->label,
                'description' => $setting->description,
                'permission' => $setting->permission,
                'config_key' => $setting->config_key,
                'rules' => $setting->rules,
                'options' => $this->processOptions($setting->options, $setting->key),
                'subfields' => $setting->subfields,
                'callout' => $setting->callout,
                'default' => $setting->default,
                'warning' => $setting->warning,
                'order' => $setting->order,
                'setting_group' => $setting->settingGroup,
            ];
        }

        return $result;
    }

    /**
     * Process options to handle dynamic options.
     */
    protected function processOptions($options, string $key): mixed
    {
        if (is_string($options) && str_starts_with($options, 'dynamic:')) {
            $dynamicKey = substr($options, 8); // Remove 'dynamic:' prefix

            return $this->getDynamicOptions($dynamicKey);
        }

        return $options;
    }

    /**
     * Get options for a specific setting.
     */
    public function getOptions(string $key): array
    {
        $settings = $this->getAll();
        $setting = $settings[$key] ?? null;

        if (! $setting) {
            // Try to get from config if not in database
            $settingConfig = Config::get("settings.settings.{$key}");
            if ($settingConfig && isset($settingConfig['options'])) {
                return $this->processOptions($settingConfig['options'], $key);
            }

            return [];
        }

        return $this->processOptions($setting['options'], $key);
    }

    /**
     * Get dynamic options for a setting.
     */
    public function getDynamicOptions(string $key): array
    {
        return match ($key) {
            'general.homepage' => \App\Models\Page::orderBy('title->'.app()->getLocale(), 'asc')
                ->pluck('title', 'id')
                ->map(fn($title, $id) => is_array($title) ? ($title[app()->getLocale()] ?? $title['en'] ?? 'Untitled') : $title)
                ->toArray(),
            default => [],
        };
    }

    /**
     * Check if the current cache store supports tagging.
     */
    protected function supportsCacheTags(): bool
    {
        try {
            $store = Cache::getStore();

            return method_exists($store, 'tags');
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Delete a setting.
     *
     * @param string $key The setting key
     * @return bool True if the setting was deleted successfully
     */
    public function delete(string $key): bool
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $setting->delete();
            $this->clearCache();
            return true;
        }
        return false;
    }

    /**
     * Get all settings.
     *
     * @return Collection The collection of all settings
     */
    public function all(): Collection
    {
        return collect($this->getAll());
    }

    /**
     * Get settings by group.
     *
     * @param string $group The settings group
     * @return Collection The collection of settings in the group
     */
    public function getGroup(string $group): Collection
    {
        $allSettings = $this->getAll();
        return collect($allSettings)->filter(fn($setting, $key): bool => str_starts_with((string) $key, $group . '.'));
    }

    /**
     * Get settings by type.
     *
     * @param SettingType $type The setting type
     * @return Collection The collection of settings of the specified type
     */
    public function getByType(SettingType $type): Collection
    {
        $allSettings = $this->getAll();
        return collect($allSettings)->filter(fn(array $setting): bool => $setting['type'] === $type->value);
    }

    /**
     * Set multiple settings at once.
     *
     * @param array<string, mixed> $settings Array of key-value pairs
     * @param SettingType $type The setting type for all settings
     * @return bool True if all settings were saved successfully
     */
    public function setMultiple(array $settings, SettingType $type = SettingType::STRING): bool
    {
        try {
            foreach ($settings as $key => $value) {
                $this->set($key, $value, $type);
            }
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Refresh the settings cache.
     *
     * @return bool True if the cache was refreshed successfully
     */
    public function refreshCache(): bool
    {
        try {
            $this->clearCache();
            $this->getAll(); // This will rebuild the cache
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get setting metadata.
     *
     * @param string $key The setting key
     * @return array<string, mixed>|null The setting metadata or null if not found
     */
    public function getMetadata(string $key): ?array
    {
        $settings = $this->getAll();
        $setting = $settings[$key] ?? null;
        
        if (!$setting) {
            return null;
        }

        return [
            'type' => $setting['type'],
            'cast' => $setting['cast'],
            'label' => $setting['label'],
            'description' => $setting['description'],
            'permission' => $setting['permission'],
            'config_key' => $setting['config_key'],
            'rules' => $setting['rules'],
            'options' => $setting['options'],
            'subfields' => $setting['subfields'],
            'callout' => $setting['callout'],
            'default' => $setting['default'],
            'warning' => $setting['warning'],
            'order' => $setting['order'],
        ];
    }

    /**
     * Set setting metadata.
     *
     * @param string $key The setting key
     * @param array<string, mixed> $metadata The metadata to set
     * @return bool True if the metadata was saved successfully
     */
    public function setMetadata(string $key, array $metadata): bool
    {
        try {
            $setting = Setting::where('key', $key)->first();
            if (!$setting) {
                return false;
            }

            foreach ($metadata as $field => $value) {
                if (property_exists($setting, $field)) {
                    $setting->$field = $value;
                }
            }

            $setting->save();
            $this->clearCache();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get settings with their metadata.
     *
     * @return Collection The collection of settings with metadata
     */
    public function allWithMetadata(): Collection
    {
        return $this->all();
    }

    /**
     * Validate a setting value.
     *
     * @param string $key The setting key
     * @param mixed $value The value to validate
     * @return bool True if the value is valid
     */
    public function validate(string $key, mixed $value): bool
    {
        try {
            $rules = $this->getValidationRules($key);
            if ($rules === []) {
                return true;
            }

            $validator = Validator::make([$key => $value], [$key => $rules]);
            return !$validator->fails();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get validation rules for a setting.
     *
     * @param string $key The setting key
     * @return array<string> Array of validation rules
     */
    public function getValidationRules(string $key): array
    {
        $settings = $this->getAll();
        $setting = $settings[$key] ?? null;
        
        if (!$setting || !$setting['rules']) {
            return [];
        }

        return is_array($setting['rules']) ? $setting['rules'] : [$setting['rules']];
    }

    /**
     * Get setting description.
     *
     * @param string $key The setting key
     * @return string|null The setting description or null if not found
     */
    public function getDescription(string $key): ?string
    {
        $metadata = $this->getMetadata($key);
        return $metadata['description'] ?? null;
    }

    /**
     * Set setting description.
     *
     * @param string $key The setting key
     * @param string $description The description to set
     * @return bool True if the description was saved successfully
     */
    public function setDescription(string $key, string $description): bool
    {
        return $this->setMetadata($key, ['description' => $description]);
    }
}
