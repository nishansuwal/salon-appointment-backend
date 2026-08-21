<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::create([
            'name' => 'Sarah Johnson',
            'image' => null,
            'message' => 'I had an amazing experience at the salon. The staff were friendly, professional, and made me feel very comfortable. I absolutely love my new hairstyle!',
            'rating' => 5,
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => 'Emily Davis',
            'image' => null,
            'message' => 'The service was excellent from start to finish. The stylist understood exactly what I wanted and the result was even better than I expected.',
            'rating' => 5,
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => 'Sophia Wilson',
            'image' => null,
            'message' => 'A beautiful and relaxing salon with very professional staff. I have been coming here regularly and have always been happy with the service.',
            'rating' => 4,
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => 'Olivia Brown',
            'image' => null,
            'message' => 'The staff were very welcoming and the quality of the service was outstanding. Highly recommended for anyone looking for professional beauty services.',
            'rating' => 5,
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => 'Mia Anderson',
            'image' => null,
            'message' => 'I really enjoyed my visit. The salon was clean, comfortable, and the stylist did a fantastic job. I will definitely be coming back.',
            'rating' => 5,
            'is_active' => true,
        ]);
    }
}