<?php

namespace Tests\Unit;

use App\Enums\SettingGroupKey;
use App\Enums\SettingType;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\User;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SettingsConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsManager $settingsManager;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->settingsManager = new SettingsManager();
        $this->user = User::factory()->create();
        
        // Create setting groups from config
        $this->createSettingGroups();
    }

    /** @test */
    public function it_loads_all_settings_from_configuration(): void
    {
        $settings = config('settings.settings');
        
        $this->assertIsArray($settings);
        $this->assertNotEmpty($settings);
        
        // Verify we have settings from all groups
        $expectedGroups = [
            'general', 'appearance', 'contact', 'email', 'security', 
            'social', 'advanced', 'content', 'navigation', 'seo'
        ];
        
        foreach ($expectedGroups as $group) {
            $groupSettings = array_filter($settings, fn($key) => str_starts_with($key, $group . '.'), ARRAY_FILTER_USE_KEY);
            $this->assertNotEmpty($groupSettings, "No settings found for group: {$group}");
        }
    }

    /** @test */
    public function it_validates_general_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test app_name setting
        $this->assertArrayHasKey('general.app_name', $settings);
        $appNameConfig = $settings['general.app_name'];
        $this->assertEquals('general', $appNameConfig['group']);
        $this->assertEquals('text', $appNameConfig['type']);
        $this->assertEquals('string', $appNameConfig['cast']);
        $this->assertEquals('settings.general.manage', $appNameConfig['permission']);
        $this->assertEquals('app.name', $appNameConfig['config']);
        
        // Test app_url setting
        $this->assertArrayHasKey('general.app_url', $settings);
        $appUrlConfig = $settings['general.app_url'];
        $this->assertEquals('general', $appUrlConfig['group']);
        $this->assertEquals('url', $appUrlConfig['type']);
        $this->assertEquals('string', $appUrlConfig['cast']);
        $this->assertEquals('settings.general.manage', $appUrlConfig['permission']);
        $this->assertEquals('app.url', $appUrlConfig['config']);
        
        // Test default_locale setting
        $this->assertArrayHasKey('general.default_locale', $settings);
        $defaultLocaleConfig = $settings['general.default_locale'];
        $this->assertEquals('general', $defaultLocaleConfig['group']);
        $this->assertEquals('select', $defaultLocaleConfig['type']);
        $this->assertEquals('string', $defaultLocaleConfig['cast']);
        $this->assertEquals('settings.general.manage', $defaultLocaleConfig['permission']);
        $this->assertEquals('app.locale', $defaultLocaleConfig['config']);
        $this->assertEquals('en', $defaultLocaleConfig['default']);
        
        // Test fallback_locale setting
        $this->assertArrayHasKey('general.fallback_locale', $settings);
        $fallbackLocaleConfig = $settings['general.fallback_locale'];
        $this->assertEquals('general', $fallbackLocaleConfig['group']);
        $this->assertEquals('select', $fallbackLocaleConfig['type']);
        $this->assertEquals('string', $fallbackLocaleConfig['cast']);
        $this->assertEquals('settings.general.manage', $fallbackLocaleConfig['permission']);
        $this->assertEquals('app.fallback_locale', $fallbackLocaleConfig['config']);
        $this->assertEquals('en', $fallbackLocaleConfig['default']);
    }

    /** @test */
    public function it_validates_appearance_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test theme setting
        $this->assertArrayHasKey('appearance.theme', $settings);
        $themeConfig = $settings['appearance.theme'];
        $this->assertEquals('appearance', $themeConfig['group']);
        $this->assertEquals('select', $themeConfig['type']);
        $this->assertEquals('string', $themeConfig['cast']);
        $this->assertEquals('settings.appearance.manage', $themeConfig['permission']);
        $this->assertEquals('light', $themeConfig['default']);
        $this->assertArrayHasKey('options', $themeConfig);
        $this->assertEquals(['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto'], $themeConfig['options']);
        
        // Test primary_color setting
        $this->assertArrayHasKey('appearance.primary_color', $settings);
        $primaryColorConfig = $settings['appearance.primary_color'];
        $this->assertEquals('appearance', $primaryColorConfig['group']);
        $this->assertEquals('color', $primaryColorConfig['type']);
        $this->assertEquals('string', $primaryColorConfig['cast']);
        $this->assertEquals('settings.appearance.manage', $primaryColorConfig['permission']);
        $this->assertEquals('oklch(64.5% .246 16.439)', $primaryColorConfig['default']);
        
        // Test logo setting
        $this->assertArrayHasKey('appearance.logo', $settings);
        $logoConfig = $settings['appearance.logo'];
        $this->assertEquals('appearance', $logoConfig['group']);
        $this->assertEquals('media', $logoConfig['type']);
        $this->assertEquals('string', $logoConfig['cast']);
        $this->assertEquals('settings.appearance.manage', $logoConfig['permission']);
        
        // Test favicon setting
        $this->assertArrayHasKey('appearance.favicon', $settings);
        $faviconConfig = $settings['appearance.favicon'];
        $this->assertEquals('appearance', $faviconConfig['group']);
        $this->assertEquals('media', $faviconConfig['type']);
        $this->assertEquals('string', $faviconConfig['cast']);
        $this->assertEquals('settings.appearance.manage', $faviconConfig['permission']);
    }

    /** @test */
    public function it_validates_email_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test email driver setting
        $this->assertArrayHasKey('email.driver', $settings);
        $driverConfig = $settings['email.driver'];
        $this->assertEquals('email', $driverConfig['group']);
        $this->assertEquals('select', $driverConfig['type']);
        $this->assertEquals('string', $driverConfig['cast']);
        $this->assertEquals('settings.email.manage', $driverConfig['permission']);
        $this->assertEquals('mail.default', $driverConfig['config']);
        $this->assertArrayHasKey('options', $driverConfig);
        
        // Test email host setting
        $this->assertArrayHasKey('email.host', $settings);
        $hostConfig = $settings['email.host'];
        $this->assertEquals('email', $hostConfig['group']);
        $this->assertEquals('text', $hostConfig['type']);
        $this->assertEquals('string', $hostConfig['cast']);
        $this->assertEquals('settings.email.manage', $hostConfig['permission']);
        $this->assertEquals('mail.mailers.smtp.host', $hostConfig['config']);
        
        // Test email port setting
        $this->assertArrayHasKey('email.port', $settings);
        $portConfig = $settings['email.port'];
        $this->assertEquals('email', $portConfig['group']);
        $this->assertEquals('number', $portConfig['type']);
        $this->assertEquals('integer', $portConfig['cast']);
        $this->assertEquals('settings.email.manage', $portConfig['permission']);
        $this->assertEquals('mail.mailers.smtp.port', $portConfig['config']);
        
        // Test email from_address setting
        $this->assertArrayHasKey('email.from_address', $settings);
        $fromAddressConfig = $settings['email.from_address'];
        $this->assertEquals('email', $fromAddressConfig['group']);
        $this->assertEquals('email', $fromAddressConfig['type']);
        $this->assertEquals('string', $fromAddressConfig['cast']);
        $this->assertEquals('settings.email.manage', $fromAddressConfig['permission']);
        $this->assertEquals('mail.from.address', $fromAddressConfig['config']);
    }

    /** @test */
    public function it_validates_security_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test enable_registration setting
        $this->assertArrayHasKey('security.enable_registration', $settings);
        $registrationConfig = $settings['security.enable_registration'];
        $this->assertEquals('security', $registrationConfig['group']);
        $this->assertEquals('checkbox', $registrationConfig['type']);
        $this->assertEquals('boolean', $registrationConfig['cast']);
        $this->assertEquals('settings.security.manage', $registrationConfig['permission']);
        $this->assertFalse($registrationConfig['default']);
        
        // Test enable_email_verification setting
        $this->assertArrayHasKey('security.enable_email_verification', $settings);
        $emailVerificationConfig = $settings['security.enable_email_verification'];
        $this->assertEquals('security', $emailVerificationConfig['group']);
        $this->assertEquals('checkbox', $emailVerificationConfig['type']);
        $this->assertEquals('boolean', $emailVerificationConfig['cast']);
        $this->assertEquals('settings.security.manage', $emailVerificationConfig['permission']);
        
        // Test enable_two_factor_authentication setting
        $this->assertArrayHasKey('security.enable_two_factor_authentication', $settings);
        $twoFactorConfig = $settings['security.enable_two_factor_authentication'];
        $this->assertEquals('security', $twoFactorConfig['group']);
        $this->assertEquals('checkbox', $twoFactorConfig['type']);
        $this->assertEquals('boolean', $twoFactorConfig['cast']);
        $this->assertEquals('settings.security.manage', $twoFactorConfig['permission']);
    }

    /** @test */
    public function it_validates_contact_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test address setting
        $this->assertArrayHasKey('contact.address', $settings);
        $addressConfig = $settings['contact.address'];
        $this->assertEquals('contact', $addressConfig['group']);
        $this->assertEquals('textarea', $addressConfig['type']);
        $this->assertEquals('string', $addressConfig['cast']);
        $this->assertEquals('settings.contact.manage', $addressConfig['permission']);
        
        // Test google_maps setting
        $this->assertArrayHasKey('contact.google_maps', $settings);
        $googleMapsConfig = $settings['contact.google_maps'];
        $this->assertEquals('contact', $googleMapsConfig['group']);
        $this->assertEquals('textarea', $googleMapsConfig['type']);
        $this->assertEquals('string', $googleMapsConfig['cast']);
        $this->assertEquals('settings.contact.manage', $googleMapsConfig['permission']);
        
        // Test emails setting (repeater)
        $this->assertArrayHasKey('contact.emails', $settings);
        $emailsConfig = $settings['contact.emails'];
        $this->assertEquals('contact', $emailsConfig['group']);
        $this->assertEquals('repeater', $emailsConfig['type']);
        $this->assertEquals('array', $emailsConfig['cast']);
        $this->assertEquals('settings.contact.manage', $emailsConfig['permission']);
        $this->assertArrayHasKey('subfields', $emailsConfig);
        
        // Test phones setting (repeater)
        $this->assertArrayHasKey('contact.phones', $settings);
        $phonesConfig = $settings['contact.phones'];
        $this->assertEquals('contact', $phonesConfig['group']);
        $this->assertEquals('repeater', $phonesConfig['type']);
        $this->assertEquals('array', $phonesConfig['cast']);
        $this->assertEquals('settings.contact.manage', $phonesConfig['permission']);
        $this->assertArrayHasKey('subfields', $phonesConfig);
    }

    /** @test */
    public function it_validates_social_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test facebook_app_id setting
        $this->assertArrayHasKey('social.facebook_app_id', $settings);
        $facebookConfig = $settings['social.facebook_app_id'];
        $this->assertEquals('social', $facebookConfig['group']);
        $this->assertEquals('text', $facebookConfig['type']);
        $this->assertEquals('string', $facebookConfig['cast']);
        $this->assertEquals('settings.social.manage', $facebookConfig['permission']);
        
        // Test google_client_id setting
        $this->assertArrayHasKey('social.google_client_id', $settings);
        $googleConfig = $settings['social.google_client_id'];
        $this->assertEquals('social', $googleConfig['group']);
        $this->assertEquals('text', $googleConfig['type']);
        $this->assertEquals('string', $googleConfig['cast']);
        $this->assertEquals('settings.social.manage', $googleConfig['permission']);
        
        // Test social links setting (repeater)
        $this->assertArrayHasKey('social.links', $settings);
        $linksConfig = $settings['social.links'];
        $this->assertEquals('social', $linksConfig['group']);
        $this->assertEquals('repeater', $linksConfig['type']);
        $this->assertEquals('array', $linksConfig['cast']);
        $this->assertEquals('settings.social.manage', $linksConfig['permission']);
        $this->assertArrayHasKey('subfields', $linksConfig);
    }

    /** @test */
    public function it_validates_advanced_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test timezone setting
        $this->assertArrayHasKey('advanced.timezone', $settings);
        $timezoneConfig = $settings['advanced.timezone'];
        $this->assertEquals('advanced', $timezoneConfig['group']);
        $this->assertEquals('select', $timezoneConfig['type']);
        $this->assertEquals('string', $timezoneConfig['cast']);
        $this->assertEquals('settings.advanced.manage', $timezoneConfig['permission']);
        $this->assertEquals('app.timezone', $timezoneConfig['config']);
        $this->assertArrayHasKey('options', $timezoneConfig);
        
        // Test cache_driver setting
        $this->assertArrayHasKey('advanced.cache_driver', $settings);
        $cacheConfig = $settings['advanced.cache_driver'];
        $this->assertEquals('advanced', $cacheConfig['group']);
        $this->assertEquals('select', $cacheConfig['type']);
        $this->assertEquals('string', $cacheConfig['cast']);
        $this->assertEquals('settings.advanced.manage', $cacheConfig['permission']);
        $this->assertEquals('cache.default', $cacheConfig['config']);
        $this->assertArrayHasKey('options', $cacheConfig);
        
        // Test session_driver setting
        $this->assertArrayHasKey('advanced.session_driver', $settings);
        $sessionConfig = $settings['advanced.session_driver'];
        $this->assertEquals('advanced', $sessionConfig['group']);
        $this->assertEquals('select', $sessionConfig['type']);
        $this->assertEquals('string', $sessionConfig['cast']);
        $this->assertEquals('settings.advanced.manage', $sessionConfig['permission']);
        $this->assertEquals('session.driver', $sessionConfig['config']);
        $this->assertArrayHasKey('options', $sessionConfig);
    }

    /** @test */
    public function it_validates_content_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test autosave_enabled setting
        $this->assertArrayHasKey('content.autosave_enabled', $settings);
        $autosaveConfig = $settings['content.autosave_enabled'];
        $this->assertEquals('content', $autosaveConfig['group']);
        $this->assertEquals('checkbox', $autosaveConfig['type']);
        $this->assertEquals('boolean', $autosaveConfig['cast']);
        $this->assertEquals('settings.content.manage', $autosaveConfig['permission']);
        $this->assertTrue($autosaveConfig['default']);
        
        // Test autosave_interval setting
        $this->assertArrayHasKey('content.autosave_interval', $settings);
        $intervalConfig = $settings['content.autosave_interval'];
        $this->assertEquals('content', $intervalConfig['group']);
        $this->assertEquals('number', $intervalConfig['type']);
        $this->assertEquals('integer', $intervalConfig['cast']);
        $this->assertEquals('settings.content.manage', $intervalConfig['permission']);
        $this->assertEquals(30, $intervalConfig['default']);
    }

    /** @test */
    public function it_validates_navigation_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test header_links setting (repeater)
        $this->assertArrayHasKey('navigation.header_links', $settings);
        $headerLinksConfig = $settings['navigation.header_links'];
        $this->assertEquals('navigation', $headerLinksConfig['group']);
        $this->assertEquals('repeater', $headerLinksConfig['type']);
        $this->assertEquals('array', $headerLinksConfig['cast']);
        $this->assertEquals('settings.navigation.manage', $headerLinksConfig['permission']);
        $this->assertArrayHasKey('subfields', $headerLinksConfig);
        
        // Test footer_links setting (repeater)
        $this->assertArrayHasKey('navigation.footer_links', $settings);
        $footerLinksConfig = $settings['navigation.footer_links'];
        $this->assertEquals('navigation', $footerLinksConfig['group']);
        $this->assertEquals('repeater', $footerLinksConfig['type']);
        $this->assertEquals('array', $footerLinksConfig['cast']);
        $this->assertEquals('settings.navigation.manage', $footerLinksConfig['permission']);
        $this->assertArrayHasKey('subfields', $footerLinksConfig);
    }

    /** @test */
    public function it_validates_seo_settings_structure(): void
    {
        $settings = config('settings.settings');
        
        // Test google_analytics_id setting
        $this->assertArrayHasKey('seo.google_analytics_id', $settings);
        $analyticsConfig = $settings['seo.google_analytics_id'];
        $this->assertEquals('seo', $analyticsConfig['group']);
        $this->assertEquals('text', $analyticsConfig['type']);
        $this->assertEquals('string', $analyticsConfig['cast']);
        $this->assertEquals('settings.seo.manage', $analyticsConfig['permission']);
        
        // Test google_tag_manager_id setting
        $this->assertArrayHasKey('seo.google_tag_manager_id', $settings);
        $gtmConfig = $settings['seo.google_tag_manager_id'];
        $this->assertEquals('seo', $gtmConfig['group']);
        $this->assertEquals('text', $gtmConfig['type']);
        $this->assertEquals('string', $gtmConfig['cast']);
        $this->assertEquals('settings.seo.manage', $gtmConfig['permission']);
        
        // Test default_meta_title setting
        $this->assertArrayHasKey('seo.default_meta_title', $settings);
        $metaTitleConfig = $settings['seo.default_meta_title'];
        $this->assertEquals('seo', $metaTitleConfig['group']);
        $this->assertEquals('text', $metaTitleConfig['type']);
        $this->assertEquals('string', $metaTitleConfig['cast']);
        $this->assertEquals('settings.seo.manage', $metaTitleConfig['permission']);
        
        // Test default_meta_description setting
        $this->assertArrayHasKey('seo.default_meta_description', $settings);
        $metaDescriptionConfig = $settings['seo.default_meta_description'];
        $this->assertEquals('seo', $metaDescriptionConfig['group']);
        $this->assertEquals('textarea', $metaDescriptionConfig['type']);
        $this->assertEquals('string', $metaDescriptionConfig['cast']);
        $this->assertEquals('settings.seo.manage', $metaDescriptionConfig['permission']);
        
        // Test sitemap_enabled setting
        $this->assertArrayHasKey('seo.sitemap_enabled', $settings);
        $sitemapEnabledConfig = $settings['seo.sitemap_enabled'];
        $this->assertEquals('seo', $sitemapEnabledConfig['group']);
        $this->assertEquals('checkbox', $sitemapEnabledConfig['type']);
        $this->assertEquals('boolean', $sitemapEnabledConfig['cast']);
        $this->assertEquals('settings.seo.manage', $sitemapEnabledConfig['permission']);
        $this->assertTrue($sitemapEnabledConfig['default']);
        
        // Test sitemap_update_frequency setting
        $this->assertArrayHasKey('seo.sitemap_update_frequency', $settings);
        $sitemapFrequencyConfig = $settings['seo.sitemap_update_frequency'];
        $this->assertEquals('seo', $sitemapFrequencyConfig['group']);
        $this->assertEquals('select', $sitemapFrequencyConfig['type']);
        $this->assertEquals('string', $sitemapFrequencyConfig['cast']);
        $this->assertEquals('settings.seo.manage', $sitemapFrequencyConfig['permission']);
        $this->assertEquals('weekly', $sitemapFrequencyConfig['default']);
        $this->assertArrayHasKey('options', $sitemapFrequencyConfig);
    }

    /** @test */
    public function it_validates_setting_types(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            // Validate required fields exist
            $this->assertArrayHasKey('label', $config, "Setting {$key} missing label");
            $this->assertArrayHasKey('description', $config, "Setting {$key} missing description");
            $this->assertArrayHasKey('type', $config, "Setting {$key} missing type");
            $this->assertArrayHasKey('cast', $config, "Setting {$key} missing cast");
            $this->assertArrayHasKey('group', $config, "Setting {$key} missing group");

            // Validate type is a valid SettingType
            $validTypes = array_column(SettingType::cases(), 'value');
            $this->assertContains($config['type'], $validTypes, "Invalid type for setting {$key}");

            // Validate cast is a valid cast type
            $validCasts = ['string', 'integer', 'boolean', 'array', 'float'];
            $this->assertContains($config['cast'], $validCasts, "Invalid cast for setting {$key}");

            // Validate group is a valid SettingGroupKey
            $validGroups = array_column(SettingGroupKey::cases(), 'value');
            $this->assertContains($config['group'], $validGroups, "Invalid group for setting {$key}");
        }
    }

    /** @test */
    public function it_validates_setting_groups(): void
    {
        $groups = config('settings.groups');
        
        $this->assertIsArray($groups);
        $this->assertNotEmpty($groups);
        
        foreach ($groups as $key => $config) {
            $this->assertArrayHasKey('label', $config, "Group {$key} missing label");
            $this->assertArrayHasKey('description', $config, "Group {$key} missing description");
            $this->assertArrayHasKey('icon', $config, "Group {$key} missing icon");
            $this->assertArrayHasKey('order_column', $config, "Group {$key} missing order_column");
        }
    }

    /** @test */
    public function it_validates_required_fields_in_repeater_settings(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            if ($config['type'] === SettingType::REPEATER->value) {
                $this->assertArrayHasKey('subfields', $config, "Repeater setting {$key} missing subfields");
                $this->assertIsArray($config['subfields'], "Subfields for {$key} must be an array");
                $this->assertNotEmpty($config['subfields'], "Subfields for {$key} cannot be empty");
            }
        }
    }

    /** @test */
    public function it_validates_translations_in_settings(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            // Validate label translations
            $this->assertArrayHasKey('label', $config, "Setting {$key} missing label");
            $this->assertIsArray($config['label'], "Setting {$key} label should be an array");
            $this->assertArrayHasKey('en', $config['label'], "Setting {$key} missing English label");
            
            // Validate description translations
            $this->assertArrayHasKey('description', $config, "Setting {$key} missing description");
            $this->assertIsArray($config['description'], "Setting {$key} description should be an array");
            $this->assertArrayHasKey('en', $config['description'], "Setting {$key} missing English description");
        }
    }

    /** @test */
    public function it_validates_permissions_are_consistent(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['permission'])) {
                $permission = $config['permission'];
                $group = $config['group'];
                
                // Validate permission format matches group
                $expectedPermission = "settings.{$group}.manage";
                $this->assertEquals($expectedPermission, $permission, "Permission for {$key} should be {$expectedPermission}");
            }
        }
    }

    /** @test */
    public function it_validates_config_mapping_is_consistent(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['config'])) {
                $configKey = $config['config'];
                
                // Validate config key format
                $this->assertMatchesRegularExpression('/^[a-z]+\.[a-z_]+(\.[a-z_]+)*$/', $configKey, "Config key for {$key} should be in dot notation format");
            }
        }
    }

    /** @test */
    public function it_validates_default_values_are_appropriate(): void
    {
        $settings = config('settings.settings');
        
        foreach ($settings as $key => $config) {
            if (isset($config['default'])) {
                $default = $config['default'];
                $type = $config['type'];
                $cast = $config['cast'];
                
                // Validate default value matches type
                switch ($cast) {
                    case 'boolean':
                        $this->assertIsBool($default, "Default value for {$key} should be boolean");
                        break;
                    case 'integer':
                        $this->assertIsInt($default, "Default value for {$key} should be integer");
                        break;
                    case 'string':
                        $this->assertIsString($default, "Default value for {$key} should be string");
                        break;
                    case 'array':
                        $this->assertIsArray($default, "Default value for {$key} should be array");
                        break;
                }
            }
        }
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