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
        Schema::create('quotes', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('voucher_type');
            $blueprint->string('serie');
            $blueprint->string('correlativo');
            $blueprint->date('date');
            $blueprint->string('status')->nullable();
            $blueprint->decimal('subtotal', 15, 2)->nullable();
            $blueprint->decimal('igv', 15, 2)->nullable();
            $blueprint->decimal('total', 15, 2)->nullable();
            $blueprint->string('total_string')->nullable();
            $blueprint->string('currency')->default('SOLES');
            $blueprint->string('file_path')->nullable();
            $blueprint->string('observation')->nullable();
            $blueprint->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $blueprint->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
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
        Schema::dropIfExists('quotes');
    }
};
