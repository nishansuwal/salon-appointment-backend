<?php

namespace Database\Seeders;

use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Category;
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

        $profile = StaffProfile::create([
            'user_id' => $staff->id,
            'position' => 'Senior Hair Stylist',
            'avg_rating' => 4.8,
            'experience' => 6,
            'bio' => 'Experienced salon professional specializing in modern hairstyles, coloring, and hair treatments.',
        ]);

        $categoryIds = Category::whereNull('parent_id')
            ->whereIn('name', [
                'Hair',
                'Facial',
            ])
            ->pluck('id');

        $profile->categories()->attach($categoryIds);

        $staff2 = User::where('email', 'staff1234@gmail.com')->first();

        if ($staff2) {
            $profile2 = StaffProfile::create([
                'user_id' => $staff2->id,
                'position' => 'Beauty Specialist',
                'avg_rating' => 4.7,
                'experience' => 5,
                'bio' => 'Professional beauty specialist experienced in facials, skincare, hair styling, and beauty treatments.',
            ]);

            $categoryIds = Category::whereNull('parent_id')
                ->whereIn('name', [
                    'Nails',
                    'Makeup',
                ])
                ->pluck('id');

            $profile2->categories()->attach($categoryIds);
        }
    }
}
