<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use App\Facades\Settings;

class ApplySettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apply app name setting
        if ($appName = Settings::get('general.app_name')) {
            Config::set('app.name', $appName);
        }

        // Apply app URL setting
        if ($appUrl = Settings::get('general.app_url')) {
            Config::set('app.url', $appUrl);
        }

        // Apply mail settings
        if ($mailDriver = Settings::get('email.driver')) {
            Config::set('mail.default', $mailDriver);
        }

        if ($mailHost = Settings::get('email.host')) {
            Config::set('mail.mailers.smtp.host', $mailHost);
        }

        if ($mailPort = Settings::get('email.port')) {
            Config::set('mail.mailers.smtp.port', $mailPort);
        }

        if ($mailUsername = Settings::get('email.username')) {
            Config::set('mail.mailers.smtp.username', $mailUsername);
        }

        if ($mailPassword = Settings::get('email.password')) {
            Config::set('mail.mailers.smtp.password', $mailPassword);
        }

        if ($mailEncryption = Settings::get('email.encryption')) {
            Config::set('mail.mailers.smtp.encryption', $mailEncryption === 'null' ? null : $mailEncryption);
        }

        if ($mailFromAddress = Settings::get('email.from_address')) {
            Config::set('mail.from.address', $mailFromAddress);
        }

        if ($mailFromName = Settings::get('email.from_name')) {
            Config::set('mail.from.name', $mailFromName);
        }

        // Apply timezone setting
        if ($timezone = Settings::get('advanced.timezone')) {
            Config::set('app.timezone', $timezone);
            date_default_timezone_set($timezone);
        }

        // Apply cache driver setting
        if ($cacheDriver = Settings::get('advanced.cache_driver')) {
            Config::set('cache.default', $cacheDriver === 'null' ? null : $cacheDriver);
        }

        // Apply session driver setting
        if ($sessionDriver = Settings::get('advanced.session_driver')) {
            Config::set('session.driver', $sessionDriver);
        }

        return $next($request);
    }
}
