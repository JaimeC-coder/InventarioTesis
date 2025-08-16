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
        Schema::create('movements', function (Blueprint $table) {
           $table->id();
           //type,serie,correlativo,date,total,observaciones, reason_id
           $table->string('type');
           $table->string('serie');
           $table->integer('correlativo');
           $table->timestamp('date');
           $table->decimal('total', 10, 2);
           $table->string('observaciones')->nullable();
           $table->foreignId('reason_id')->constrained('reasons')->onDelete('cascade');
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
        Schema::dropIfExists('movements');
    }
};
