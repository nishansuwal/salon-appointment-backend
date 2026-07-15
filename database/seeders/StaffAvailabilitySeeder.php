<?php

namespace Database\Seeders;

use App\Models\StaffAvailability;
use App\Models\StaffProfile;
use Illuminate\Database\Seeder;

class StaffAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffProfiles = StaffProfile::all();

        $schedule = [
            ['day_of_week' => 'mon', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
            ['day_of_week' => 'tue', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
            ['day_of_week' => 'wed', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
            ['day_of_week' => 'thu', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
            ['day_of_week' => 'fri', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
            ['day_of_week' => 'sat', 'start_time' => '10:00:00', 'end_time' => '16:00:00'],
            // Sunday is off
        ];

        foreach ($staffProfiles as $staff) {
            foreach ($schedule as $day) {
                StaffAvailability::create([
                    'staff_id' => $staff->id,
                    'day_of_week' => $day['day_of_week'],
                    'start_time' => $day['start_time'],
                    'end_time' => $day['end_time'],
                ]);
            }
        }
    }
}