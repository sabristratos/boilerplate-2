<?php

namespace App\Livewire;

use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use App\Facades\Settings;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Traits\WithToastNotifications;
use Flux\Flux;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsPage extends Component
{
    use WithFileUploads, WithToastNotifications;

    /**
     * The state of the settings.
     */
    public array $state = [];

    /**
     * The initial state of the settings.
     */
    public array $initialState = [];

    /**
     * The warning message for the confirmation modal.
     */
    public string $confirmationWarning = '';

    /**
     * The current group.
     */
    public string $group = 'general';

    /**
     * Temporary file uploads.
     */
    public array $files = [];

    public SettingGroup $currentGroup;

    protected $listeners = [
        'repeater-updated' => 'onRepeaterUpdate',
    ];

    /**
     * Mount the component.
     */
    public function mount(?string $group = null): void
    {
        // Set the group to the provided value or default to 'general'
        $this->group = $group ?? 'general';
        $this->currentGroup = SettingGroup::where('key', $this->group)->firstOrFail();

        // Validate that the group exists and user has access to it
        $authorizedGroups = $this->getAuthorizedGroups();
        if ($authorizedGroups->doesntContain('key', $this->group)) {
            // If the group doesn't exist or user doesn't have access, redirect to the first available group
            $firstGroup = $authorizedGroups->first();
            if ($firstGroup) {
                $this->redirect(route('admin.settings.group', ['group' => $firstGroup->key]));
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
     */
    protected function loadSettings(): void
    {
        $settingsConfig = Config::get('settings.settings', []);

        foreach ($settingsConfig as $key => $setting) {
            if (($setting['group'] ?? '') !== $this->group) {
                continue;
            }

            if (! $this->userCan($setting['permission'] ?? null)) {
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

            // Extract the field name from the full key (remove group prefix)
            $fieldName = str_replace($this->group . '.', '', $key);
            data_set($this->state, $this->group . '.' . $fieldName, $value);
        }
    }

    /**
     * Save the settings.
     */
    public function save(): void
    {
        $settingsConfig = Config::get('settings.settings', []);
        $warnings = [];

        $currentState = data_get($this->state, $this->group, []);
        $initialState = data_get($this->initialState, $this->group, []);

        foreach ($currentState as $key => $value) {
            if (data_get($initialState, $key) != $value) {
                $fullKey = $this->group.'.'.$key;
                if (isset($settingsConfig[$fullKey]['warning'])) {
                    $label = $settingsConfig[$fullKey]['label'];
                    $labelText = is_array($label) ? ($label[app()->getLocale() ?? 'en'] ?? $label['en']) : $label;
                    $warnings[$labelText] = $settingsConfig[$fullKey]['warning'];
                }
            }
        }

        if ($warnings !== []) {
            $warningHtml = '<p>'.__('messages.confirm_save.title').':</p><ul class="mt-2 list-disc list-inside space-y-1">';
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
        try {
            $settings = Config::get('settings.settings', []);
            $validationRules = [];
            $validationMessages = [];

            $currentState = data_get($this->state, $this->group, []);
            $initialState = data_get($this->initialState, $this->group, []);
            $changedData = [];

            // 1. Identify changed fields and build validation rules ONLY for them.
            foreach ($currentState as $key => $value) {
                if (data_get($initialState, $key) != $value) {
                    $fullKey = $this->group.'.'.$key;



                    $changedData[$fullKey] = $value;

                    if (isset($settings[$fullKey]['rules'])) {
                        $validationRules['state.'.$fullKey] = $settings[$fullKey]['rules'];
                    }
                    if (isset($settings[$fullKey]['messages'])) {
                        foreach ($settings[$fullKey]['messages'] as $rule => $message) {
                            $validationMessages['state.'.$fullKey.'.'.$rule] = $message;
                        }
                    }
                }
            }

            // 2. Validate ONLY the changed fields.
            if ($validationRules !== []) {
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

            // Show success message using toast notification
            $groupLabel = $this->getAuthorizedGroups()->firstWhere('key', $this->group)->label ?? $this->group;
            $message = __(':group settings saved successfully.', ['group' => $groupLabel]);

            $this->showSuccessToast($message);

            // Dispatch a global event to notify other components
            if ($changedSettings !== []) {
                $this->dispatch('settings-updated', settings: $changedSettings);
            }

            // Close the modal
            Flux::modal('confirm-save')->close();

            // Update initial state
            $this->initialState = $this->state;
        } catch (\Exception $e) {
            $this->showErrorToast(__('messages.errors.generic'), $e->getMessage());
        }
    }

    /**
     * Revert changes to the initial state.
     */
    public function cancelChanges(): void
    {
        $this->state = $this->initialState;
    }

    /**
     * Handle file upload.
     */
    protected function handleFileUpload(string $key): string
    {
        try {
            $file = $this->files[$key];
            $path = $file->store('public/settings');

            return str_replace('public/', 'storage/', $path);
        } catch (\Exception $e) {
            $this->showErrorToast(__('messages.errors.file_upload'), $e->getMessage());
            throw $e; // Re-throw to be caught by the parent try-catch
        }
    }

    /**
     * Clear the application cache.
     */
    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            $this->showSuccessToast(__('messages.cache_cleared_successfully'));
        } catch (\Exception $e) {
            $this->showErrorToast(__('messages.errors.generic'), $e->getMessage());
        }
    }

    /**
     * Fix the language settings by resetting them to defaults.
     * This addresses issues with incorrect data structure in the database.
     */
    public function fixLanguageSettings(): void
    {
        try {
            // Delete the incorrect setting row
            Setting::where('key', 'general.available_locales')->delete();

            // Run the settings:sync command to recreate the setting with the correct structure
            Artisan::call('settings:sync');

            // Reload the settings to update the UI
            $this->loadSettings();
            $this->initialState = $this->state;

            $this->showSuccessToast(__('messages.language_settings_reset'));
        } catch (\Exception $e) {
            $this->showErrorToast(__('messages.errors.generic'), $e->getMessage());
        }
    }

    /**
     * Get the authorized setting groups.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedGroups()
    {
        return SettingGroup::query()
            ->whereIn('key', array_keys(config('settings.groups')))
            ->get()
            ->filter(function ($group): bool {
                // If a permission is required for the group, check if the user has it
                $permission = config('settings.groups.'.$group->key.'.permission');

                return ! $permission || $this->userCan($permission);
            });
    }

    /**
     * Get the settings for a given group.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedSettings(string $groupKey)
    {
        $settings = collect(config('settings.settings', []));

        return $settings->filter(fn ($setting, $key): bool => ($setting['group'] ?? '') === $groupKey && $this->userCan($setting['permission'] ?? null))->map(function ($settingData, $key) {
            // Check if the user has permission to manage the setting
            if (! $this->userCan($settingData['permission'] ?? null)) {
                return null;
            }

            // For media settings, we load the Setting model instance.
            if ($settingData['type'] === SettingType::MEDIA->value) {
                $setting = Setting::firstOrCreate(['key' => $key], $settingData);
            } else {
                $settingData['key'] = $key;
                $settingData['value'] = Settings::get($key);
                $settingData['label'] = is_array($settingData['label']) ? ($settingData['label'][app()->getLocale() ?? 'en'] ?? $settingData['label']['en']) : $settingData['label'];
                $settingData['description'] = isset($settingData['description']) ? (is_array($settingData['description']) ? ($settingData['description'][app()->getLocale() ?? 'en'] ?? $settingData['description']['en']) : $settingData['description']) : null;
                
                // Process subfields for repeaters
                if (isset($settingData['subfields']) && is_array($settingData['subfields'])) {
                    foreach ($settingData['subfields'] as $subfieldKey => $subfield) {
                        if (isset($subfield['label'])) {
                            $settingData['subfields'][$subfieldKey]['label'] = is_array($subfield['label']) 
                                ? ($subfield['label'][app()->getLocale() ?? 'en'] ?? $subfield['label']['en']) 
                                : $subfield['label'];
                        }
                    }
                }
                
                if (isset($settingData['options']) && is_callable($settingData['options'])) {
                    $settingData['options'] = call_user_func($settingData['options']);
                }
                if (isset($settingData['callout']['text'])) {
                    $settingData['callout']['text'] = is_array($settingData['callout']['text']) ? ($settingData['callout']['text'][app()->getLocale() ?? 'en'] ?? $settingData['callout']['text']['en']) : $settingData['callout']['text'];
                }
                $setting = (object) $settingData;
            }

            return $setting;
        })->filter();
    }

    /**
     * Check if the user can perform the given permission.
     */
    protected function userCan(?string $permission): bool
    {
        if ($permission === null || $permission === '' || $permission === '0') {
            return true;
        }

        // Check if the authenticated user has the required permission
        return Auth::user() && Auth::user()->can($permission);
    }

    /**
     * Get the icon for a group.
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
    public function reloadSettings(): void
    {
        $this->loadSettings();
    }

    #[On('repeater-updated')]
    public function onRepeaterUpdate(array $data): void
    {
        data_set($this->state, $data['model'], $data['items']);
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
