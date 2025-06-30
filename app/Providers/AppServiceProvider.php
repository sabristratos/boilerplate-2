<?php

namespace App\Providers;

use App\Facades\Settings;
use App\Services\SettingsManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app->singleton('settings', fn (): \App\Services\SettingsManager => new SettingsManager);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        View::share('headerLinks', Settings::get('navigation.header_links', []));
        View::share('footerLinks', Settings::get('navigation.footer_links', []));

        Settings::get('appearance.primary_color', 'oklch(64.5% .246 16.439)');
        Settings::get('appearance.theme', 'light');

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(fn ($user, $ability): ?true => $user->hasRole('Super Admin') ? true : null);
    }
}
