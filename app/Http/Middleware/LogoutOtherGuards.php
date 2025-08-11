<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LogoutOtherGuards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $currentGuard  Guard yang sedang dipakai untuk login
     */
    public function handle($request, Closure $next, $currentGuard)
    {
        // Ambil semua guard yang ada di config/auth.php
        $guards = array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            if ($guard !== $currentGuard && Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        // Amankan session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $next($request);
    }
}
