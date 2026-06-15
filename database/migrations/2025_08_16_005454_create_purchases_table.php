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
        Schema::create('purchases', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('voucher_type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->timestamp('date');
            $blueprint->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('cascade');
            $blueprint->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->enum('status', ['REGISTRADO','PEDIDO', 'LLEGADO'])->default('REGISTRADO');
            $blueprint->decimal('subtotal', 10, 2)->nullable();
            $blueprint->decimal('igv', 10, 2)->nullable();
            $blueprint->decimal('total', 10, 2)->nullable();
            $blueprint->string('total_string')->nullable();
            $blueprint->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $blueprint->string('currency')->default('SOLES');
            $blueprint->string('payment_method')->default('EFECTIVO');
            $blueprint->string('payment_type')->default('CONTADO');
            $blueprint->string('file_path')->nullable();
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
