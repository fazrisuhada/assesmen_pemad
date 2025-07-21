<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|in:price_asc,price_desc,name_asc,name_desc,product_name_asc,product_name_desc',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        // Cache key yang lebih spesifik
        $cacheKey = 'products_' . md5(serialize($request->only([
            'search', 'category', 'min_price', 'max_price', 'sort', 'page', 'limit'
        ])));

        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Product::with(['category', 'variants']);

            // Apply scopes
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('category')) {
                $query->category($request->category);
            }

            if ($request->filled('min_price') || $request->filled('max_price')) {
                $minPrice = $request->get('min_price', 0);
                $maxPrice = $request->get('max_price', PHP_INT_MAX);
                $query->priceBetween($minPrice, $maxPrice);
            }

            if ($request->filled('sort')) {
                $sortValue = $request->sort;
                
                if (str_contains($sortValue, 'product_name_')) {
                    $sortValue = str_replace('product_name_', 'name_', $sortValue);
                }
                
                $query->sortBy($sortValue);
            } else {
                $query->orderBy('id', 'desc'); // Default sorting
            }

            $limit = min($request->get('limit', 10), 100); // Max 100 items per page

            return $query->paginate($limit);
        });

        return ProductResource::collection($products);
    }
}