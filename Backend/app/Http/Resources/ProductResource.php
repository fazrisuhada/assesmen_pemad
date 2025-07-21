<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'slug' => $this->slug,
            'description' => $this->description,
            'main_image' => $this->main_image,

            'category' => [
                'id' => $this->category->id ?? null,
                'category_name' => $this->category->category_name ?? null,
            ],

            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'product_variant_name' => $variant->product_variant_name,
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                    'variant_image' => $variant->variant_image, 
                ];
            }),

            'reviews' => $this->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user' => [
                        'id' => $review->user->id ?? null,
                        'user_name' => $review->user->user_name ?? null,
                    ],
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
