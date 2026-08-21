<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'email' => 'user123@gmail.com',
            'password' => bcrypt('user123'),
            'phone' => '1234567890',
            'role' => 'user',
        ]);
        User::factory()->create([
            'name' => 'User2',
            'email' => 'user1234@gmail.com',
            'password' => bcrypt('user123'),
            'phone' => '1234567890',
            'role' => 'user',
        ]);
        User::factory()->create([
            'name' => 'User3',
            'email' => 'user12345@gmail.com',
            'password' => bcrypt('user123'),
            'phone' => '1234567890',
            'role' => 'user',
        ]);
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin123@gmail.com',
            'password' => bcrypt('admin123'),
            'phone' => '1234567890',
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Staff',
            'email' => 'staff123@gmail.com',
            'password' => bcrypt('staff123'),
            'phone' => '1234567890',
            'role' => 'staff',
        ]);
        User::factory()->create([
            'name' => 'Staff2',
            'email' => 'staff1234@gmail.com',
            'password' => bcrypt('staff123'),
            'phone' => '1234567890',
            'role' => 'staff',
        ]);
        User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            SettingSeeder::class,
            FaqSeeder::class,
            ServiceSeeder::class,
            StaffProfileSeeder::class,
            StaffAvailabilitySeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
