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
        Schema::create('purchases', function (Blueprint $blueprint): void {
           $blueprint->id();

           $blueprint->string('voucher_type');
           $blueprint->string('serie');
           $blueprint->integer('correlativo');
           $blueprint->timestamp('date');
           $blueprint->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
           $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
           $blueprint->decimal('total', 10, 2);
           $blueprint->string('observation')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
