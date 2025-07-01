<?php

namespace Tests\Feature;

use App\Facades\Settings;
use App\Models\User;
use App\Services\SocialLoginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Socialite
        Socialite::shouldReceive('driver')->andReturnSelf();
        
        // Ensure settings are synced for tests
        $this->artisan('settings:sync');
    }

    /** @test */
    public function it_can_check_if_google_login_is_enabled()
    {
        // Test when Google login is disabled
        try {
            Settings::set('social.enable_google_login', false);
        } catch (\Exception $e) {
            // If setting doesn't exist, create it
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', false);
        }
        
        $service = app(SocialLoginService::class);
        $this->assertFalse($service->isProviderEnabled('google'));
        
        // Test when Google login is enabled
        Settings::set('social.enable_google_login', true);
        $this->assertTrue($service->isProviderEnabled('google'));
    }

    /** @test */
    public function it_can_check_if_facebook_login_is_enabled()
    {
        // Test when Facebook login is disabled
        try {
            Settings::set('social.enable_facebook_login', false);
        } catch (\Exception $e) {
            // If setting doesn't exist, create it
            $this->artisan('settings:sync');
            Settings::set('social.enable_facebook_login', false);
        }
        
        $service = app(SocialLoginService::class);
        $this->assertFalse($service->isProviderEnabled('facebook'));
        
        // Test when Facebook login is enabled
        Settings::set('social.enable_facebook_login', true);
        $this->assertTrue($service->isProviderEnabled('facebook'));
    }

    /** @test */
    public function it_returns_enabled_providers()
    {
        $service = app(SocialLoginService::class);
        
        // Test when no providers are enabled
        try {
            Settings::set('social.enable_google_login', false);
            Settings::set('social.enable_facebook_login', false);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', false);
            Settings::set('social.enable_facebook_login', false);
        }
        
        $this->assertEquals([], $service->getEnabledProviders());
        
        // Test when only Google is enabled
        Settings::set('social.enable_google_login', true);
        Settings::set('social.enable_facebook_login', false);
        $this->assertEquals(['google'], $service->getEnabledProviders());
        
        // Test when only Facebook is enabled
        Settings::set('social.enable_google_login', false);
        Settings::set('social.enable_facebook_login', true);
        $this->assertEquals(['facebook'], $service->getEnabledProviders());
        
        // Test when both are enabled
        Settings::set('social.enable_google_login', true);
        Settings::set('social.enable_facebook_login', true);
        $this->assertEquals(['google', 'facebook'], $service->getEnabledProviders());
    }

    /** @test */
    public function it_returns_correct_provider_display_names()
    {
        $service = app(SocialLoginService::class);
        
        $this->assertEquals('Google', $service->getProviderDisplayName('google'));
        $this->assertEquals('Facebook', $service->getProviderDisplayName('facebook'));
        $this->assertEquals('Twitter', $service->getProviderDisplayName('twitter'));
    }

    /** @test */
    public function it_blocks_redirect_when_provider_is_disabled()
    {
        try {
            Settings::set('social.enable_google_login', false);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', false);
        }
        
        $response = $this->get('/auth/google/redirect');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_blocks_callback_when_provider_is_disabled()
    {
        try {
            Settings::set('social.enable_google_login', false);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', false);
        }
        
        $response = $this->get('/auth/google/callback');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_create_user_from_google_login()
    {
        try {
            Settings::set('social.enable_google_login', true);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', true);
        }
        
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_123');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getEmail')->andReturn('john@example.com');
        $socialiteUser->token = 'google_token';
        $socialiteUser->refreshToken = 'google_refresh_token';
        
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);
        
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect('/dashboard');
        
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google_123', $user->google_id);
        $this->assertEquals('google_token', $user->google_token);
        $this->assertEquals('google_refresh_token', $user->google_refresh_token);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_can_create_user_from_facebook_login()
    {
        try {
            Settings::set('social.enable_facebook_login', true);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_facebook_login', true);
        }
        
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('facebook_123');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Doe');
        $socialiteUser->shouldReceive('getEmail')->andReturn('jane@example.com');
        $socialiteUser->token = 'facebook_token';
        $socialiteUser->refreshToken = 'facebook_refresh_token';
        
        Socialite::shouldReceive('driver')->with('facebook')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);
        
        $response = $this->get('/auth/facebook/callback');
        $response->assertRedirect('/dashboard');
        
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('facebook_123', $user->facebook_id);
        $this->assertEquals('facebook_token', $user->facebook_token);
        $this->assertEquals('facebook_refresh_token', $user->facebook_refresh_token);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_links_social_account_to_existing_user()
    {
        try {
            Settings::set('social.enable_google_login', true);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', true);
        }
        
        // Create existing user
        $existingUser = User::factory()->create([
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
        
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_123');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getEmail')->andReturn('john@example.com');
        $socialiteUser->token = 'google_token';
        $socialiteUser->refreshToken = 'google_refresh_token';
        
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);
        
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect('/dashboard');
        
        $existingUser->refresh();
        $this->assertEquals('google_123', $existingUser->google_id);
        $this->assertEquals('google_token', $existingUser->google_token);
        $this->assertEquals('google_refresh_token', $existingUser->google_refresh_token);
    }

    /** @test */
    public function it_updates_tokens_for_existing_social_user()
    {
        try {
            Settings::set('social.enable_google_login', true);
        } catch (\Exception $e) {
            $this->artisan('settings:sync');
            Settings::set('social.enable_google_login', true);
        }
        
        // Create user with existing Google account
        $user = User::factory()->create([
            'google_id' => 'google_123',
            'google_token' => 'old_token',
            'google_refresh_token' => 'old_refresh_token',
        ]);
        
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_123');
        $socialiteUser->shouldReceive('getName')->andReturn($user->name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($user->email);
        $socialiteUser->token = 'new_token';
        $socialiteUser->refreshToken = 'new_refresh_token';
        
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);
        
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect('/dashboard');
        
        $user->refresh();
        $this->assertEquals('new_token', $user->google_token);
        $this->assertEquals('new_refresh_token', $user->google_refresh_token);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 