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
        Schema::create('sales', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('voucher_type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->timestamp('date');
            $blueprint->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('cascade');
            $blueprint->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            // agregar un estado de la venta (pendiente, pagado, cancelado,Por entregar)
            // subtotal ,igv,total_string
            $blueprint->string('status');
            $blueprint->decimal('subtotal', 10, 2);
            $blueprint->decimal('igv', 10, 2);
            $blueprint->decimal('total', 10, 2)->default(0.00);
            $blueprint->string('total_string');
            $blueprint->string('currency')->default('SOLES');
            $blueprint->string('file_path')->nullable();
            $blueprint->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('sales');
    }
};
