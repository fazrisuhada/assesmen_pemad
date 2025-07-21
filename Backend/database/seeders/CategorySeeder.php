<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => [
                'Smartphones',
                'Laptops',
                'Cameras',
                'Tablets',
                'Smartwatches',
                'Headphones',
                'Monitors',
                'Gaming Consoles',
            ],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::create([
                'category_name' => $parentName,
                'slug' => Str::slug($parentName),
                'parent_id' => null,
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'category_name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
