<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Create Admin User ---
        DB::table('admins')->insert([
            'username'   => 'admin',
            'password'   => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- Sample Slots ---
        $slots = [
            ['2025-10-26', '09:00:00', 'Main Gate'],
            ['2025-10-26', '10:00:00', 'North Entrance'],
            ['2025-10-26', '11:00:00', 'Reception'],
            ['2025-10-27', '09:30:00', 'Block A'],
            ['2025-10-27', '10:30:00', 'Main Gate'],
            ['2025-10-27', '11:30:00', 'Reception'],
            ['2025-10-28', '09:00:00', 'Block B'],
            ['2025-10-28', '10:00:00', 'Reception'],
            ['2025-10-28', '11:00:00', 'North Entrance'],
            ['2025-10-29', '14:00:00', 'Main Gate'],
            ['2025-10-29', '15:00:00', 'Reception'],
            ['2025-10-29', '16:00:00', 'Block A'],
        ];

        // --- Insert Slots with Appointment Array ---
        foreach ($slots as $index => $slot) {
            // Example appointments for each slot
            $appointments = [
                ['name' => 'User ' . ($index * 2 + 1), 'phone' => '+91987654000' . $index],
                ['name' => 'User ' . ($index * 2 + 2), 'phone' => '+91987654001' . $index],
            ];

            DB::table('slots')->insert([
                'slot_date'     => $slot[0],
                'slot_time'     => $slot[1],
                'pickup_point'  => $slot[2],
                'appointment'   => json_encode($appointments), // store array of name+phone
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
