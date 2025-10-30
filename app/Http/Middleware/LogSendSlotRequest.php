<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogSendSlotRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Log request details to storage/logs/laravel.log
        Log::info('🟢 Incoming /send-slots request', [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'input' => $request->all(),
            'time' => now()->toDateTimeString(),
        ]);

        // Also print to Laravel console (if running `php artisan serve`)
        dump('🟢 /send-slots called with input: ', $request->all());

        return $next($request);
    }
}
