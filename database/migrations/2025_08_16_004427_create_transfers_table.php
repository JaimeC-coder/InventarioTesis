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
        Schema::create('transfers', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->integer('type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->timestamp('date');
            $blueprint->decimal('total', 10, 2)->default('0');
            $blueprint->string('observaciones')->nullable();
            $blueprint->foreignId('origin_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->foreignId('destination_warehouse_id')->constrained('warehouses')->onDelete('cascade');
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
        Schema::dropIfExists('transfers');
    }
};
