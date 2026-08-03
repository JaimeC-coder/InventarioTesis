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
        //va a ser el historial de los registros de productos osea el historial de los registros de productos, para saber en que almacén se encuentra y su cantidad
        Schema::create('records', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('observation')->nullable();
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->string('warehouse_name', 255);
            $blueprint->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $blueprint->string('product_name', 255);
            $blueprint->string('product_code', 255);
            $blueprint->decimal('quantity', 10, 2);
            $blueprint->morphs('recordable');
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
        Schema::dropIfExists('records');
    }
};
