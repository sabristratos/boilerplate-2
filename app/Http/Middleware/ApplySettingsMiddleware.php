<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class ApplySettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if the settings table exists to avoid errors during migrations
            if (Schema::hasTable('settings')) {
                $settings = Settings::getAll();
                foreach ($settings as $key => $setting) {
                    $settingConfig = Config::get("settings.settings.{$key}");
                    if (isset($settingConfig['config'])) {
                        Config::set($settingConfig['config'], $setting['value']);
                    }
                }
            }
        } catch (QueryException $e) {
            // This can happen if the database connection is not available yet
            // or if migrations haven't run. In this case, we'll just proceed
            // with the default config values.
        }

        return $next($request);
    }
}
