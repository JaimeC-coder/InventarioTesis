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
        Schema::create('quotes', function (Blueprint $table) {
           $table->id();
           //voucher_type,serie,correlativo,date,total,observacion ,customer_id
           $table->string('voucher_type');
           $table->string('serie');
           $table->string('correlativo');
           $table->date('date');
           $table->decimal('total', 10, 2);
           $table->string('observation')->nullable();
           $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
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
        Schema::dropIfExists('quotes');
    }
};
