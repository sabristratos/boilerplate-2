<?php

declare(strict_types=1);

namespace App\Providers;

use App\Facades\Settings;
use App\Observers\MediaObserver;
use App\Services\SettingsManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SettingsManager as both a singleton and a class binding
        $this->app->singleton(SettingsManager::class, fn (): SettingsManager => new SettingsManager);
        $this->app->singleton('settings', fn (): SettingsManager => new SettingsManager);

        // Register BlockManager as a singleton
        $this->app->singleton(\App\Services\BlockManager::class, fn (): \App\Services\BlockManager => new \App\Services\BlockManager);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only apply settings to views and config when not in console mode
        if (! $this->app->runningInConsole()) {
            try {
                View::share('headerLinks', Settings::get('navigation.header_links', []));
                View::share('footerLinks', Settings::get('navigation.footer_links', []));

                Settings::get('appearance.primary_color', 'oklch(64.5% .246 16.439)');
                Settings::get('appearance.theme', 'light');
            } catch (\Exception $e) {
                // If settings are not available (e.g., during migrations), use defaults
                View::share('headerLinks', []);
                View::share('footerLinks', []);
            }
        }

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(fn ($user, $ability): ?true => $user->hasRole('Super Admin') ? true : null);

        // Register media observer for automatic image optimization
        Media::observe(MediaObserver::class);

        // Schedule sitemap generation if enabled
        try {
            if (Settings::get('seo.sitemap_enabled', true)) {
                $frequency = Settings::get('seo.sitemap_update_frequency', 'weekly');

                $schedule = app(Schedule::class);

                switch ($frequency) {
                    case 'daily':
                        $schedule->command('sitemap:generate')->daily();
                        break;
                    case 'weekly':
                        $schedule->command('sitemap:generate')->weekly();
                        break;
                    case 'monthly':
                        $schedule->command('sitemap:generate')->monthly();
                        break;
                }
            }
        } catch (\Exception $e) {
            // If settings are not available, skip scheduling
        }
    }
}
