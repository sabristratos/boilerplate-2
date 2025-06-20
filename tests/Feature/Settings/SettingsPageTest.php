<?php

namespace Tests\Feature\Settings;

use App\Facades\Settings;
use App\Livewire\SettingsPage;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Settings::get($key, $default);
    }
}

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test setting group
        $group = SettingGroup::create([
            'key' => 'general',
            'label' => 'General',
            'description' => 'General settings for the application',
            'order_column' => 1,
            'icon' => 'cog',
        ]);

        // Create a test setting
        Setting::create([
            'key' => 'general.app_name',
            'value' => 'Test App',
            'setting_group_id' => $group->id,
        ]);

        // Create a second group
        $group2 = SettingGroup::create([
            'key' => 'appearance',
            'label' => 'Appearance',
            'description' => 'Appearance settings for the application',
            'order_column' => 2,
            'icon' => 'paint-brush',
        ]);

        // Create a test setting for the second group
        Setting::create([
            'key' => 'appearance.theme',
            'value' => 'light',
            'setting_group_id' => $group2->id,
        ]);

        // Mock the config
        config(['settings.settings' => [
            'general.app_name' => [
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of the application',
                'type' => 'text',
                'cast' => 'string',
                'rules' => 'required|string|max:255',
            ],
            'appearance.theme' => [
                'group' => 'appearance',
                'label' => 'Theme',
                'description' => 'The theme of the application',
                'type' => 'select',
                'cast' => 'string',
                'rules' => 'string|in:light,dark,auto',
                'options' => [
                    'light' => 'Light',
                    'dark' => 'Dark',
                    'auto' => 'Auto',
                ],
            ],
        ]]);
    }

    /** @test */
    public function it_can_render_the_settings_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.group', ['group' => 'general']))
            ->assertStatus(200);
    }

    /** @test */
    public function it_loads_settings_for_the_current_group()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->assertSet('group', 'general')
            ->assertSet('state.general.app_name', 'Test App');
    }

    /** @test */
    public function it_redirects_to_first_available_group_if_group_does_not_exist()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'non-existent'])
            ->assertRedirect(route('settings.group', ['group' => 'general']));
    }

    /** @test */
    public function it_can_save_settings()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->set('state.general.app_name', 'Updated App Name')
            ->call('save');

        $this->assertEquals('Updated App Name', setting('general.app_name'));
    }

    /** @test */
    public function it_validates_settings_before_saving()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->set('state.general.app_name', '') // Empty value, should fail validation
            ->call('save')
            ->assertHasErrors(['state.general.app_name' => 'required']);
    }

    /** @test */
    public function it_can_clear_the_cache()
    {
        $user = User::factory()->create();

        Artisan::shouldReceive('call')
            ->with('cache:clear')
            ->once();

        Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->call('clearCache');
    }

    /** @test */
    public function it_only_shows_settings_for_the_current_group()
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->assertSet('group', 'general')
            ->assertSet('state.general.app_name', 'Test App');

        // The appearance.theme setting should not be loaded
        $this->assertArrayNotHasKey('appearance.theme', $component->get('state'));
    }

    /** @test */
    public function it_can_switch_between_groups()
    {
        $user = User::factory()->create();

        // Start with the general group
        $component = Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'general'])
            ->assertSet('group', 'general')
            ->assertSet('state.general.app_name', 'Test App');

        // Switch to the appearance group
        $component = Livewire::actingAs($user)
            ->test(SettingsPage::class, ['group' => 'appearance'])
            ->assertSet('group', 'appearance')
            ->assertSet('state.appearance.theme', 'light');
    }
}
