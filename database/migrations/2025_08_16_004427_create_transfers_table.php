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
            $blueprint->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $blueprint->integer('type');
            $blueprint->string('serie');
            $blueprint->integer('correlativo');
            $blueprint->date('date');
            $blueprint->string('status')->nullable();
            $blueprint->decimal('subtotal', 15, 2)->nullable();
            $blueprint->decimal('igv', 15, 2)->nullable();
            $blueprint->decimal('total', 15, 2)->nullable();
            $blueprint->string('total_string')->nullable();
            $blueprint->string('currency')->default('SOLES');
            $blueprint->string('file_path')->nullable();
            $blueprint->string('observation')->nullable();
            $blueprint->foreignId('origin_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->foreignId('destination_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $blueprint->uuid('uuid')->unique();
            $blueprint->timestamps();
            $blueprint->softDeletes();
            $blueprint->unique(['serie', 'correlativo', 'origin_warehouse_id','deleted_at'], 'unique_transfer');
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
