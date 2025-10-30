<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Twilio\Security\RequestValidator;

use Twilio\TwiML\MessagingResponse;

// Set the content-type to XML to send back TwiML from the PHP SDK
header("content-type: text/xml");

$response = new MessagingResponse();
$response->message(
	"Message received! Hello again from the Twilio Sandbox for WhatsApp."
);

echo $response;

use Illuminate\Support\Facades\Log;

Log::info('Twilio WhatsApp FROM:', ['from' => env('TWILIO_WHATSAPP_FROM')]);


class WhatsAppController extends Controller
{
    public function sendSlots(Request $request)
{
    Log::info('Form submitted:', ['to' => $request->input('to')]);
    // Log::info('🟢 sendSlots() called', ['request_data' => $request->all()]);

    try {
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_WHATSAPP_NUMBER');
        $to = 'whatsapp:' . $request->to;

        Log::info('🔑 Twilio credentials loaded', [
            'sid' => substr($sid, 0, 6) . '...', // mask for safety
            'from' => $from,
            'to' => $to,
        ]);

        $client = new Client($sid, $token);

        $slots = Slot::whereDate('slot_date', '>=', Carbon::today())->get();
        Log::info('📅 Slots fetched', ['count' => $slots->count()]);

        if ($slots->isEmpty()) {
            $message = "No upcoming slots available.";
        } else {
            $message = "📅 *Available Appointment Slots:*\n\n";
            foreach ($slots as $slot) {
                $message .= "🆔 ID: {$slot->id}\n";
                $message .= "📅 Date: {$slot->slot_date}\n";
                $message .= "⏰ Time: {$slot->slot_time}\n";
                $message .= "📍 Pickup: {$slot->pickup_point}\n\n";
            }
            $message .= "Reply with:\n👉 *BOOK <Slot_ID> <Your_Name> <Your_Phone>*\n";
            $message .= "_Example:_ BOOK 1 John +911234567890";
        }

        Log::info('📤 Sending WhatsApp message...', ['message_preview' => substr($message, 0, 100)]);

        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);

        Log::info('✅ Message sent successfully to user', ['to' => $to]);

        return response()->json(['status' => 'sent', 'to' => $to]);
    } catch (Exception $e) {
        Log::error('❌ Error in sendSlots()', [
            'error_message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}


public function receiveMessage(Request $request)
{
    $token = $request->session()->token();
    $token = csrf_token();
    // ✅ Verify Twilio webhook authenticity
    $validator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));
    $twilioSignature = $request->header('X-Twilio-Signature');
    $url = $request->fullUrl();
    $params = $request->all();

    if (!$validator->validate($twilioSignature, $url, $params)) {
        \Log::warning('❌ Invalid Twilio Signature', [
            'url' => $url,
            'signature' => $twilioSignature,
            'params' => $params
        ]);
        return response('Forbidden', 403);
    }

    \Log::info('✅ Valid Twilio webhook received', ['from' => $request->input('From')]);

    // 🧠 Then continue with your existing logic
    try {
        $body = trim($request->input('Body'));
        $from = $request->input('From');
        $cleanPhone = str_replace('whatsapp:', '', $from);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $fromNumber = env('TWILIO_WHATSAPP_NUMBER');
        $client = new \Twilio\Rest\Client($sid, $token);

        // 🧩 Validate incoming message
        if (empty($body)) {
            \Log::warning('⚠️ Empty WhatsApp body received', ['from' => $cleanPhone]);
            return response('No message body', 400);
        }

        // 🧩 Command check
        if (!str_starts_with(strtoupper($body), 'BOOK')) {
            $client->messages->create($from, [
                'from' => $fromNumber,
                'body' => "👋 Welcome! To book an appointment, reply with:\nBOOK <Date:YYYY-MM-DD> <Time:HH:MM> <Your_Name> <Your_Phone>"
            ]);
            return response('Default reply sent');
        }

        // 🧩 Parse the BOOK command
        $parts = explode(' ', $body, 5);
        if (count($parts) < 5) {
            $client->messages->create($from, [
                'from' => $fromNumber,
                'body' => "❌ Invalid format. Use:\nBOOK <Date:YYYY-MM-DD> <Time:HH:MM> <Your_Name> <Your_Phone>"
            ]);
            return response('Invalid format', 400);
        }

        [$cmd, $date, $time, $name, $phone] = $parts;

        // 🧩 Validate date & time format
        if (!\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
            throw new \Exception("Invalid date format: $date");
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            throw new \Exception("Invalid time format: $time");
        }

        // ✅ Check if slot exists
        $slot = \App\Models\Slot::whereDate('slot_date', $date)
                ->where('slot_time', $time)
                ->first();

        if (!$slot) {
            // ✅ Create new slot
            $slot = new \App\Models\Slot();
            $slot->slot_date = $date;
            $slot->slot_time = $time;
            $slot->pickup_point = 'Default Pickup'; // You can modify
            $slot->appointment = [];
            $slot->save();

            \Log::info('🆕 Created new slot', ['date' => $date, 'time' => $time]);
        }

        // ✅ Prevent duplicate bookings
        $appointments = $slot->appointment ?? [];
        foreach ($appointments as $appt) {
            if ($appt['phone'] === $phone) {
                $client->messages->create($from, [
                    'from' => $fromNumber,
                    'body' => "⚠️ You already booked this slot."
                ]);
                return response('Duplicate booking prevented');
            }
        }

        // ✅ Push new appointment
        $appointments[] = [
            'name' => $name,
            'phone' => $phone,
        ];

        $slot->appointment = $appointments;
        $slot->save();

        // ✅ Send confirmation message
        $confirm = "✅ Appointment confirmed for *$name*\n"
                 . "📅 Date: {$slot->slot_date}\n"
                 . "⏰ Time: {$slot->slot_time}\n"
                 . "📍 Pickup: {$slot->pickup_point}\n\n"
                 . "Thank you for booking!";

        $client->messages->create($from, [
            'from' => $fromNumber,
            'body' => $confirm
        ]);

        \Log::info('✅ Appointment stored successfully', [
            'slot_id' => $slot->id,
            'user' => $name,
            'phone' => $phone
        ]);

        return response('Success', 200);

    } catch (\Twilio\Exceptions\RestException $twilioError) {
        \Log::error('❌ Twilio API error', ['message' => $twilioError->getMessage()]);
        return response('Twilio API Error: ' . $twilioError->getMessage(), 500);

    } catch (\Exception $e) {
        \Log::error('❌ Error in receiveMessage()', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        try {
            // Send error reply to user
            if (isset($client) && isset($fromNumber)) {
                $client->messages->create($from ?? '', [
                    'from' => $fromNumber,
                    'body' => "⚠️ An error occurred while processing your booking. Please try again later."
                ]);
            }
        } catch (\Exception $twilioFail) {
            \Log::error('❌ Failed to send error message to WhatsApp', [
                'message' => $twilioFail->getMessage()
            ]);
        }

        return response('Error: ' . $e->getMessage(), 500);
    }
}


}
