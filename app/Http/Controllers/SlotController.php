<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SlotController extends Controller
{
    /**
     * Get available time slots for a given date
     */
    public function available($date)
    {
        try {
            if (!$date || !strtotime($date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format.',
                    'slots' => []
                ]);
            }

            $selectedDate = Carbon::parse($date);
            if ($selectedDate->isPast() && !$selectedDate->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot select a past date.',
                    'slots' => []
                ]);
            }

            $possibleTimes = [];
            $startTime = Carbon::createFromTime(9, 0, 0);
            $endTime = Carbon::createFromTime(17, 0, 0);

            while ($startTime->lte($endTime)) {
                $possibleTimes[] = $startTime->format('H:i');
                $startTime->addHour();
            }

            $existingTimes = Slot::where('slot_date', $date)->pluck('slot_time')->toArray();
            $availableSlots = array_diff($possibleTimes, $existingTimes);

            return response()->json([
                'success' => true,
                'message' => count($availableSlots) > 0 ? 'Slots available' : 'No slots available',
                'slots' => array_values($availableSlots),
                'total' => count($availableSlots)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching available slots: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching slots.',
                'slots' => []
            ]);
        }
    }

    /**
     * Display list of slots in admin dashboard
     */
    public function index(Request $request)
    {
        $query = Slot::query();

        $query->fromDate($request->from_date)
              ->applySort($request->sort ?? 'date_asc');

        $slots = $query->paginate($request->get('per_page', 10))->appends($request->query());

        return view('admin.dashboard', [
            'slots' => $slots,
            'filters' => [
                'sort' => $request->sort ?? 'date_asc',
                'from_date' => $request->from_date,
                'per_page' => $request->get('per_page', 10),
            ]
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $pickupPoints = [
            'Main Hospital Entrance',
            'City Center Bus Stop',
            'Railway Station Parking',
            'North Gate Clinic',
            'Community Health Center',
            'Airport Pickup Zone',
        ];

        return view('admin.add_slot', compact('pickupPoints'));
    }

    /**
     * Store new slot
     */
    public function store(Request $request)
    {
        $request->validate([
            'slot_date' => 'required|date|after_or_equal:today',
            'slot_time' => 'required',
            'pickup_point' => 'required|string|max:255',
            'appointment_name.*' => 'nullable|string|max:255',
            'appointment_phone.*' => 'nullable|string|max:20',
        ]);

        $slotDateTime = Carbon::parse($request->slot_date . ' ' . $request->slot_time);
        if ($slotDateTime->isPast()) {
            return back()->withErrors(['slot_time' => 'The appointment date and time must be in the future.'])->withInput();
        }

        // Combine name + phone into array
        $appointments = [];
        if ($request->appointment_name && $request->appointment_phone) {
            foreach ($request->appointment_name as $index => $name) {
                if (!empty($name) && !empty($request->appointment_phone[$index])) {
                    $appointments[] = [
                        'name' => $name,
                        'phone' => $request->appointment_phone[$index],
                    ];
                }
            }
        }

        Slot::create([
            'slot_date' => $request->slot_date,
            'slot_time' => $request->slot_time,
            'pickup_point' => $request->pickup_point,
            'appointment' => json_encode($appointments),
        ]);

        return redirect()->route('dashboard')->with('success', 'Slot added successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
{
    $slot = Slot::findOrFail($id);

    // no need to decode — it’s already an array
    $slot->appointment = $slot->appointment ?? [];

    $pickupPoints = [
        'Main Hospital Entrance',
        'City Center Bus Stop',
        'Railway Station Parking',
        'North Gate Clinic',
        'Community Health Center',
        'Airport Pickup Zone',
    ];

    return view('admin.edit_slot', compact('slot', 'pickupPoints'));
}


    /**
     * Update existing slot
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'slot_date' => 'required|date|after_or_equal:today',
            'slot_time' => 'required',
            'pickup_point' => 'required|string|max:255',
            'appointment_name.*' => 'nullable|string|max:255',
            'appointment_phone.*' => 'nullable|string|max:20',
        ]);

        $slotDateTime = Carbon::parse($request->slot_date . ' ' . $request->slot_time);
        if ($slotDateTime->isPast()) {
            return back()->withErrors(['slot_time' => 'The appointment date and time must be in the future.'])->withInput();
        }

        $appointments = [];
        if ($request->appointment_name && $request->appointment_phone) {
            foreach ($request->appointment_name as $index => $name) {
                if (!empty($name) && !empty($request->appointment_phone[$index])) {
                    $appointments[] = [
                        'name' => $name,
                        'phone' => $request->appointment_phone[$index],
                    ];
                }
            }
        }

        $slot = Slot::findOrFail($id);
        $slot->update([
            'slot_date' => $request->slot_date,
            'slot_time' => $request->slot_time,
            'pickup_point' => $request->pickup_point,
            'appointment' => json_encode($appointments),
        ]);

        return redirect()->route('dashboard')->with('success', 'Slot updated successfully!');
    }

    /**
     * Delete slot
     */
    public function destroy($id)
    {
        $slot = Slot::findOrFail($id);
        $slot->delete();

        return redirect()->route('dashboard')->with('success', 'Slot deleted successfully!');
    }
}
