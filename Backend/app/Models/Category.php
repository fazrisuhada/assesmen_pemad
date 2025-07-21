<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Relasi: Satu kategori punya banyak produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi: Subkategori punya parent.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi: Kategori punya banyak subkategori.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
