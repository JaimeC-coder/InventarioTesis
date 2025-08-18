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
        Schema::create('movements', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->timestamp('date');
            $blueprint->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->decimal('total', 10, 2)->default(0.00);
            $blueprint->string('observaciones')->nullable();
            $blueprint->foreignId('reason_id')->constrained('reasons')->onDelete('cascade');
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
        Schema::dropIfExists('movements');
    }
};
