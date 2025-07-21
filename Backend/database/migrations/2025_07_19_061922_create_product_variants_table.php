<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned()->index(); // index FK ke produk
            $table->string('sku')->unique(); // SKU unik & indexed
            $table->string('product_variant_name')->index(); // index nama varian
            $table->decimal('price', 12, 2)->index(); // index harga
            $table->integer('stock')->default(0)->index(); // index stok
            $table->string('variant_image')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
