<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereHas('parent', function ($q) {
            $q->where('category_name', 'Electronics');
        })->orWhere('category_name', 'Electronics')->get();

        $users = User::all();

        $batchSize = 5000;
        $totalProducts = 500000;

        for ($i = 0; $i < $totalProducts; $i += $batchSize) {

            $products = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $brand = fake()->randomElement([
                    'Samsung', 'Apple', 'Sony', 'Asus', 'Acer', 'Lenovo',
                    'Canon', 'Nikon', 'Huawei', 'Xiaomi'
                ]);
                $productType = fake()->randomElement([
                    'Smartphone', 'Laptop', 'Camera', 'Tablet',
                    'Smartwatch', 'Monitor', 'Headphones', 'Gaming Console'
                ]);
                $adjective = fake()->randomElement([
                    'Pro', 'Ultra', 'Max', 'Lite', 'Plus', 'Series X', 'Classic'
                ]);

                $name = "{$brand} {$productType} {$adjective}";

                $products[] = [
                    'product_name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::random(5),
                    'description' => fake()->paragraph(),
                    'category_id' => $categories->random()->id,
                    'main_image' => "https://placehold.co/600x400?text=" . urlencode($name),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Product::insert($products);

            $productIds = Product::latest('id')->take($batchSize)->pluck('id');

            foreach ($productIds as $productId) {
                $variantCount = rand(1, 3);
                $variants = [];

                for ($k = 0; $k < $variantCount; $k++) {
                    $variantName = fake()->randomElement([
                        'Black', 'White', 'Silver', 'Gold',
                        '64GB', '128GB', '256GB',
                        '4GB RAM', '8GB RAM', '16GB RAM',
                        'Standard Edition', 'Limited Edition'
                    ]);

                    $variants[] = [
                        'product_id' => $productId,
                        'sku' => strtoupper(Str::random(12)),
                        'product_variant_name' => "Variant {$variantName}",
                        'price' => fake()->randomFloat(2, 5_000_000, 100_000_000),
                        'stock' => rand(0, 500),
                        'variant_image' => "https://placehold.co/400x400?text=" . urlencode($variantName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ProductVariant::insert($variants);

                $reviewCount = rand(0, 5);
                $reviews = [];

                for ($k = 0; $k < $reviewCount; $k++) {
                    $reviews[] = [
                        'product_id' => $productId,
                        'user_id' => $users->random()->id,
                        'rating' => rand(1, 5),
                        'comment' => fake()->sentence(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($reviews)) {
                    ProductReview::insert($reviews);
                }
            }

            echo "Batch " . ($i + $batchSize) . " inserted...\n";
        }
    }
}
