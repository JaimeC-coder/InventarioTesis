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
        Schema::create('customers', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('identity');
            $blueprint->string('document_number')->unique();
            $blueprint->string('name');
            $blueprint->string('address')->nullable();
            $blueprint->string('email')->nullable();
            $blueprint->string('phone')->nullable();
            $blueprint->enum('type', ['A1', 'GENERAL'])->default('GENERAL');
            $blueprint->uuid('uuid')->unique();
            $blueprint->timestamps();
            $blueprint->softDeletes();
            $blueprint->unique(['document_number', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
