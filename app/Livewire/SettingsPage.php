<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Facades\Settings;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class SettingsPage extends Component
{
    use WithFileUploads;

    /**
     * The state of the settings.
     *
     * @var array
     */
    public array $state = [];

    /**
     * The initial state of the settings.
     *
     * @var array
     */
    public array $initialState = [];

    /**
     * The warning message for the confirmation modal.
     *
     * @var string
     */
    public string $confirmationWarning = '';

    /**
     * The current group.
     *
     * @var string
     */
    public string $group = 'general';

    /**
     * Temporary file uploads.
     *
     * @var array
     */
    public array $files = [];

    /**
     * Mount the component.
     *
     * @param string|null $group
     * @return void
     */
    public function mount(?string $group = null): void
    {
        // Set the group to the provided value or default to 'general'
        $this->group = $group ?? 'general';

        // Validate that the group exists and user has access to it
        $authorizedGroups = $this->getAuthorizedGroups();
        if (!$authorizedGroups->contains('key', $this->group)) {
            // If the group doesn't exist or user doesn't have access, redirect to the first available group
            $firstGroup = $authorizedGroups->first();
            if ($firstGroup) {
                $this->redirect(route('settings.group', ['group' => $firstGroup->key]));
            } else {
                abort(403, 'You do not have access to any settings groups.');
            }
        }

        // Load settings for this group
        $this->loadSettings();
        $this->initialState = $this->state;
    }

    /**
     * Load settings into state.
     *
     * @return void
     */
    protected function loadSettings(): void
    {
        $settingsConfig = Config::get('settings.settings', []);

        foreach ($settingsConfig as $key => $setting) {
            if (($setting['group'] ?? '') !== $this->group) {
                continue;
            }

            if (!$this->userCan($setting['permission'] ?? null)) {
                continue;
            }

            // For media fields, we don't store the value directly in the state.
            // The MediaUploader component will handle the media.
            if ($setting['type'] === SettingType::MEDIA->value) {
                continue;
            }

            $value = Settings::get($key);

            // If value is null, use the default value from the config
            if ($value === null && isset($setting['default'])) {
                $value = $setting['default'];
            }

            if ($setting['type'] === SettingType::REPEATER->value) {
                $value = is_array($value) ? $value : [];
            }

            data_set($this->state, $key, $value);
        }
    }

    /**
     * Save the settings.
     *
     * @return void
     */
    public function save(): void
    {
        $settingsConfig = Config::get('settings.settings', []);
        $warnings = [];

        $currentState = data_get($this->state, $this->group, []);
        $initialState = data_get($this->initialState, $this->group, []);

        foreach ($currentState as $key => $value) {
            if (data_get($initialState, $key) != $value) {
                $fullKey = $this->group . '.' . $key;
                if (isset($settingsConfig[$fullKey]['warning'])) {
                    $label = $settingsConfig[$fullKey]['label'];
                    $labelText = is_array($label) ? ($label[app()->getLocale()] ?? $label['en']) : $label;
                    $warnings[$labelText] = $settingsConfig[$fullKey]['warning'];
                }
            }
        }

        if (!empty($warnings)) {
            $warningHtml = '<p>'.__('Please confirm you want to save these changes:').'</p><ul class="mt-2 list-disc list-inside space-y-1">';
            foreach ($warnings as $label => $text) {
                $warningHtml .= "<li><strong>{$label}:</strong> {$text}</li>";
            }
            $warningHtml .= '</ul>';
            $this->confirmationWarning = $warningHtml;

            Flux::modal('confirm-save')->show();

            return;
        }

        $this->confirmedSave();
    }

    public function confirmedSave(): void
    {
        $settings = Config::get('settings.settings', []);
        $validationRules = [];
        $validationMessages = [];

        $currentState = data_get($this->state, $this->group, []);
        $initialState = data_get($this->initialState, $this->group, []);
        $changedData = [];

        // 1. Identify changed fields and build validation rules ONLY for them.
        foreach ($currentState as $key => $value) {
            if (data_get($initialState, $key) != $value) {
                $fullKey = $this->group . '.' . $key;
                $changedData[$fullKey] = $value;

                if (isset($settings[$fullKey]['rules'])) {
                    $validationRules['state.' . $fullKey] = $settings[$fullKey]['rules'];
                }
                if (isset($settings[$fullKey]['messages'])) {
                    foreach ($settings[$fullKey]['messages'] as $rule => $message) {
                        $validationMessages['state.' . $fullKey . '.' . $rule] = $message;
                    }
                }
            }
        }

        // 2. Validate ONLY the changed fields.
        if (!empty($validationRules)) {
            $this->validate($validationRules, $validationMessages);
        }

        // 3. Save the validated, changed data.
        $changedSettings = [];
        foreach ($changedData as $fullKey => $value) {
            // Handle file uploads
            if (isset($this->files[$fullKey]) && $this->files[$fullKey]) {
                $value = $this->handleFileUpload($fullKey);
            }

            Settings::set($fullKey, $value);
            $changedSettings[$fullKey] = $value;
        }

        // Show success message using Flux toast
        $groupLabel = $this->getAuthorizedGroups()->firstWhere('key', $this->group)->label ?? $this->group;
        $message = __(':group settings saved successfully.', ['group' => $groupLabel]);

        Flux::toast($message, variant: 'success');

        // Dispatch a global event to notify other components
        if (!empty($changedSettings)) {
            $this->dispatch('settings-updated', settings: $changedSettings);
        }

        // Close the modal
        Flux::modal('confirm-save')->close();

        // Update initial state
        $this->initialState = $this->state;
    }

    /**
     * Revert changes to the initial state.
     *
     * @return void
     */
    public function cancelChanges(): void
    {
        $this->state = $this->initialState;
    }

    /**
     * Handle file upload.
     *
     * @param string $key
     * @return string
     */
    protected function handleFileUpload(string $key): string
    {
        $file = $this->files[$key];
        $path = $file->store('public/settings');
        return str_replace('public/', 'storage/', $path);
    }

    /**
     * Clear the application cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Artisan::call('cache:clear');

        Flux::toast(__('messages.cache_cleared_successfully'), variant: 'success');
    }

    /**
     * Fix the language settings by resetting them to defaults.
     * This addresses issues with incorrect data structure in the database.
     *
     * @return void
     */
    public function fixLanguageSettings(): void
    {
        // Delete the incorrect setting row
        Setting::where('key', 'general.available_locales')->delete();

        // Run the settings:sync command to recreate the setting with the correct structure
        Artisan::call('settings:sync');

        // Reload the settings to update the UI
        $this->loadSettings();
        $this->initialState = $this->state;

        Flux::toast(__('Language settings have been reset to defaults.'), variant: 'success');
    }

    /**
     * Get the setting groups the user is authorized to see.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedGroups()
    {
        // Get all groups from config
        $groups = collect(config('settings.groups', []));

        // Filter groups based on user permissions
        return $groups->filter(function ($group, $key) {
            // Get all settings for this group
            $settingsInGroup = $this->getAuthorizedSettings($key);

            // The group should only be shown if there is at least one setting the user can see
            return $settingsInGroup->isNotEmpty();
        })->map(function ($group, $key) {
            return (object) [
                'key' => $key,
                'label' => is_array($group['label']) ? ($group['label'][app()->getLocale()] ?? $group['label']['en']) : $group['label'],
                'description' => is_array($group['description']) ? ($group['description'][app()->getLocale()] ?? $group['description']['en']) : $group['description'],
                'icon' => $this->getGroupIcon($key),
                'order_column' => $group['order_column'] ?? 99,
            ];
        })->sortBy('order_column');
    }

    /**
     * Get the settings for a group that the user is authorized to see.
     *
     * @param string $groupKey
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedSettings(string $groupKey)
    {
        $settings = collect(config('settings.settings', []));

        return $settings->filter(function ($setting, $key) use ($groupKey) {
            return ($setting['group'] ?? '') === $groupKey && $this->userCan($setting['permission'] ?? null);
        })->map(function ($settingData, $key) {
            // For media settings, we load the Setting model instance.
            if ($settingData['type'] === SettingType::MEDIA->value) {
                $setting = Setting::firstOrCreate(['key' => $key], $settingData);
            } else {
                $settingData['key'] = $key;
                $settingData['value'] = Settings::get($key);
                $settingData['label'] = is_array($settingData['label']) ? ($settingData['label'][app()->getLocale()] ?? $settingData['label']['en']) : $settingData['label'];
                $settingData['description'] = isset($settingData['description']) ? (is_array($settingData['description']) ? ($settingData['description'][app()->getLocale()] ?? $settingData['description']['en']) : $settingData['description']) : null;
                if (isset($settingData['callout']['text'])) {
                    $settingData['callout']['text'] = is_array($settingData['callout']['text']) ? ($settingData['callout']['text'][app()->getLocale()] ?? $settingData['callout']['text']['en']) : $settingData['callout']['text'];
                }
                $setting = (object) $settingData;
            }
            return $setting;
        });
    }

    /**
     * Check if the user can perform the given permission.
     *
     * @param string|null $permission
     * @return bool
     */
    protected function userCan(?string $permission): bool
    {
        if (!$permission) {
            return true;
        }

        // Check if the authenticated user has the required permission
        return Auth::user() && Auth::user()->can($permission);
    }

    /**
     * Get the icon for a group.
     *
     * @param string $groupKey
     * @return string
     */
    protected function getGroupIcon(string $groupKey): string
    {
        return match ($groupKey) {
            SettingGroupKey::GENERAL->value => 'cog',
            SettingGroupKey::APPEARANCE->value => 'paint-brush',
            SettingGroupKey::EMAIL->value => 'envelope',
            SettingGroupKey::SECURITY->value => 'shield-check',
            SettingGroupKey::SOCIAL->value => 'share',
            SettingGroupKey::ADVANCED->value => 'wrench',
            SettingGroupKey::CONTACT->value => 'phone',
            default => 'adjustments-horizontal',
        };
    }

    #[On('media-updated')]
    #[On('repeater-updated')]
    public function reloadSettings()
    {
        $this->loadSettings();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Get all authorized groups
        $groups = $this->getAuthorizedGroups();

        // Find the current group object
        $currentGroup = $groups->firstWhere('key', $this->group);

        // Get all settings for the current group
        $settings = $this->getAuthorizedSettings($this->group);

        return view('livewire.settings-page', [
            'groups' => $groups,
            'currentGroup' => $currentGroup,
            'settings' => $settings,
        ]);
    }
}
