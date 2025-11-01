<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Slot;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    /**
     * ✅ Step 1: Verify webhook (GET)
     * Called once by Meta when setting up the webhook
     */
    public function verifyWebhook(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN');
        $mode = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode && $token === $verifyToken) {
            Log::info('✅ Webhook verified successfully');
            return response($challenge, 200);
        }

        Log::warning('❌ Webhook verification failed');
        return response('Forbidden', 403);
    }

    /**
     * ✅ Step 2: Handle incoming messages (POST)
     */
    public function receiveMessage(Request $request)
    {
        Log::info('📩 Incoming WhatsApp message', $request->all());

        try {
            $data = $request->all();

            if (!isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
                return response('No message found', 200);
            }

            $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $message['from']; // WhatsApp number
            $text = $message['text']['body'] ?? '';

            Log::info("💬 Message from $from: $text");

            if (stripos($text, 'book') === 0) {
                return $this->handleBooking($from, $text);
            }

            return $this->sendWhatsAppMessage($from, "👋 Welcome! To book an appointment, reply with:\nBOOK <Date:YYYY-MM-DD> <Time:HH:MM> <Your_Name> <Your_Phone>");
        } catch (\Exception $e) {
            Log::error('❌ Error handling message', ['error' => $e->getMessage()]);
            return response('Error', 500);
        }
    }

    /**
     * ✅ Handle BOOK <date> <time> <name> <phone>
     */
    private function handleBooking($from, $body)
    {
        try {
            $parts = explode(' ', $body, 5);
            if (count($parts) < 5) {
                return $this->sendWhatsAppMessage($from, "❌ Invalid format. Use:\nBOOK <Date:YYYY-MM-DD> <Time:HH:MM> <Your_Name> <Your_Phone>");
            }

            [$cmd, $date, $time, $name, $phone] = $parts;

            // Validate date and time
            if (!Carbon::hasFormat($date, 'Y-m-d') || !preg_match('/^\d{2}:\d{2}$/', $time)) {
                return $this->sendWhatsAppMessage($from, "❌ Invalid format. Please use:\nBOOK 2025-11-01 10:30 John +911234567890");
            }

            // Find or create slot
            $slot = Slot::firstOrCreate(
                ['slot_date' => $date, 'slot_time' => $time],
                ['pickup_point' => 'Default Pickup', 'appointment' => []]
            );

            $appointments = $slot->appointment ?? [];

            foreach ($appointments as $appt) {
                if ($appt['phone'] === $phone) {
                    return $this->sendWhatsAppMessage($from, "⚠️ You already booked this slot.");
                }
            }

            // Add booking
            $appointments[] = ['name' => $name, 'phone' => $phone];
            $slot->appointment = $appointments;
            $slot->save();

            $confirm = "✅ Appointment confirmed for *$name*\n"
                . "📅 Date: $date\n"
                . "⏰ Time: $time\n"
                . "📍 Pickup: {$slot->pickup_point}\n\n"
                . "Thank you for booking!";

            return $this->sendWhatsAppMessage($from, $confirm);
        } catch (\Exception $e) {
            Log::error('❌ Booking error', ['error' => $e->getMessage()]);
            return $this->sendWhatsAppMessage($from, "⚠️ Error booking your slot. Please try again later.");
        }
    }

    /**
     * ✅ Send WhatsApp message via Cloud API
     */
    private function sendWhatsAppMessage($to, $message)
    {
        $token = env('WHATSAPP_ACCESS_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');

        $response = Http::withToken($token)->post("https://graph.facebook.com/v21.0/{$phoneId}/messages", [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message],
        ]);

        Log::info('📤 Sent WhatsApp message', [
            'to' => $to,
            'body' => $message,
            'response' => $response->json()
        ]);

        return response('Message sent', 200);
    }
}
