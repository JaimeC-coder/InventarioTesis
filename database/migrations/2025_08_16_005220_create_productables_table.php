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
        //este sigue siendo el detalle general
        Schema::create('productables', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $blueprint->string('product_name', 255);
            $blueprint->string('price_type', 20)->default('NONE');
            $blueprint->decimal('price', 15, 2);
            $blueprint->integer('quantity');
            $blueprint->decimal('subtotal', 15, 2);
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
