<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('identity_id')->constrained('identities')->onDelete('cascade');
            $blueprint->string('document_number')->unique();
            $blueprint->string('name');
            $blueprint->string('address')->nullable();
            $blueprint->string('email')->nullable();
            $blueprint->string('phone')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
