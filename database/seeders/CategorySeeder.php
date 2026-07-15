<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main Categories
        $hair = Category::create([
            'name' => 'Hair',
            'slug' => Str::slug('Hair'),
            'parent_id' => null,
            'is_active' => true,
        ]);

        $nails = Category::create([
            'name' => 'Nails',
            'slug' => Str::slug('Nails'),
            'parent_id' => null,
            'is_active' => true,
        ]);

        $spa = Category::create([
            'name' => 'Spa',
            'slug' => Str::slug('Spa'),
            'parent_id' => null,
            'is_active' => true,
        ]);

        $facial = Category::create([
            'name' => 'Facial',
            'slug' => Str::slug('Facial'),
            'parent_id' => null,
            'is_active' => true,
        ]);

        $makeup = Category::create([
            'name' => 'Makeup',
            'slug' => Str::slug('Makeup'),
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Hair Subcategories
        Category::create([
            'name' => 'Hair Cut',
            'slug' => Str::slug('Hair Cut'),
            'parent_id' => $hair->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Hair Coloring',
            'slug' => Str::slug('Hair Coloring'),
            'parent_id' => $hair->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Hair Treatment',
            'slug' => Str::slug('Hair Treatment'),
            'parent_id' => $hair->id,
            'is_active' => true,
        ]);

        // Nail Subcategories
        Category::create([
            'name' => 'Manicure',
            'slug' => Str::slug('Manicure'),
            'parent_id' => $nails->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Pedicure',
            'slug' => Str::slug('Pedicure'),
            'parent_id' => $nails->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Nail Art',
            'slug' => Str::slug('Nail Art'),
            'parent_id' => $nails->id,
            'is_active' => true,
        ]);

        // Spa Subcategories
        Category::create([
            'name' => 'Body Massage',
            'slug' => Str::slug('Body Massage'),
            'parent_id' => $spa->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Aromatherapy',
            'slug' => Str::slug('Aromatherapy'),
            'parent_id' => $spa->id,
            'is_active' => true,
        ]);

        // Facial Subcategories
        Category::create([
            'name' => 'Basic Facial',
            'slug' => Str::slug('Basic Facial'),
            'parent_id' => $facial->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Gold Facial',
            'slug' => Str::slug('Gold Facial'),
            'parent_id' => $facial->id,
            'is_active' => true,
        ]);

        // Makeup Subcategories
        Category::create([
            'name' => 'Bridal Makeup',
            'slug' => Str::slug('Bridal Makeup'),
            'parent_id' => $makeup->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Party Makeup',
            'slug' => Str::slug('Party Makeup'),
            'parent_id' => $makeup->id,
            'is_active' => true,
        ]);
    }
}
