<?php

namespace App\Livewire\Auth;

use App\Services\SocialLoginService;
use App\Traits\WithToastNotifications;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    use WithToastNotifications;

    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            $this->showErrorToast(__('auth.failed'));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->showSuccessToast(__('auth.login_success', ['name' => Auth::user()->name]));
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Redirect to social login provider
     */
    public function socialLogin(string $provider): void
    {
        try {
            $socialLoginService = app(SocialLoginService::class);

            if (! $socialLoginService->isProviderEnabled($provider)) {
                $this->showErrorToast('Social login is not enabled for this provider.');

                return;
            }

            $this->redirect(route('social.redirect', $provider));
        } catch (\Exception) {
            $this->showErrorToast('Unable to process social login. Please try again.');
        }
    }

    /**
     * Get enabled social login providers
     */
    public function getEnabledProvidersProperty(): array
    {
        try {
            $socialLoginService = app(SocialLoginService::class);

            return $socialLoginService->getEnabledProviders();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->showErrorToast(__('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
