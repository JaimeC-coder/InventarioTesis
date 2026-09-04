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
        Schema::create('chatbot_query_logs', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained();
            $blueprint->string('entity');
            $blueprint->string('metric');
            $blueprint->json('filters')->nullable();
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
        Schema::dropIfExists('chatbot_query_logs');
    }
};
