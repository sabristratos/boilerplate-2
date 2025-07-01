<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;

class SettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsManager $settingsManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsManager = new SettingsManager;

        // Create a test setting group
        $group = SettingGroup::create([
            'key' => 'test',
            'label' => 'Test Group',
            'description' => 'Test Group Description',
            'order_column' => 1,
            'icon' => 'test-icon',
        ]);

        // Create a test setting
        Setting::create([
            'key' => 'test.key',
            'value' => 'test-value',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'Test Setting'],
            'type' => 'text',
            'cast' => 'string',
        ]);

        // Mock config for testing
        Config::set('settings.settings.test.key', [
            'group' => 'test',
            'type' => 'text',
            'cast' => 'string',
            'rules' => 'string|max:255',
        ]);

        Config::set('settings.settings.test.translatable', [
            'group' => 'test',
            'type' => 'text',
            'cast' => 'array',
            'rules' => 'array',
        ]);

        Config::set('settings.settings.test.permission', [
            'group' => 'test',
            'type' => 'text',
            'cast' => 'string',
            'permission' => 'test.permission',
        ]);

        Config::set('settings.settings.test.new_key', [
            'group' => 'test',
            'type' => 'text',
            'cast' => 'string',
            'rules' => 'string|max:255',
        ]);

        Config::set('settings.settings.test.new_setting', [
            'group' => 'test',
            'type' => 'text',
            'cast' => 'string',
            'rules' => 'string|max:255',
        ]);

        // Create settings with different types
        $group = SettingGroup::where('key', 'test')->first();

        Setting::create([
            'key' => 'test.boolean',
            'value' => '1',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'Boolean Setting'],
            'type' => 'checkbox',
            'cast' => 'boolean',
        ]);

        Setting::create([
            'key' => 'test.integer',
            'value' => '42',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'Integer Setting'],
            'type' => 'number',
            'cast' => 'integer',
        ]);

        Setting::create([
            'key' => 'test.json',
            'value' => '{"key":"value"}',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'JSON Setting'],
            'type' => 'repeater',
            'cast' => 'array',
        ]);

        Setting::create([
            'key' => 'test.translatable',
            'value' => '{"en":"English","fr":"French"}',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'Translatable Setting'],
            'type' => 'text',
            'cast' => 'array',
        ]);
    }

    /** @test */
    public function it_can_get_a_setting(): void
    {
        $value = $this->settingsManager->get('test.key');

        $this->assertEquals('test-value', $value);
    }

    /** @test */
    public function it_returns_default_value_if_setting_does_not_exist(): void
    {
        $value = $this->settingsManager->get('non.existent.key', 'default-value');

        $this->assertEquals('default-value', $value);
    }

    /** @test */
    public function it_can_set_a_setting(): void
    {
        $this->settingsManager->set('test.new_key', 'new-value');

        $value = $this->settingsManager->get('test.new_key');

        $this->assertEquals('new-value', $value);
    }

    /** @test */
    public function it_can_check_if_a_setting_exists(): void
    {
        $this->assertTrue($this->settingsManager->has('test.key'));
        $this->assertFalse($this->settingsManager->has('non.existent.key'));
    }

    /** @test */
    public function it_can_get_all_settings(): void
    {
        $settings = $this->settingsManager->getAll();

        $this->assertIsArray($settings);
        $this->assertArrayHasKey('test.key', $settings);
    }

    /** @test */
    public function it_can_get_all_settings_with_tagged_cache(): void
    {
        $settings = $this->settingsManager->getAll();

        $this->assertIsArray($settings);
        $this->assertArrayHasKey('test.key', $settings);
    }

    /** @test */
    public function it_can_get_all_settings_without_tagged_cache(): void
    {
        $settings = $this->settingsManager->getAll();

        $this->assertIsArray($settings);
        $this->assertArrayHasKey('test.key', $settings);
    }

    /** @test */
    public function it_can_clear_the_cache(): void
    {
        Cache::shouldReceive('tags')
            ->with(['settings'])
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        Cache::shouldReceive('forget')
            ->with('settings')
            ->never();

        $this->settingsManager->clearCache();
    }

    /** @test */
    public function it_can_clear_cache_without_tags(): void
    {
        Cache::shouldReceive('forget')
            ->with('settings')
            ->once();

        $this->settingsManager->clearCache();
    }

    /** @test */
    public function it_can_handle_keys_without_dots(): void
    {
        $group = SettingGroup::create([
            'key' => 'simple',
            'label' => 'Simple Group',
            'description' => 'Simple Group Description',
            'order_column' => 2,
            'icon' => 'simple-icon',
        ]);

        Setting::create([
            'key' => 'simple',
            'value' => 'simple-value',
            'setting_group_id' => $group->id,
            'label' => ['en' => 'Simple Setting'],
            'type' => 'text',
            'cast' => 'string',
        ]);

        $value = $this->settingsManager->get('simple');

        $this->assertEquals('simple-value', $value);
    }

    /** @test */
    public function it_can_cast_values_correctly(): void
    {
        // Mock the config to return cast types
        config(['settings.settings' => [
            'test.boolean' => ['cast' => 'boolean'],
            'test.integer' => ['cast' => 'integer'],
            'test.json' => ['cast' => 'array'],
        ]]);

        // Test boolean casting
        $value = $this->settingsManager->get('test.boolean');
        $this->assertIsBool($value);
        $this->assertTrue($value);

        // Test integer casting
        $value = $this->settingsManager->get('test.integer');
        $this->assertIsInt($value);
        $this->assertEquals(42, $value);

        // Test array casting
        $value = $this->settingsManager->get('test.json');
        $this->assertIsArray($value);
        $this->assertEquals(['key' => 'value'], $value);
    }

    /** @test */
    public function it_can_get_translated_setting(): void
    {
        $group = SettingGroup::where('key', 'test')->first();

        $value = $this->settingsManager->getTranslation('test.translatable', 'en');
        $this->assertEquals('English', $value);

        $value = $this->settingsManager->getTranslation('test.translatable', 'fr');
        $this->assertEquals('French', $value);

        $value = $this->settingsManager->getTranslation('test.translatable', 'es', 'default');
        $this->assertEquals('default', $value);
    }

    /** @test */
    public function it_can_set_translated_setting(): void
    {
        $this->settingsManager->setTranslation('test.translatable', 'en', 'English');
        $this->settingsManager->setTranslation('test.translatable', 'fr', 'French');

        $value = $this->settingsManager->get('test.translatable');
        $this->assertEquals(['en' => 'English', 'fr' => 'French'], $value);
    }

    /** @test */
    public function it_throws_exception_for_invalid_setting_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Setting configuration not found for key: invalid.key');

        $this->settingsManager->set('invalid.key', 'value');
    }

    /** @test */
    public function it_validates_setting_rules(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Try to set a value that doesn't match the rules (max:10)
        Config::set('settings.settings.test.key.rules', 'string|max:10');
        
        $this->settingsManager->set('test.key', str_repeat('a', 15));
    }

    /** @test */
    public function it_throws_exception_for_insufficient_permissions(): void
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Insufficient permissions to modify this setting.');

        // Create a user without the required permission
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $this->settingsManager->set('test.permission', 'value');
    }

    /** @test */
    public function it_allows_setting_with_proper_permissions(): void
    {
        // Create a user with the required permission
        $user = \App\Models\User::factory()->create();
        
        // Create the permission first
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'test.permission']);
        $user->givePermissionTo($permission);
        
        $this->actingAs($user);

        // Should not throw an exception
        $this->settingsManager->set('test.permission', 'value');

        $this->assertEquals('value', $this->settingsManager->get('test.permission'));
    }

    /** @test */
    public function it_creates_new_setting_with_configuration(): void
    {
        $this->settingsManager->set('test.new_setting', 'new-value');

        $setting = Setting::where('key', 'test.new_setting')->first();
        
        $this->assertNotNull($setting);
        $this->assertEquals('text', $setting->type);
        $this->assertEquals('string', $setting->cast);
        $this->assertEquals('string|max:255', $setting->rules);
    }
}
