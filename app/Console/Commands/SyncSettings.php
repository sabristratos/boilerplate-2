<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SyncSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize settings from config file to the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting settings synchronization...');

        DB::transaction(function () {
            $groups = Config::get('settings.groups', []);
            $settings = Config::get('settings.settings', []);

            // Sync groups
            foreach ($groups as $key => $group) {
                SettingGroup::updateOrCreate(
                    ['key' => $key],
                    [
                        'label' => $group['label'],
                        'description' => $group['description'],
                        'icon' => $group['icon'],
                        'order_column' => $group['order_column'],
                    ]
                );
            }

            $this->info('Setting groups synchronized.');

            // Sync settings
            foreach ($settings as $key => $setting) {
                $group = SettingGroup::where('key', $setting['group'])->first();

                if (! $group) {
                    $this->warn("Setting group '{$setting['group']}' not found for setting '{$key}'. Skipping.");
                    continue;
                }

                $settingModel = Setting::firstOrNew(['key' => $key]);

                $data = [
                    'setting_group_id' => $group->id,
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'type' => $setting['type'],
                    'cast' => $setting['cast'] ?? null,
                    'permission' => $setting['permission'] ?? null,
                    'config_key' => $setting['config'] ?? null,
                ];

                if (isset($setting['options']) && is_array($setting['options'])) {
                    $data['options'] = $setting['options'];
                }

                if (isset($setting['rules']) && (is_string($setting['rules']) || is_array($setting['rules']))) {
                    $data['rules'] = $setting['rules'];
                }

                if (isset($setting['subfields']) && is_array($setting['subfields'])) {
                    $data['subfields'] = $setting['subfields'];
                }

                if (isset($setting['callout']) && is_array($setting['callout'])) {
                    $data['callout'] = $setting['callout'];
                }

                // If the setting is new and a default value is defined, add it to the data for creation.
                if (!$settingModel->exists && isset($setting['default'])) {
                    $data['value'] = $setting['default'];
                }

                // Use updateOrCreate to sync settings.
                // This will create the setting with default value or update existing ones without overwriting their value.
                Setting::updateOrCreate(['key' => $key], $data);
            }

            $this->info('Settings synchronized.');

            // Remove old settings that are not in the config file
            $definedKeys = array_keys($settings);
            Setting::whereNotIn('key', $definedKeys)->delete();
            $this->info('Old settings removed.');
        });

        $this->info('Settings synchronization complete.');
    }
}
