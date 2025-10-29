<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use Twilio\Rest\Client;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    public function sendSlots(Request $request)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_WHATSAPP_NUMBER');
        $to = 'whatsapp:' . $request->to; // user WhatsApp number

        $client = new Client($sid, $token);

        $slots = Slot::whereDate('slot_date', '>=', Carbon::today())->get();

        if ($slots->isEmpty()) {
            $message = "No upcoming slots available.";
        } else {
            $message = "📅 *Available Appointment Slots:*\n\n";
            foreach ($slots as $slot) {
                $message .= "🆔 ID: {$slot->id}\n";
                $message .= "📅 Date: {$slot->slot_date->format('Y-m-d')}\n";
                $message .= "⏰ Time: {$slot->slot_time}\n";
                $message .= "📍 Pickup: {$slot->pickup_point}\n\n";
            }
            $message .= "Reply with:\n👉 *BOOK <Slot_ID> <Your_Name> <Your_Phone>*\n";
            $message .= "_Example:_ BOOK 1 John +911234567890";
        }

        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);

        return response()->json(['status' => 'sent']);
    }

    public function receiveMessage(Request $request)
{
    $body = trim($request->input('Body'));
    $from = $request->input('From'); // "whatsapp:+911234567890"
    $cleanPhone = str_replace('whatsapp:', '', $from);

    $sid = env('TWILIO_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $fromNumber = env('TWILIO_WHATSAPP_NUMBER');
    $client = new Client($sid, $token);

    // Parse the message
    if (str_starts_with(strtoupper($body), 'BOOK')) {
        $parts = explode(' ', $body, 4);

        if (count($parts) < 4) {
            $client->messages->create($from, [
                'from' => $fromNumber,
                'body' => "❌ Invalid format. Use:\nBOOK <Slot_ID> <Your_Name> <Your_Phone>"
            ]);
            return;
        }

        [$cmd, $slotId, $name, $phone] = $parts;

        $slot = Slot::find($slotId);

        if (!$slot) {
            $client->messages->create($from, [
                'from' => $fromNumber,
                'body' => "❌ Slot not found. Please try another ID."
            ]);
            return;
        }

        // Store appointment
        $appointments = $slot->appointment ?? [];
        $appointments[] = [
            'name' => $name,
            'phone' => $phone,
        ];

        $slot->appointment = $appointments;
        $slot->save();

        // Confirmation message
        $confirm = "✅ Appointment confirmed for *$name*\n"
                 . "📅 Date: {$slot->slot_date->format('Y-m-d')}\n"
                 . "⏰ Time: {$slot->slot_time}\n"
                 . "📍 Pickup: {$slot->pickup_point}\n\n"
                 . "Thank you for booking!";

        $client->messages->create($from, [
            'from' => $fromNumber,
            'body' => $confirm
        ]);
    } else {
        // Default reply
        $client->messages->create($from, [
            'from' => $fromNumber,
            'body' => "👋 Welcome! To book an appointment, reply with:\nBOOK <Slot_ID> <Your_Name> <Your_Phone>"
        ]);
    }
}

}
