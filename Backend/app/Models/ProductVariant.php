<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * Relasi: Varian milik satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
