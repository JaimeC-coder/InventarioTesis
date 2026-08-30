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
        Schema::create('employees', function (Blueprint $blueprint): void {
            //'document','phone','address','fechaNacimiento','user_id',
            $blueprint->id();
            $blueprint->string('document')->unique();
            $blueprint->string('phone');
            $blueprint->string('address');
            $blueprint->date('fechaNacimiento');
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('employees');
    }
};
