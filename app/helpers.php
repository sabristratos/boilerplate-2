<?php

use App\Facades\Settings;
use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     * If false is passed as the default, we will return whether the key exists.
     */
    function setting(string|array|null $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return Settings::getAll();
        }

        if (is_array($key)) {
            foreach ($key as $k => $value) {
                Settings::set($k, $value);
            }

            return true;
        }

        // If the default is exactly boolean false, return whether the key exists
        if ($default === false) {
            return Settings::has($key);
        }

        return Settings::get($key, $default);
    }
}

if (! function_exists('setting_media_url')) {
    /**
     * Get the media URL for a setting.
     */
    function setting_media_url(string $key, string $collection = 'default', mixed $default = null): ?string
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $mediaUrl = $setting->getFirstMediaUrl($collection);

        return $mediaUrl ?: $default;
    }
}
