<?php

namespace Database\Seeders;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = User::where('email', 'staff123@gmail.com')->first();

        if (! $staff) {
            return;
        }

        StaffProfile::create([
            'user_id' => $staff->id,
            'position' => 'Senior Hair Stylist',
            'specialization' => 'Hair Cut, Hair Coloring, Hair Treatment',
            'avg_rating' => 4.8,
            'experience' => 6,
            'bio' => 'Experienced salon professional specializing in modern hairstyles, coloring, and hair treatments.',
        ]);
    }
}