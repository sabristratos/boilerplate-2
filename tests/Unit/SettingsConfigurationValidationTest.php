<?php

namespace Tests\Unit;

use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SettingsConfigurationValidationTest extends TestCase
{
    /** @test */
    public function settings_configuration_is_valid(): void
    {
        $settings = Config::get('settings.settings');
        $groups = Config::get('settings.groups');
        
        $this->assertIsArray($settings, 'Settings should be an array');
        $this->assertIsArray($groups, 'Groups should be an array');
        $this->assertNotEmpty($settings, 'Settings should not be empty');
        $this->assertNotEmpty($groups, 'Groups should not be empty');
    }

    /** @test */
    public function all_settings_have_required_fields(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            $this->assertArrayHasKey('group', $config, "Setting {$key} missing 'group' field");
            $this->assertArrayHasKey('label', $config, "Setting {$key} missing 'label' field");
            $this->assertArrayHasKey('type', $config, "Setting {$key} missing 'type' field");
            $this->assertArrayHasKey('cast', $config, "Setting {$key} missing 'cast' field");
            
            // Validate group exists
            $this->assertArrayHasKey($config['group'], Config::get('settings.groups'), 
                "Setting {$key} references non-existent group: {$config['group']}");
        }
    }

    /** @test */
    public function all_setting_types_are_valid(): void
    {
        $settings = Config::get('settings.settings');
        $validTypes = array_column(SettingType::cases(), 'value');
        
        foreach ($settings as $key => $config) {
            $this->assertContains($config['type'], $validTypes, 
                "Setting {$key} has invalid type: {$config['type']}");
        }
    }

    /** @test */
    public function all_setting_casts_are_valid(): void
    {
        $settings = Config::get('settings.settings');
        $validCasts = ['string', 'integer', 'boolean', 'array', 'float'];
        
        foreach ($settings as $key => $config) {
            $this->assertContains($config['cast'], $validCasts, 
                "Setting {$key} has invalid cast: {$config['cast']}");
        }
    }

    /** @test */
    public function all_setting_groups_are_valid(): void
    {
        $groups = Config::get('settings.groups');
        $validGroups = array_column(SettingGroupKey::cases(), 'value');
        
        foreach ($groups as $key => $config) {
            $this->assertContains($key, $validGroups, 
                "Group {$key} is not a valid SettingGroupKey");
        }
    }

    /** @test */
    public function all_groups_have_required_fields(): void
    {
        $groups = Config::get('settings.groups');
        
        foreach ($groups as $key => $config) {
            $this->assertArrayHasKey('label', $config, "Group {$key} missing 'label' field");
            $this->assertArrayHasKey('description', $config, "Group {$key} missing 'description' field");
            $this->assertArrayHasKey('icon', $config, "Group {$key} missing 'icon' field");
            $this->assertArrayHasKey('order_column', $config, "Group {$key} missing 'order_column' field");
            
            // Validate that label and description are arrays (for translations)
            $this->assertIsArray($config['label'], "Group {$key} label should be an array");
            $this->assertIsArray($config['description'], "Group {$key} description should be an array");
        }
    }

    /** @test */
    public function all_labels_are_translatable(): void
    {
        $settings = Config::get('settings.settings');
        $groups = Config::get('settings.groups');
        
        // Check settings labels
        foreach ($settings as $key => $config) {
            $this->assertIsArray($config['label'], "Setting {$key} label should be an array for translations");
            $this->assertArrayHasKey('en', $config['label'], "Setting {$key} label missing English translation");
        }
        
        // Check groups labels
        foreach ($groups as $key => $config) {
            $this->assertIsArray($config['label'], "Group {$key} label should be an array for translations");
            $this->assertArrayHasKey('en', $config['label'], "Group {$key} label missing English translation");
        }
    }

    /** @test */
    public function all_descriptions_are_translatable(): void
    {
        $settings = Config::get('settings.settings');
        $groups = Config::get('settings.groups');
        
        // Check settings descriptions
        foreach ($settings as $key => $config) {
            if (isset($config['description'])) {
                $this->assertIsArray($config['description'], "Setting {$key} description should be an array for translations");
                $this->assertArrayHasKey('en', $config['description'], "Setting {$key} description missing English translation");
            }
        }
        
        // Check groups descriptions
        foreach ($groups as $key => $config) {
            $this->assertIsArray($config['description'], "Group {$key} description should be an array for translations");
            $this->assertArrayHasKey('en', $config['description'], "Group {$key} description missing English translation");
        }
    }

    /** @test */
    public function select_settings_have_options(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if ($config['type'] === SettingType::SELECT->value) {
                // Some select settings might have dynamic options (closures) or be optional
                if (isset($config['options'])) {
                    $this->assertNotEmpty($config['options'], "Select setting {$key} has empty options");
                }
            }
        }
    }

    /** @test */
    public function repeater_settings_have_subfields(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if ($config['type'] === SettingType::REPEATER->value) {
                $this->assertArrayHasKey('subfields', $config, "Repeater setting {$key} missing 'subfields' field");
                $this->assertNotEmpty($config['subfields'], "Repeater setting {$key} has empty subfields");
                
                // Validate subfields structure
                foreach ($config['subfields'] as $subfieldKey => $subfieldConfig) {
                    $this->assertArrayHasKey('label', $subfieldConfig, "Subfield {$subfieldKey} in {$key} missing 'label' field");
                    $this->assertArrayHasKey('type', $subfieldConfig, "Subfield {$subfieldKey} in {$key} missing 'type' field");
                    $this->assertArrayHasKey('rules', $subfieldConfig, "Subfield {$subfieldKey} in {$key} missing 'rules' field");
                }
            }
        }
    }

    /** @test */
    public function settings_with_permissions_have_valid_permission_strings(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['permission'])) {
                $this->assertIsString($config['permission'], "Setting {$key} permission should be a string");
                $this->assertNotEmpty($config['permission'], "Setting {$key} permission should not be empty");
                
                // Validate permission format (should be dot-separated)
                $this->assertMatchesRegularExpression('/^[a-z]+\.[a-z]+\.[a-z]+$/', $config['permission'], 
                    "Setting {$key} permission should follow format: group.section.action");
            }
        }
    }

    /** @test */
    public function settings_with_config_mapping_have_valid_config_keys(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['config'])) {
                $this->assertIsString($config['config'], "Setting {$key} config should be a string");
                $this->assertNotEmpty($config['config'], "Setting {$key} config should not be empty");
                
                // Validate config key format (should be dot-separated)
                $this->assertMatchesRegularExpression('/^[a-z]+\.[a-z._]+$/', $config['config'], 
                    "Setting {$key} config should follow format: section.key");
            }
        }
    }

    /** @test */
    public function settings_with_rules_have_valid_validation_rules(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['rules'])) {
                $this->assertIsString($config['rules'], "Setting {$key} rules should be a string");
                $this->assertNotEmpty($config['rules'], "Setting {$key} rules should not be empty");
                
                // Basic validation that rules contain common Laravel validation rules
                $commonRules = ['required', 'string', 'integer', 'boolean', 'array', 'email', 'url', 'max:', 'min:', 'nullable', 'timezone', 'in:', 'regex:', 'exists:'];
                $hasValidRule = false;
                
                foreach ($commonRules as $rule) {
                    if (str_contains($config['rules'], $rule)) {
                        $hasValidRule = true;
                        break;
                    }
                }
                
                $this->assertTrue($hasValidRule, "Setting {$key} rules should contain valid Laravel validation rules");
            }
        }
    }

    /** @test */
    public function settings_with_default_values_have_correct_types(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['default'])) {
                $cast = $config['cast'];
                
                switch ($cast) {
                    case 'boolean':
                        $this->assertIsBool($config['default'], "Setting {$key} default should be boolean");
                        break;
                    case 'integer':
                        $this->assertIsInt($config['default'], "Setting {$key} default should be integer");
                        break;
                    case 'array':
                        $this->assertIsArray($config['default'], "Setting {$key} default should be array");
                        break;
                    case 'string':
                        $this->assertIsString($config['default'], "Setting {$key} default should be string");
                        break;
                }
            }
        }
    }

    /** @test */
    public function settings_keys_follow_naming_convention(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            // Keys should be lowercase and dot-separated
            $this->assertMatchesRegularExpression('/^[a-z]+\.[a-z_]+$/', $key, 
                "Setting key {$key} should follow format: group.setting_name");
        }
    }

    /** @test */
    public function group_keys_follow_naming_convention(): void
    {
        $groups = Config::get('settings.groups');
        
        foreach ($groups as $key => $config) {
            // Group keys should be lowercase
            $this->assertMatchesRegularExpression('/^[a-z]+$/', $key, 
                "Group key {$key} should be lowercase");
        }
    }

    /** @test */
    public function no_duplicate_setting_keys(): void
    {
        $settings = Config::get('settings.settings');
        $keys = array_keys($settings);
        $uniqueKeys = array_unique($keys);
        
        $this->assertCount(count($keys), $uniqueKeys, 'Settings should not have duplicate keys');
    }

    /** @test */
    public function no_duplicate_group_keys(): void
    {
        $groups = Config::get('settings.groups');
        $keys = array_keys($groups);
        $uniqueKeys = array_unique($keys);
        
        $this->assertCount(count($keys), $uniqueKeys, 'Groups should not have duplicate keys');
    }

    /** @test */
    public function all_settings_belong_to_existing_groups(): void
    {
        $settings = Config::get('settings.settings');
        $groups = Config::get('settings.groups');
        $groupKeys = array_keys($groups);
        
        foreach ($settings as $key => $config) {
            $this->assertContains($config['group'], $groupKeys, 
                "Setting {$key} belongs to non-existent group: {$config['group']}");
        }
    }

    /** @test */
    public function settings_have_consistent_structure(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            // All settings should have these basic fields
            $requiredFields = ['group', 'label', 'type', 'cast'];
            foreach ($requiredFields as $field) {
                $this->assertArrayHasKey($field, $config, "Setting {$key} missing required field: {$field}");
            }
            
            // Optional fields should be of correct type when present
            $optionalFields = [
                'description' => 'array',
                'rules' => 'string',
                'permission' => 'string',
                'config' => 'string',
                'options' => 'mixed', // Can be array or closure
                'subfields' => 'array',
                'callout' => 'array',
                'default' => 'mixed',
                'warning' => 'string',
                'order' => 'integer',
            ];
            
            foreach ($optionalFields as $field => $expectedType) {
                if (isset($config[$field])) {
                    if ($expectedType === 'array') {
                        $this->assertIsArray($config[$field], "Setting {$key} {$field} should be array");
                    } elseif ($expectedType === 'string') {
                        $this->assertIsString($config[$field], "Setting {$key} {$field} should be string");
                    } elseif ($expectedType === 'integer') {
                        $this->assertIsInt($config[$field], "Setting {$key} {$field} should be integer");
                    } elseif ($expectedType === 'mixed') {
                        // For mixed types, just ensure it's not null
                        $this->assertNotNull($config[$field], "Setting {$key} {$field} should not be null");
                    }
                }
            }
        }
    }
} 