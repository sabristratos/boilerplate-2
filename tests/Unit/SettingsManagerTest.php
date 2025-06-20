<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsManager $settingsManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsManager = new SettingsManager();

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
        ]);
    }

    /** @test */
    public function it_can_get_a_setting()
    {
        $value = $this->settingsManager->get('test.key');

        $this->assertEquals('test-value', $value);
    }

    /** @test */
    public function it_returns_default_value_if_setting_does_not_exist()
    {
        $value = $this->settingsManager->get('non.existent.key', 'default-value');

        $this->assertEquals('default-value', $value);
    }

    /** @test */
    public function it_can_set_a_setting()
    {
        $this->settingsManager->set('test.new_key', 'new-value');

        $value = $this->settingsManager->get('test.new_key');

        $this->assertEquals('new-value', $value);
    }

    /** @test */
    public function it_can_check_if_a_setting_exists()
    {
        $this->assertTrue($this->settingsManager->has('test.key'));
        $this->assertFalse($this->settingsManager->has('non.existent.key'));
    }

    /** @test */
    public function it_can_get_all_settings()
    {
        $settings = $this->settingsManager->getAll();

        $this->assertIsArray($settings);
        $this->assertArrayHasKey('test.key', $settings);
    }

    /** @test */
    public function it_can_clear_the_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('settings');

        $this->settingsManager->clearCache();
    }

    /** @test */
    public function it_can_handle_keys_without_dots()
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
        ]);

        $value = $this->settingsManager->get('simple');

        $this->assertEquals('simple-value', $value);
    }

    /** @test */
    public function it_can_cast_values_correctly()
    {
        // Create settings with different types
        $group = SettingGroup::where('key', 'test')->first();

        Setting::create([
            'key' => 'test.boolean',
            'value' => '1',
            'setting_group_id' => $group->id,
        ]);

        Setting::create([
            'key' => 'test.integer',
            'value' => '42',
            'setting_group_id' => $group->id,
        ]);

        Setting::create([
            'key' => 'test.json',
            'value' => '{"key":"value"}',
            'setting_group_id' => $group->id,
        ]);

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
}
