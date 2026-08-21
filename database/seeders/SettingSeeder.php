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
            'title'        => 'A calm salon workflow for clients and staff',
            'bio'          => 'Thoughtful beauty appointments, handled end to end.',
            'description'  => 'Glamour Salon uses a simple appointment flow so clients can compare services, select the right specialist, and request an appointment without calling the front desk.',
            'opening_time' => '09:00:00',
            'closing_time' => '19:00:00',
        ]);
    }
}
