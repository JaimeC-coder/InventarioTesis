<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productables', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $blueprint->integer('quantity');
            $blueprint->decimal('price', 10, 2);
            $blueprint->decimal('subtotal', 10, 2);
            $blueprint->morphs('productable');
            $blueprint->uuid('uuid')->unique();
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productables');
    }
};
