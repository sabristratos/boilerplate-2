<?php

namespace Tests\Feature;

use App\Enums\SettingGroupKey;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\User;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected SettingsManager $settingsManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear config cache to ensure fresh config
        \Artisan::call('config:clear');
        // Explicitly reload settings config
        $settingsConfig = require config_path('settings.php');
        config()->set('settings', $settingsConfig);
        config()->set('settings.settings', $settingsConfig['settings']);

        $this->settingsManager = new SettingsManager();
        
        // Seed permissions and roles first
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        
        // Create admin user with settings permissions
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin'); // This role has all settings permissions
        
        // Create setting groups
        $this->createSettingGroups();
    }

    /** @test */
    public function admin_can_access_settings_page(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertStatus(200)
            ->assertSee('Settings');
    }

    /** @test */
    public function non_admin_cannot_access_settings_page(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get('/admin/settings')
            ->assertStatus(403);
    }

    /** @test */
    public function settings_page_displays_all_groups(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('General')
            ->assertSee('Appearance')
            ->assertSee('Contact')
            ->assertSee('Email')
            ->assertSee('Security')
            ->assertSee('Social')
            ->assertSee('Advanced')
            ->assertSee('Content')
            ->assertSee('Navigation')
            ->assertSee('SEO');
    }

    /** @test */
    public function settings_page_displays_general_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('Application Name')
            ->assertSee('Application URL')
            ->assertSee('Available Locales')
            ->assertSee('Default Locale')
            ->assertSee('Fallback Locale');
    }

    /** @test */
    public function settings_page_displays_appearance_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('Theme')
            ->assertSee('Primary Color')
            ->assertSee('Application Logo')
            ->assertSee('Application Favicon');
    }

    /** @test */
    public function settings_page_displays_email_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('Mail Driver')
            ->assertSee('SMTP Host')
            ->assertSee('SMTP Port')
            ->assertSee('From Address')
            ->assertSee('From Name');
    }

    /** @test */
    public function settings_page_displays_security_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('Enable Registration')
            ->assertSee('Enable Email Verification')
            ->assertSee('Enable Two-Factor Authentication');
    }

    /** @test */
    public function settings_page_displays_seo_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/settings')
            ->assertSee('Google Analytics ID')
            ->assertSee('Google Tag Manager ID')
            ->assertSee('Default Meta Title')
            ->assertSee('Default Meta Description')
            ->assertSee('Enable Sitemap');
    }

    /** @test */
    public function admin_can_update_general_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('general.app_name', 'Updated App Name');
        $this->settingsManager->set('general.app_url', 'https://updated-app.com');
        $this->settingsManager->set('general.default_locale', 'fr');
        
        $this->assertEquals('Updated App Name', $this->settingsManager->get('general.app_name'));
        $this->assertEquals('https://updated-app.com', $this->settingsManager->get('general.app_url'));
        $this->assertEquals('fr', $this->settingsManager->get('general.default_locale'));
    }

    /** @test */
    public function admin_can_update_appearance_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('appearance.theme', 'dark');
        $this->settingsManager->set('appearance.primary_color', '#ff0000');
        
        $this->assertEquals('dark', $this->settingsManager->get('appearance.theme'));
        $this->assertEquals('#ff0000', $this->settingsManager->get('appearance.primary_color'));
    }

    /** @test */
    public function admin_can_update_email_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('email.driver', 'smtp');
        $this->settingsManager->set('email.host', 'smtp.example.com');
        $this->settingsManager->set('email.port', 587);
        $this->settingsManager->set('email.from_address', 'noreply@example.com');
        $this->settingsManager->set('email.from_name', 'Example App');
        
        $this->assertEquals('smtp', $this->settingsManager->get('email.driver'));
        $this->assertEquals('smtp.example.com', $this->settingsManager->get('email.host'));
        $this->assertEquals(587, $this->settingsManager->get('email.port'));
        $this->assertEquals('noreply@example.com', $this->settingsManager->get('email.from_address'));
        $this->assertEquals('Example App', $this->settingsManager->get('email.from_name'));
    }

    /** @test */
    public function admin_can_update_security_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('security.enable_registration', true);
        $this->settingsManager->set('security.enable_email_verification', true);
        $this->settingsManager->set('security.enable_two_factor_authentication', true);
        
        $this->assertTrue($this->settingsManager->get('security.enable_registration'));
        $this->assertTrue($this->settingsManager->get('security.enable_email_verification'));
        $this->assertTrue($this->settingsManager->get('security.enable_two_factor_authentication'));
    }

    /** @test */
    public function admin_can_update_contact_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('contact.address', '123 Updated Street, City, Country');
        $this->settingsManager->set('contact.google_maps', '<iframe src="updated-map"></iframe>');
        
        $this->assertEquals('123 Updated Street, City, Country', $this->settingsManager->get('contact.address'));
        $this->assertEquals('<iframe src="updated-map"></iframe>', $this->settingsManager->get('contact.google_maps'));
    }

    /** @test */
    public function admin_can_update_social_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('social.facebook_app_id', 'updated-facebook-id');
        $this->settingsManager->set('social.google_client_id', 'updated-google-client-id');
        
        $this->assertEquals('updated-facebook-id', $this->settingsManager->get('social.facebook_app_id'));
        $this->assertEquals('updated-google-client-id', $this->settingsManager->get('social.google_client_id'));
    }

    /** @test */
    public function admin_can_update_advanced_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('advanced.timezone', 'America/New_York');
        $this->settingsManager->set('advanced.cache_driver', 'redis');
        $this->settingsManager->set('advanced.session_driver', 'redis');
        
        $this->assertEquals('America/New_York', $this->settingsManager->get('advanced.timezone'));
        $this->assertEquals('redis', $this->settingsManager->get('advanced.cache_driver'));
        $this->assertEquals('redis', $this->settingsManager->get('advanced.session_driver'));
    }

    /** @test */
    public function admin_can_update_content_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('content.autosave_enabled', false);
        $this->settingsManager->set('content.autosave_interval', 60);
        
        $this->assertFalse($this->settingsManager->get('content.autosave_enabled'));
        $this->assertEquals(60, $this->settingsManager->get('content.autosave_interval'));
    }

    /** @test */
    public function admin_can_update_navigation_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $headerLinks = [
            ['label' => 'Home', 'url' => '/', 'target' => '_self'],
            ['label' => 'About', 'url' => '/about', 'target' => '_self'],
            ['label' => 'Contact', 'url' => '/contact', 'target' => '_self'],
        ];
        
        $footerLinks = [
            ['label' => 'Privacy Policy', 'url' => '/privacy', 'target' => '_self'],
            ['label' => 'Terms of Service', 'url' => '/terms', 'target' => '_self'],
        ];
        
        $this->settingsManager->set('navigation.header_links', $headerLinks);
        $this->settingsManager->set('navigation.footer_links', $footerLinks);
        
        $this->assertEquals($headerLinks, $this->settingsManager->get('navigation.header_links'));
        $this->assertEquals($footerLinks, $this->settingsManager->get('navigation.footer_links'));
    }

    /** @test */
    public function admin_can_update_seo_settings(): void
    {
        $this->actingAs($this->adminUser);
        
        $this->settingsManager->set('seo.google_analytics_id', 'G-UPDATED123');
        $this->settingsManager->set('seo.google_tag_manager_id', 'GTM-UPDATED');
        $this->settingsManager->set('seo.default_meta_title', 'Updated Meta Title');
        $this->settingsManager->set('seo.default_meta_description', 'Updated meta description for the website');
        $this->settingsManager->set('seo.sitemap_enabled', false);
        $this->settingsManager->set('seo.sitemap_update_frequency', 'monthly');
        
        $this->assertEquals('G-UPDATED123', $this->settingsManager->get('seo.google_analytics_id'));
        $this->assertEquals('GTM-UPDATED', $this->settingsManager->get('seo.google_tag_manager_id'));
        $this->assertEquals('Updated Meta Title', $this->settingsManager->get('seo.default_meta_title'));
        $this->assertEquals('Updated meta description for the website', $this->settingsManager->get('seo.default_meta_description'));
        $this->assertFalse($this->settingsManager->get('seo.sitemap_enabled'));
        $this->assertEquals('monthly', $this->settingsManager->get('seo.sitemap_update_frequency'));
    }

    /** @test */
    public function settings_are_persisted_in_database(): void
    {
        $this->actingAs($this->adminUser);
        
        // Set a setting
        $this->settingsManager->set('general.app_name', 'Database Test App');
        
        // Verify it's in the database
        $setting = Setting::where('key', 'general.app_name')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('Database Test App', $setting->value);
        
        // Verify it can be retrieved
        $this->assertEquals('Database Test App', $this->settingsManager->get('general.app_name'));
    }

    /** @test */
    public function settings_respect_permissions(): void
    {
        // Create a user without admin permissions
        $regularUser = User::factory()->create();
        
        // User without permissions should not be able to set settings
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->actingAs($regularUser);
        $this->settingsManager->set('general.app_name', 'Unauthorized Change');
    }

    /** @test */
    public function settings_validation_works(): void
    {
        $this->actingAs($this->adminUser);
        
        // Test invalid email format
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('email.from_address', 'invalid-email-format');
        
        // Test invalid URL format
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('general.app_url', 'not-a-valid-url');
        
        // Test invalid theme value
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('appearance.theme', 'invalid-theme');
        
        // Test invalid autosave interval (too low)
        $this->expectException(\InvalidArgumentException::class);
        $this->settingsManager->set('content.autosave_interval', 1);
    }

    /** @test */
    public function settings_cache_works_properly(): void
    {
        $this->actingAs($this->adminUser);
        
        // Set a setting
        $this->settingsManager->set('general.app_name', 'Cached App Name');
        
        // Verify it's cached
        $this->assertEquals('Cached App Name', $this->settingsManager->get('general.app_name'));
        
        // Clear cache
        $this->settingsManager->clearCache();
        
        // Should still work after cache clear
        $this->assertEquals('Cached App Name', $this->settingsManager->get('general.app_name'));
    }

    /** @test */
    public function settings_config_mapping_works(): void
    {
        $this->actingAs($this->adminUser);
        
        // Set settings that have config mappings
        $this->settingsManager->set('general.app_name', 'Config Mapped App');
        $this->settingsManager->set('general.app_url', 'https://config-mapped-app.com');
        $this->settingsManager->set('email.driver', 'smtp');
        
        // Verify config values are updated
        $this->assertEquals('Config Mapped App', config('app.name'));
        $this->assertEquals('https://config-mapped-app.com', config('app.url'));
        $this->assertEquals('smtp', config('mail.default'));
    }

    /** @test */
    public function settings_default_values_work(): void
    {
        $this->actingAs($this->adminUser);
        
        // Test default values for settings that haven't been set
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
    public function settings_translations_work(): void
    {
        $this->actingAs($this->adminUser);
        
        // Test that settings support translations
        $this->settingsManager->set('general.app_name', 'Translated App');
        
        // Test getting translated label
        $label = $this->settingsManager->getTranslatedLabel('general.app_name', 'en');
        $this->assertEquals('Application Name', $label);
        
        $label = $this->settingsManager->getTranslatedLabel('general.app_name', 'fr');
        $this->assertEquals('Nom de l\'application', $label);
    }

    /** @test */
    public function settings_repeater_fields_work(): void
    {
        $this->actingAs($this->adminUser);
        
        // Test available_locales setting (repeater)
        $locales = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
        ];
        $this->settingsManager->set('general.available_locales', $locales);
        $this->assertEquals($locales, $this->settingsManager->get('general.available_locales'));
        
        // Test contact.emails setting (repeater)
        $emails = [
            ['email' => 'info@example.com'],
            ['email' => 'support@example.com'],
        ];
        $this->settingsManager->set('contact.emails', $emails);
        $this->assertEquals($emails, $this->settingsManager->get('contact.emails'));
        
        // Test social.links setting (repeater)
        $socialLinks = [
            ['network' => 'facebook', 'url' => 'https://facebook.com/example'],
            ['network' => 'twitter', 'url' => 'https://twitter.com/example'],
        ];
        $this->settingsManager->set('social.links', $socialLinks);
        $this->assertEquals($socialLinks, $this->settingsManager->get('social.links'));
    }

    protected function createSettingGroups(): void
    {
        $groups = config('settings.groups');
        
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