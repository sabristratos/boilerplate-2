<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use App\Facades\Settings;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\Contracts\SettingsManagerInterface;
use App\Traits\WithToastNotifications;
use Flux\Flux;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Livewire component for managing application settings.
 *
 * This component provides a comprehensive settings management interface
 * with support for different setting types, validation, and file uploads.
 * It uses services for business logic and DTOs for data handling.
 */
class SettingsPage extends Component
{
    use WithFileUploads, WithToastNotifications;

    /**
     * The state of the settings.
     *
     * @var array<string, mixed>
     */
    public array $state = [];

    /**
     * The initial state of the settings.
     *
     * @var array<string, mixed>
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
     *
     * @var array<string, mixed>
     */
    public array $files = [];

    public SettingGroup $currentGroup;

    /**
     * Settings manager service instance.
     */
    protected SettingsManagerInterface $settingsManager;

    /**
     * Boot the component with dependencies.
     */
    public function boot(SettingsManagerInterface $settingsManager): void
    {
        $this->settingsManager = $settingsManager;
    }

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

            if (!$this->userCan($setting['permission'] ?? null)) {
                continue;
            }

            // For media fields, we don't store the value directly in the state.
            // The MediaUploader component will handle the media.
            if ($setting['type'] === SettingType::MEDIA->value) {
                continue;
            }

            $value = $this->settingsManager->get($key);

            // If value is null, use the default value from the config
            if ($value === null && isset($setting['default'])) {
                $value = $setting['default'];
            }

            if ($setting['type'] === SettingType::REPEATER->value) {
                $value = is_array($value) ? $value : [];
            }

            // Extract the field name from the full key (remove group prefix)
            $fieldName = str_replace($this->group.'.', '', $key);
            data_set($this->state, $this->group.'.'.$fieldName, $value);
        }
    }

    /**
     * Save the settings.
     */
    public function save(): void
    {
        try {
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

        } catch (\Exception $e) {
            logger()->error('Error saving settings', [
                'group' => $this->group,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->showErrorToast(__('settings.errors.save_failed'));
        }
    }

    /**
     * Confirm and execute the save operation.
     */
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

            // 3. Save the validated, changed data using the service.
            $changedSettings = [];
            foreach ($changedData as $fullKey => $value) {
                // Handle file uploads
                if (isset($this->files[$fullKey]) && $this->files[$fullKey]) {
                    $value = $this->handleFileUpload($fullKey);
                }

                $this->settingsManager->set($fullKey, $value);
                $changedSettings[$fullKey] = $value;
            }

            // Update initial state to reflect saved changes
            foreach ($changedSettings as $fullKey => $value) {
                $key = str_replace($this->group.'.', '', $fullKey);
                data_set($this->initialState, $this->group.'.'.$key, $value);
            }

            $this->showSuccessToast(__('settings.toast.saved_successfully'));

            // Clear cache if needed
            if (!empty($changedSettings)) {
                $this->clearCache();
            }

        } catch (\Exception $e) {
            logger()->error('Error in confirmed save', [
                'group' => $this->group,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->showErrorToast(__('settings.errors.save_failed'));
        }
    }

    /**
     * Cancel changes and revert to initial state.
     */
    public function cancelChanges(): void
    {
        $this->state = $this->initialState;
        $this->showWarningToast(__('settings.toast.changes_cancelled'));
    }

    /**
     * Handle file upload for a setting.
     */
    protected function handleFileUpload(string $key): string
    {
        $file = $this->files[$key];
        $path = $file->store('settings', 'public');
        
        return $path;
    }

    /**
     * Clear application cache.
     */
    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            $this->showSuccessToast(__('settings.toast.cache_cleared'));
            
        } catch (\Exception $e) {
            logger()->error('Error clearing cache', [
                'error' => $e->getMessage(),
            ]);
            
            $this->showErrorToast(__('settings.errors.cache_clear_failed'));
        }
    }

    /**
     * Fix language settings.
     */
    public function fixLanguageSettings(): void
    {
        try {
            $this->settingsManager->fixLanguageSettings();
            $this->showSuccessToast(__('settings.toast.language_settings_fixed'));
            
        } catch (\Exception $e) {
            logger()->error('Error fixing language settings', [
                'error' => $e->getMessage(),
            ]);
            
            $this->showErrorToast(__('settings.errors.language_fix_failed'));
        }
    }

    /**
     * Get authorized setting groups for the current user.
     */
    protected function getAuthorizedGroups()
    {
        return SettingGroup::whereHas('settings', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('permission')
                  ->orWhereHas('permission', function ($permissionQuery) {
                      $permissionQuery->whereHas('roles', function ($roleQuery) {
                          $roleQuery->whereHas('users', function ($userQuery) {
                              $userQuery->where('user_id', Auth::id());
                          });
                      });
                  });
            });
        })->get();
    }

    /**
     * Get authorized settings for a specific group.
     */
    protected function getAuthorizedSettings(string $groupKey)
    {
        $settingsConfig = Config::get('settings.settings', []);
        $authorizedSettings = [];

        foreach ($settingsConfig as $key => $setting) {
            if (($setting['group'] ?? '') !== $groupKey) {
                continue;
            }

            if (!$this->userCan($setting['permission'] ?? null)) {
                continue;
            }

            $authorizedSettings[$key] = $setting;
        }

        return $authorizedSettings;
    }

    /**
     * Check if user has permission for a setting.
     */
    protected function userCan(?string $permission): bool
    {
        if ($permission === null) {
            return true;
        }

        return Auth::user()->can($permission);
    }

    /**
     * Get group icon for display.
     */
    protected function getGroupIcon(string $groupKey): string
    {
        $icons = [
            SettingGroupKey::GENERAL->value => 'cog-6-tooth',
            SettingGroupKey::APPEARANCE->value => 'paint-brush',
            SettingGroupKey::EMAIL->value => 'envelope',
            SettingGroupKey::SOCIAL->value => 'share',
            SettingGroupKey::ADVANCED->value => 'wrench-screwdriver',
        ];

        return $icons[$groupKey] ?? 'cog-6-tooth';
    }

    /**
     * Handle media updates.
     */
    #[On('media-updated')]
    public function reloadSettings(): void
    {
        $this->loadSettings();
        $this->initialState = $this->state;
    }

    /**
     * Handle repeater updates.
     */
    #[On('repeater-updated')]
    public function onRepeaterUpdate(array $data): void
    {
        if (isset($data['model']) && isset($data['items'])) {
            $modelPath = $data['model'];
            $items = $data['items'];

            data_set($this->state, $modelPath, $items);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $authorizedGroups = $this->getAuthorizedGroups();
        $authorizedSettings = $this->getAuthorizedSettings($this->group);

        return view('livewire.settings-page', [
            'authorizedGroups' => $authorizedGroups,
            'authorizedSettings' => $authorizedSettings,
        ])->title(__('settings.page_title'));
    }
}
