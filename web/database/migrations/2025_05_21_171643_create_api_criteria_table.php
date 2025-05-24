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
        Schema::create('api_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['real', 'mock'])->default('mock');
            $table->string('path');
            $table->integer('status_code')->default(200);
            $table->string('content_type')->default('application/json');
            $table->text('headers')->nullable();
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])->default('GET');
            $table->text('body')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('create_by')->nullable();
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();
            $table->string('real_api_url')->nullable();
            
            // Indexes for faster lookups
            $table->index('path');
            $table->index('method');
            $table->index('type');
            
            // Path and method combination must be unique
            $table->unique(['path', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_criteria');
    }
};
