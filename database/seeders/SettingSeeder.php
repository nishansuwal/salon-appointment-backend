<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'name'         => 'Glamour Salon',
            'email'        => 'info@glamoursalon.com',
            'phone'        => '+977-9800000000',
            'address'      => 'Bhaktapur, Nepal',
            'logo'         => null,
            'opening_time' => '09:00:00',
            'closing_time' => '19:00:00',
        ]);
    }
}
