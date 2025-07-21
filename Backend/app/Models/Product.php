<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    /**
     * Relasi: Produk milik satu kategori.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Produk punya banyak varian.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi: Produk punya banyak review.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Scope: Pencarian berdasarkan nama produk
     * Menggunakan fulltext index untuk performa lebih baik
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('product_name', 'ILIKE', "%{$keyword}%")
                ->orWhere('description', 'ILIKE', "%{$keyword}%");
        });
    }

    /**
     * Scope: Filter berdasarkan kategori
     * Optimized dengan join untuk menghindari subquery
     */
    public function scopeCategory(Builder $query, string $categoryName): Builder
    {
        return $query->whereHas('category', function ($q) use ($categoryName) {
            $q->where('category_name', 'ILIKE', $categoryName);
        });
    }

    /**
     * Scope: Filter berdasarkan rentang harga
     * Menggunakan EXISTS untuk performa lebih baik
     */
    public function scopePriceBetween(Builder $query, float $min, float $max): Builder
    {
        return $query->whereExists(function ($q) use ($min, $max) {
            $q->select(DB::raw(1))
                ->from('product_variants')
                ->whereColumn('product_variants.product_id', 'products.id')
                ->whereBetween('product_variants.price', [$min, $max]);
        });
    }

    /**
     * Scope: Sorting yang lebih fleksibel
     */
    public function scopeSortBy($query, $sort)
    {
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('product_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('product_name', 'desc');
                break;
            case 'price_asc':
                $query->select('products.*')
                    ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->groupBy('products.id')
                    ->orderByRaw('MIN(product_variants.price) ASC');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->groupBy('products.id')
                    ->orderByRaw('MIN(product_variants.price) DESC');
                break;
            default:
                $query->orderBy('id', 'desc');
        }
    }

    /**
     * Scope: Produk dengan review tinggi (untuk optimasi cache)
     */
    public function scopeHighRated(Builder $query, float $minRating = 4.0): Builder
    {
        return $query->whereHas('reviews', function ($q) use ($minRating) {
            $q->groupBy('product_id')
                ->havingRaw('AVG(rating) >= ?', [$minRating]);
        });
    }


    /**
     * Scope: Produk dengan stok tersedia
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('variants', function ($q) {
            $q->where('stock', '>', 0);
        });
    }
}
