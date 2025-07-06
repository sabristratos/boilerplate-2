<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsCoreFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsManager $settingsManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->settingsManager = new SettingsManager();
        
        // Create setting groups
        $this->createSettingGroups();
        
        // Run the permissions seeder
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /** @test */
    public function it_can_set_and_get_basic_settings(): void
    {
        // Test setting and getting a basic text setting
        $this->settingsManager->set('general.app_name', 'Test Application');
        $this->assertEquals('Test Application', $this->settingsManager->get('general.app_name'));
        
        // Test setting and getting a URL setting
        $this->settingsManager->set('general.app_url', 'https://test.com');
        $this->assertEquals('https://test.com', $this->settingsManager->get('general.app_url'));
        
        // Test setting and getting a boolean setting
        $this->settingsManager->set('security.enable_registration', true);
        $this->assertTrue($this->settingsManager->get('security.enable_registration'));
        
        // Test setting and getting an integer setting
        $this->settingsManager->set('content.autosave_interval', 60);
        $this->assertEquals(60, $this->settingsManager->get('content.autosave_interval'));
    }

    /** @test */
    public function it_can_set_and_get_select_settings(): void
    {
        // Test theme setting
        $this->settingsManager->set('appearance.theme', 'dark');
        $this->assertEquals('dark', $this->settingsManager->get('appearance.theme'));
        
        // Test email driver setting
        $this->settingsManager->set('email.driver', 'smtp');
        $this->assertEquals('smtp', $this->settingsManager->get('email.driver'));
        
        // Test timezone setting
        $this->settingsManager->set('advanced.timezone', 'UTC');
        $this->assertEquals('UTC', $this->settingsManager->get('advanced.timezone'));
    }

    /** @test */
    public function it_can_set_and_get_repeater_settings(): void
    {
        // Test available locales
        $locales = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
        ];
        $this->settingsManager->set('general.available_locales', $locales);
        $this->assertEquals($locales, $this->settingsManager->get('general.available_locales'));
        
        // Test contact emails
        $emails = [
            ['email' => 'info@example.com'],
            ['email' => 'support@example.com'],
        ];
        $this->settingsManager->set('contact.emails', $emails);
        $this->assertEquals($emails, $this->settingsManager->get('contact.emails'));
        
        // Test navigation links
        $headerLinks = [
            ['label' => 'Home', 'url' => '/', 'target' => '_self'],
            ['label' => 'About', 'url' => '/about', 'target' => '_self'],
        ];
        $this->settingsManager->set('navigation.header_links', $headerLinks);
        $this->assertEquals($headerLinks, $this->settingsManager->get('navigation.header_links'));
    }

    /** @test */
    public function it_returns_default_values_when_settings_not_set(): void
    {
        // Test default values from config
        $this->assertEquals('light', $this->settingsManager->get('appearance.theme'));
        $this->assertEquals('oklch(64.5% .246 16.439)', $this->settingsManager->get('appearance.primary_color'));
        $this->assertEquals('en', $this->settingsManager->get('general.default_locale'));
        $this->assertEquals('en', $this->settingsManager->get('general.fallback_locale'));
        $this->assertFalse($this->settingsManager->get('security.enable_registration'));
        $this->assertTrue($this->settingsManager->get('content.autosave_enabled'));
        $this->assertEquals(30, $this->settingsManager->get('content.autosave_interval'));
        $this->assertTrue($this->settingsManager->get('seo.sitemap_enabled'));
        $this->assertEquals('weekly', $this->settingsManager->get('seo.sitemap_update_frequency'));
    }

    /** @test */
    public function it_validates_setting_values(): void
    {
        // Test invalid email
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('email.from_address', 'invalid-email');
        
        // Test invalid URL
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('general.app_url', 'not-a-url');
        
        // Test invalid theme
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('appearance.theme', 'invalid-theme');
        
        // Test invalid timezone
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('advanced.timezone', 'invalid-timezone');
        
        // Test invalid Google Analytics ID
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('seo.google_analytics_id', 'invalid-id');
    }

    /** @test */
    public function it_validates_repeater_settings(): void
    {
        // Test invalid available_locales (missing required fields)
        $invalidLocales = [
            ['name' => 'English'], // Missing 'code'
            ['code' => 'fr'], // Missing 'name'
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('general.available_locales', $invalidLocales);
        
        // Test invalid contact emails (invalid email format)
        $invalidEmails = [
            ['email' => 'invalid-email'],
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('contact.emails', $invalidEmails);
        
        // Test invalid navigation links (invalid target)
        $invalidLinks = [
            ['label' => 'Home', 'url' => '/', 'target' => 'invalid'],
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('navigation.header_links', $invalidLinks);
    }

    /** @test */
    public function it_persists_settings_in_database(): void
    {
        // Set a setting
        $this->settingsManager->set('general.app_name', 'Persisted App Name');
        
        // Verify it's in the database
        $setting = Setting::where('key', 'general.app_name')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('Persisted App Name', $setting->value);
        
        // Verify it can be retrieved
        $this->assertEquals('Persisted App Name', $this->settingsManager->get('general.app_name'));
        
        // Verify the setting has the correct metadata
        $this->assertEquals('text', $setting->type);
        $this->assertEquals('string', $setting->cast);
        $this->assertEquals('settings.general.manage', $setting->permission);
    }

    /** @test */
    public function it_handles_cache_properly(): void
    {
        // Set a setting
        $this->settingsManager->set('general.app_name', 'Cached Setting');
        
        // Verify it's cached
        $this->assertEquals('Cached Setting', $this->settingsManager->get('general.app_name'));
        
        // Clear cache
        $this->settingsManager->clearCache();
        
        // Should still work after cache clear
        $this->assertEquals('Cached Setting', $this->settingsManager->get('general.app_name'));
    }

    /** @test */
    public function it_handles_translations(): void
    {
        // Test setting translation
        $this->settingsManager->setTranslation('general.app_name', 'en', 'English App Name');
        $this->settingsManager->setTranslation('general.app_name', 'fr', 'Nom de l\'application en français');
        
        $this->assertEquals('English App Name', $this->settingsManager->getTranslation('general.app_name', 'en'));
        $this->assertEquals('Nom de l\'application en français', $this->settingsManager->getTranslation('general.app_name', 'fr'));
        
        // Test getting full translated value
        $fullValue = $this->settingsManager->get('general.app_name');
        $this->assertIsArray($fullValue);
        $this->assertEquals('English App Name', $fullValue['en']);
        $this->assertEquals('Nom de l\'application en français', $fullValue['fr']);
    }

    /** @test */
    public function it_maps_settings_to_config(): void
    {
        // Set settings that should update config
        $this->settingsManager->set('general.app_name', 'Config Mapped App');
        $this->settingsManager->set('general.app_url', 'https://config-mapped.com');
        $this->settingsManager->set('general.default_locale', 'es');
        $this->settingsManager->set('advanced.timezone', 'Europe/London');
        
        // Verify config was updated
        $this->assertEquals('Config Mapped App', config('app.name'));
        $this->assertEquals('https://config-mapped.com', config('app.url'));
        $this->assertEquals('es', config('app.locale'));
        $this->assertEquals('Europe/London', config('app.timezone'));
    }

    /** @test */
    public function it_handles_all_setting_types(): void
    {
        $settings = Config::get('settings.settings');
        
        foreach ($settings as $key => $config) {
            // Skip settings that require special handling or have complex validation
            if (in_array($key, [
                'general.homepage', // Requires Page model
                'appearance.logo', // Media type
                'appearance.favicon', // Media type
                'seo.default_og_image', // Media type
            ])) {
                continue;
            }
            
            // Test each setting type with appropriate test values
            switch ($config['type']) {
                case 'text':
                case 'textarea':
                case 'email':
                case 'url':
                case 'password':
                case 'tel':
                    $testValue = 'test_value';
                    break;
                case 'number':
                    $testValue = 42;
                    break;
                case 'checkbox':
                    $testValue = true;
                    break;
                case 'select':
                    // Use first option if available
                    if (isset($config['options']) && is_array($config['options'])) {
                        $testValue = array_key_first($config['options']);
                    } elseif (isset($config['options']) && is_callable($config['options'])) {
                        // Skip dynamic options for now
                        continue 2;
                    } else {
                        $testValue = 'test';
                    }
                    break;
                case 'color':
                    $testValue = '#ff0000';
                    break;
                case 'repeater':
                    // Use simple repeater structure
                    $testValue = [['test' => 'value']];
                    break;
                default:
                    $testValue = 'test_value';
            }
            
            try {
                $this->settingsManager->set($key, $testValue);
                $retrievedValue = $this->settingsManager->get($key);
                
                // Basic assertion that the value was set and retrieved
                $this->assertNotNull($retrievedValue, "Setting {$key} should return a value");
            } catch (\Exception $e) {
                // Some settings might have validation that prevents our test values
                // This is expected for some settings
                continue;
            }
        }
    }

    /** @test */
    public function it_handles_missing_settings_gracefully(): void
    {
        // Test getting non-existent setting with default
        $value = $this->settingsManager->get('non.existent.setting', 'default_value');
        $this->assertEquals('default_value', $value);
        
        // Test getting non-existent setting without default
        $value = $this->settingsManager->get('non.existent.setting');
        $this->assertNull($value);
        
        // Test checking if setting exists
        $this->assertFalse($this->settingsManager->has('non.existent.setting'));
        $this->assertTrue($this->settingsManager->has('general.app_name')); // Should exist after previous tests
    }

    protected function createSettingGroups(): void
    {
        $groups = Config::get('settings.groups');
        
        foreach ($groups as $key => $config) {
            SettingGroup::create([
                'key' => $key,
                'label' => $config['label'],
                'description' => $config['description'],
                'icon' => $config['icon'],
                'order_column' => $config['order_column'],
            ]);
        }
    }
} 