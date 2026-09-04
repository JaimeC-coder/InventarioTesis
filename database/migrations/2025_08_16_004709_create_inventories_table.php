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
        //va a ser el historial de los productos,osea este va a ser el registro de los productos en total
        Schema::create('inventories', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('detail')->nullable();
            $blueprint->integer('quantity_in')->default(0);
            $blueprint->integer('quantity_out')->default(0);
            $blueprint->integer('quantity_total')->default(0);
            $blueprint->string('product_name')->nullable();
            $blueprint->string('type')->nullable();
            $blueprint->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->morphs('inventoryable');
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
        Schema::dropIfExists('inventories');
    }
};
