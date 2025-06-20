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
        $settings = Config::get('settings.settings', []);

        foreach ($settings as $key => $setting) {
            // Skip settings that don't belong to the current group
            if (($setting['group'] ?? '') !== $this->group) {
                continue;
            }

            // Skip settings the user doesn't have permission to see
            if (!$this->userCan($setting['permission'] ?? null)) {
                continue;
            }

            // Get the setting value
            $value = Settings::get($key);

            // For repeater fields, ensure the value is an array
            if ($setting['type'] === SettingType::REPEATER->value) {
                $value = is_array($value) ? $value : [];
            }

            // Set the value in the state
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

        Flux::toast(__('Application cache cleared.'), variant: 'success');

        // Close the modal
        Flux::modal('confirm-clear-cache')->close();
    }

    /**
     * Get the setting groups the user is authorized to see.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedGroups()
    {
        return SettingGroup::query()
            ->orderBy('order_column')
            ->get()
            ->filter(function (SettingGroup $group) {
                // Check if user has permission to see any settings in this group
                return $group->settings()
                    ->where(function ($query) {
                        $query->whereNull('permission')
                            ->orWhereIn('permission', Auth::user()->getAllPermissions()->pluck('name'));
                    })
                    ->exists();
            });
    }

    /**
     * Get the settings for a group that the user is authorized to see.
     *
     * @param string $groupKey
     * @return \Illuminate\Support\Collection
     */
    protected function getAuthorizedSettings(string $groupKey)
    {
        $group = SettingGroup::where('key', $groupKey)->firstOrFail();

        return $group->settings()
            ->where(function ($query) {
                $query->whereNull('permission')
                    ->orWhereIn('permission', Auth::user()->getAllPermissions()->pluck('name'));
            })
            ->get()
            ->map(function (Setting $setting) {
                $setting->value = data_get($this->state, $setting->key);
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

        return Auth::user()->can($permission);
    }

    /**
     * Get the icon for a group.
     *
     * @param string $groupKey
     * @return string
     */
    protected function getGroupIcon(string $groupKey): string
    {
        $icons = [
            'general' => 'cog',
            'appearance' => 'paint-brush',
            'email' => 'envelope',
            'security' => 'shield-check',
            'social' => 'share',
            'advanced' => 'code-bracket',
            'contact' => 'phone',
        ];

        return $icons[$groupKey] ?? 'cog';
    }

    /**
     * Handle the media-updated event.
     */
    #[On('media-updated')]
    public function handleMediaUpdated(string $settingKey)
    {
        // Reload the settings to reflect the updated media
        $this->loadSettings();

        // Dispatch a global event to notify other components of the media change.
        $this->dispatch('settings-updated', settings: [
            $settingKey => setting_media_url($settingKey)
        ]);

        // Show a success message
        Flux::toast('Media updated successfully.', variant: 'success');
    }

    /**
     * Handle the repeater-updated event.
     */
    #[On('repeater-updated')]
    public function handleRepeaterUpdated(string $settingKey, array $items)
    {
        data_set($this->state, $settingKey, $items);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $currentGroup = $this->getAuthorizedGroups()->firstWhere('key', $this->group);

        return view('livewire.settings-page', [
            'groups' => $this->getAuthorizedGroups(),
            'settings' => $currentGroup ? $this->getAuthorizedSettings($this->group) : collect(),
            'currentGroup' => $currentGroup,
        ]);
    }
}
