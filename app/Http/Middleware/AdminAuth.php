<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if session exists
        if (!session('admin') || !session('admin_token')) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // 2. Validate token
        $admin = Admin::where('auth_token', session('admin_token'))->first();

        if (!$admin) {
            // Invalid token
            session()->flush();
            return redirect()->route('login')->with('error', 'Invalid session. Please login again.');
        }

        // 3. Check token expiry
        // Assuming you have a `token_expiry` datetime column in your admins table
        if ($admin->token_expiry && now()->greaterThan($admin->token_expiry)) {
            session()->flush();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // 4. Optionally, refresh token expiry on each request
        // $admin->update(['token_expiry' => now()->addHour()]); // uncomment if you want rolling expiry

        return $next($request);
    }
}
