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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip', 45)->nullable();
            $table->string('type', 20)->nullable(); // 'request', 'response', 'error'
            $table->integer('status_code')->nullable();
            $table->string('response_type', 50)->nullable(); // 'mock', 'real', 'cached'
            $table->json('request_body')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamps();
            
            $table->index('path');
            $table->index('method');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
