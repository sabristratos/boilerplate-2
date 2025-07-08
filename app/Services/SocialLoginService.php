<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class SocialLoginService
{
    /**
     * Redirect the user to the OAuth provider
     */
    public function redirect(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (! $this->isProviderEnabled($provider)) {
            abort(404, 'Social login provider is not enabled.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            Log::error('Social login redirect failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Unable to redirect to social login provider.');
        }
    }

    /**
     * Handle the OAuth callback and authenticate the user
     */
    public function handleCallback(string $provider): \Illuminate\Http\RedirectResponse
    {
        if (! $this->isProviderEnabled($provider)) {
            abort(404, 'Social login provider is not enabled.');
        }

        try {
            $socialiteUser = Socialite::driver($provider)->user();

            $user = $this->findUser($provider, $socialiteUser);

            if (!$user instanceof \App\Models\User) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'No account found with this social login. Please register first or contact an administrator.']);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Social login callback failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Social login failed. Please try again.']);
        }
    }

    /**
     * Check if a provider is enabled
     */
    public function isProviderEnabled(string $provider): bool
    {
        $settingKey = "social.enable_{$provider}_login";

        try {
            return (bool) Settings::get($settingKey, false);
        } catch (\Exception $e) {
            Log::warning("Could not check if {$provider} login is enabled", [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get all enabled providers
     */
    public function getEnabledProviders(): array
    {
        $providers = [];

        if ($this->isProviderEnabled('google')) {
            $providers[] = 'google';
        }

        if ($this->isProviderEnabled('facebook')) {
            $providers[] = 'facebook';
        }

        return $providers;
    }

    /**
     * Get provider display name
     */
    public function getProviderDisplayName(string $provider): string
    {
        return match ($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'github' => 'GitHub',
            'linkedin' => 'LinkedIn',
            default => ucfirst($provider),
        };
    }

    /**
     * Find a user from social login (does not create new users)
     */
    protected function findUser(string $provider, SocialiteUser $socialiteUser): ?User
    {
        $providerIdField = "{$provider}_id";
        $providerTokenField = "{$provider}_token";
        $providerRefreshTokenField = "{$provider}_refresh_token";

        // Try to find user by provider ID
        $user = User::where($providerIdField, $socialiteUser->getId())->first();

        if ($user) {
            // Update tokens for existing user
            $user->update([
                $providerTokenField => $socialiteUser->token,
                $providerRefreshTokenField => $socialiteUser->refreshToken,
            ]);

            return $user;
        }

        // Try to find user by email
        if ($socialiteUser->getEmail()) {
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // Link social account to existing user
                $user->update([
                    $providerIdField => $socialiteUser->getId(),
                    $providerTokenField => $socialiteUser->token,
                    $providerRefreshTokenField => $socialiteUser->refreshToken,
                ]);

                return $user;
            }
        }

        // No user found - return null
        return null;
    }
}
