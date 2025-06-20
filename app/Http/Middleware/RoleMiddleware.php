<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string|null  $role
     * @param  string|null  $guard
     */
    public function handle(Request $request, Closure $next, $role = null, $guard = null): Response
    {
        if (Auth::guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        if (! Auth::guard($guard)->user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
