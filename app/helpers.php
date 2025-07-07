<?php

declare(strict_types=1);

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

if (! function_exists('formatRevisionValue')) {
    /**
     * Helper function to format revision field values for display.
     *
     * @param  string  $field  The field name
     * @param  mixed  $value  The field value
     * @param  string|null  $modelType  The model type (e.g., 'Form', 'Page')
     * @return string HTML formatted value
     */
    function formatRevisionValue(string $field, $value, ?string $modelType = null): string
    {
        // Handle null values
        if ($value === null) {
            return '<span class="text-gray-400 italic">Empty</span>';
        }

        // Handle empty arrays
        if (is_array($value) && empty($value)) {
            return '<span class="text-gray-400 italic">Empty</span>';
        }

        // Handle translatable fields
        if (is_array($value) && array_keys($value) !== range(0, count($value) - 1)) {
            $formatted = [];
            foreach ($value as $locale => $translation) {
                if (! empty($translation)) {
                    $formatted[] = "<strong>{$locale}:</strong> ".htmlspecialchars($translation);
                }
            }

            return ! empty($formatted) ? implode('<br>', $formatted) : '<span class="text-gray-400 italic">Empty</span>';
        }

        // Handle arrays (like form elements, settings)
        if (is_array($value)) {
            if (count($value) === 0) {
                return '<span class="text-gray-400 italic">Empty</span>';
            }

            // Special handling for form elements
            if ($field === 'elements') {
                $elementTypes = collect($value)->pluck('type')->filter()->unique();
                if ($elementTypes->count() > 0) {
                    return '<span class="text-blue-600">'.$elementTypes->count().' element(s): '.$elementTypes->implode(', ').'</span>';
                }
            }

            // Special handling for settings
            if ($field === 'settings') {
                $settingKeys = array_keys($value);
                if (count($settingKeys) > 0) {
                    return '<span class="text-purple-600">'.count($settingKeys).' setting(s): '.implode(', ', $settingKeys).'</span>';
                }
            }

            // For other arrays, show count
            return '<span class="text-gray-600">'.count($value).' item(s)</span>';
        }

        // Handle boolean values
        if (is_bool($value)) {
            return $value ? '<span class="text-green-600">Yes</span>' : '<span class="text-red-600">No</span>';
        }

        // Handle long strings
        if (is_string($value) && strlen($value) > 100) {
            return '<span class="text-gray-600">'.htmlspecialchars(substr($value, 0, 100)).'...</span>';
        }

        // Default: return as string
        return '<span class="text-gray-900">'.htmlspecialchars((string) $value).'</span>';
    }
}

if (! function_exists('getRevisionFieldLabel')) {
    /**
     * Get human-readable field names for revision display.
     *
     * @param  string  $field  The field name
     * @return string Human-readable field label
     */
    function getRevisionFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Name',
            'title' => 'Title',
            'slug' => 'Slug',
            'elements' => 'Form Elements',
            'settings' => 'Settings',
            'data' => 'Content Data',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'og_title' => 'Open Graph Title',
            'og_description' => 'Open Graph Description',
            'og_image' => 'Open Graph Image',
            'twitter_title' => 'Twitter Title',
            'twitter_description' => 'Twitter Description',
            'twitter_image' => 'Twitter Image',
            'twitter_card_type' => 'Twitter Card Type',
            'canonical_url' => 'Canonical URL',
            'structured_data' => 'Structured Data',
            'no_index' => 'No Index',
            'no_follow' => 'No Follow',
            'no_archive' => 'No Archive',
            'no_snippet' => 'No Snippet',
            'visible' => 'Visibility',
            'order' => 'Order',
            'type' => 'Type',
            'page_id' => 'Page ID',
            'user_id' => 'User ID',
            'status' => 'Status',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
