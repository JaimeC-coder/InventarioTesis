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
        Schema::create('quotes', function (Blueprint $blueprint): void {
           $blueprint->id();
           //voucher_type,serie,correlativo,date,total,observacion ,customer_id
           $blueprint->string('voucher_type');
           $blueprint->string('serie');
           $blueprint->string('correlativo');
           $blueprint->date('date');
           $blueprint->decimal('total', 10, 2);
           $blueprint->string('observation')->nullable();
           $blueprint->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
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
