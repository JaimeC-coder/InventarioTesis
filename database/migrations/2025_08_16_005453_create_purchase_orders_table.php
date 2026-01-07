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
        Schema::create('purchase_orders', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->integer('voucher_type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->date('date');
            $blueprint->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $blueprint->string('status')->nullable();
            $blueprint->decimal('subtotal', 10, 2)->nullable();
            $blueprint->decimal('igv', 10, 2)->nullable();
            $blueprint->decimal('total', 10, 2)->nullable();
            $blueprint->string('total_string')->nullable();
            $blueprint->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $blueprint->string('currency')->default('SOLES');
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
        Schema::dropIfExists('purchase_orders');
    }
};
