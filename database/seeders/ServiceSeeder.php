<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Hair
            [
                'category' => 'Hair Cut',
                'name' => 'Men Hair Cut',
                'description' => 'Professional haircut for men.',
                'duration' => 30,
                'price' => 500,
            ],
            [
                'category' => 'Hair Cut',
                'name' => 'Women Hair Cut',
                'description' => 'Stylish haircut for women.',
                'duration' => 60,
                'price' => 1000,
            ],
            [
                'category' => 'Hair Coloring',
                'name' => 'Hair Coloring',
                'description' => 'Full hair coloring with premium products.',
                'duration' => 120,
                'price' => 3500,
            ],
            [
                'category' => 'Hair Treatment',
                'name' => 'Keratin Treatment',
                'description' => 'Smooth and repair damaged hair.',
                'duration' => 180,
                'price' => 6000,
            ],

            // Nails
            [
                'category' => 'Manicure',
                'name' => 'Classic Manicure',
                'description' => 'Nail trimming, shaping, and polish.',
                'duration' => 45,
                'price' => 800,
            ],
            [
                'category' => 'Pedicure',
                'name' => 'Classic Pedicure',
                'description' => 'Foot care with massage and polish.',
                'duration' => 60,
                'price' => 1200,
            ],
            [
                'category' => 'Nail Art',
                'name' => 'Nail Art Design',
                'description' => 'Creative nail art designs.',
                'duration' => 60,
                'price' => 1500,
            ],

            // Spa
            [
                'category' => 'Body Massage',
                'name' => 'Full Body Massage',
                'description' => 'Relaxing full-body massage.',
                'duration' => 90,
                'price' => 3000,
            ],
            [
                'category' => 'Aromatherapy',
                'name' => 'Aromatherapy Session',
                'description' => 'Essential oil therapy for relaxation.',
                'duration' => 60,
                'price' => 2500,
            ],

            // Facial
            [
                'category' => 'Basic Facial',
                'name' => 'Basic Facial',
                'description' => 'Deep cleansing facial treatment.',
                'duration' => 60,
                'price' => 1800,
            ],
            [
                'category' => 'Gold Facial',
                'name' => 'Gold Facial',
                'description' => 'Premium gold facial for glowing skin.',
                'duration' => 90,
                'price' => 3500,
            ],

            // Makeup
            [
                'category' => 'Bridal Makeup',
                'name' => 'Bridal Makeup Package',
                'description' => 'Complete bridal makeup service.',
                'duration' => 180,
                'price' => 15000,
            ],
            [
                'category' => 'Party Makeup',
                'name' => 'Party Makeup',
                'description' => 'Professional makeup for parties and events.',
                'duration' => 90,
                'price' => 4000,
            ],
        ];

        foreach ($services as $service) {
            $category = Category::where('name', $service['category'])->first();

            if (! $category) {
                continue;
            }

            Service::create([
                'category_id' => $category->id,
                'name' => $service['name'],
                'slug' => Str::slug($service['name']),
                'description' => $service['description'],
                'duration_minutes' => $service['duration'],
                'price' => $service['price'],
                'is_active' => true,
            ]);
        }
    }
}
