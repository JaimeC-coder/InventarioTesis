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
        Schema::create('type_customers', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('type')->unique();
            $blueprint->decimal('porcentage_discount', 5, 2)->default(0.00);
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
        Schema::dropIfExists('type_customers');
    }
};
