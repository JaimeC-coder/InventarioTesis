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
        Schema::create('units', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('name')->unique();
            $blueprint->string('abbreviation')->nullable();
            $blueprint->uuid('uuid')->unique();
            $blueprint->string('code')->nullable();
            $blueprint->timestamps();
            $blueprint->softDeletes();
            $blueprint->unique(['name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
