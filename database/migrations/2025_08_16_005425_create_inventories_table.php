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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('quantity_in');
            $table->integer('quantity_out');
            $table->decimal('cost_in', 10, 2)->default(0);
            $table->decimal('cost_out', 10, 2)->default(0);
            $table->decimal('total_in', 10, 2)->default(0);
            $table->decimal('total_out', 10, 2)->default(0);
            $table->integer('quantity_balance')->default(0);
            $table->decimal('cost_balance', 10, 2)->default(0);
            $table->decimal('total_balance', 10, 2)->default(0);
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->morphs('inventoryable');
            $table->uuid('uuid')->unique();
            $table->timestamps();
            $table->softDeletes();
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
