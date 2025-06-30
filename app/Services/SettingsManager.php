<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

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
     * Set a setting value.
     */
    public function set(string $key, mixed $value): void
    {
        $groupKey = $this->getGroupKey($key);
        $settingGroup = SettingGroup::where('key', $groupKey)->first();

        if (! $settingGroup) {
            throw new \InvalidArgumentException(__('settings.errors.group_not_found', ['groupKey' => $groupKey]));
        }

        $setting = Setting::firstOrNew(['key' => $key]);

        if (! $setting->exists) {
            $setting->setting_group_id = $settingGroup->id;
        }

        $setting->value = $value;
        $setting->save();

        $this->clearCache();
    }

    /**
     * Get all settings.
     */
    public function getAll(): array
    {
        return Cache::rememberForever($this->getCacheKey(), function () {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            return Setting::all()->keyBy('key')->toArray();
        });
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
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
}
