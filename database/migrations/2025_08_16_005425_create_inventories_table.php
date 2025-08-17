<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('detail')->nullable();
            $blueprint->integer('quantity_in');
            $blueprint->integer('quantity_out');
            $blueprint->decimal('cost_in', 10, 2)->default(0);
            $blueprint->decimal('cost_out', 10, 2)->default(0);
            $blueprint->decimal('total_in', 10, 2)->default(0);
            $blueprint->decimal('total_out', 10, 2)->default(0);
            $blueprint->integer('quantity_balance')->default(0);
            $blueprint->decimal('cost_balance', 10, 2)->default(0);
            $blueprint->decimal('total_balance', 10, 2)->default(0);
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
