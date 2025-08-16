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
        Schema::create('products', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('sku')->unique();
            $blueprint->string('barcode')->unique();
            $blueprint->string('description')->nullable();
            $blueprint->decimal('price', 10, 2);
            $blueprint->uuid('uuid')->unique();
            $blueprint->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
