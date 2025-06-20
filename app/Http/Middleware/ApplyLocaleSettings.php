<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyLocaleSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = Settings::get('general.default_locale', config('app.locale'));
        $fallbackLocale = Settings::get('general.fallback_locale', config('app.fallback_locale'));

        config([
            'app.locale' => $defaultLocale,
            'app.fallback_locale' => $fallbackLocale,
        ]);

        app()->setLocale($defaultLocale);

        return $next($request);
    }
} 