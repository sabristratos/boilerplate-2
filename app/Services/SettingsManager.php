<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SettingsManager
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
            if (isset($settingConfig['default'])) {
                return $settingConfig['default'];
            }

            return $default;
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
    public function set(string $key, mixed $value): void
    {
        // Use direct array access instead of dot notation to handle keys with dots
        $settingsConfig = Config::get('settings.settings', []);
        $settingConfig = $settingsConfig[$key] ?? null;

        if (! $settingConfig) {
            throw new \InvalidArgumentException("Setting configuration not found for key: {$key}");
        }

        // Validate permissions if specified
        if (isset($settingConfig['permission'])) {
            if (! auth()->user() || ! auth()->user()->can($settingConfig['permission'])) {
                throw new AuthorizationException('Insufficient permissions to modify this setting.');
            }
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
            $setting->type = $settingConfig['type'] ?? 'text';
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
    }

    /**
     * Set a translated setting value.
     */
    public function setTranslation(string $key, string $locale, mixed $value): void
    {
        $currentValue = $this->get($key, []);

        if (! is_array($currentValue)) {
            $currentValue = [];
        }

        $currentValue[$locale] = $value;
        $this->set($key, $currentValue);
    }

    /**
     * Get all settings.
     */
    public function getAll(): array
    {
        $cacheKey = $this->getCacheKey();

        if ($this->supportsCacheTags()) {
            return Cache::tags(['settings'])->remember($cacheKey, now()->addMinutes(30), function () {
                return $this->loadSettingsFromDatabase();
            });
        }

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return $this->loadSettingsFromDatabase();
        });
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        if ($this->supportsCacheTags()) {
            Cache::tags(['settings'])->flush();
        } else {
            Cache::forget($this->getCacheKey());
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
        switch ($key) {
            case 'general.homepage':
                return \App\Models\Page::orderBy('title->'.app()->getLocale(), 'asc')
                    ->pluck('title', 'id')
                    ->map(function ($title, $id) {
                        return is_array($title) ? ($title[app()->getLocale()] ?? $title['en'] ?? 'Untitled') : $title;
                    })
                    ->toArray();
            default:
                return [];
        }
    }

    /**
     * Check if the current cache store supports tagging.
     */
    protected function supportsCacheTags(): bool
    {
        try {
            $store = Cache::getStore();

            return method_exists($store, 'tags');
        } catch (\Exception $e) {
            return false;
        }
    }
}
