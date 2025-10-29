<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Session timeout in seconds
            $timeout = 60 * 60; // 1 hour
            $lastActivity = session('last_activity', now());

            // Check if session expired
            if (now()->diffInSeconds($lastActivity) > $timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }

            // Update last activity timestamp
            session(['last_activity' => now()]);
        }

        return $next($request);
    }
}
