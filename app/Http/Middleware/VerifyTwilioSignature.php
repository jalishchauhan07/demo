<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Twilio\Security\RequestValidator;
use Illuminate\Support\Facades\Log;

class VerifyTwilioSignature
{
    public function handle(Request $request, Closure $next)
    {
        $validator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));
        
        $signature = $request->header('X-Twilio-Signature');
        $url = $request->fullUrl();
        $data = $request->all();

        if (!$validator->validate($signature, $url, $data)) {
            Log::error('❌ Invalid Twilio signature', [
                'url' => $url,
                'signature' => $signature
            ]);
            abort(403, 'Invalid request signature');
        }

        Log::info('✅ Twilio signature verified');
        return $next($request);
    }
}