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
        Schema::create('post_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('posts_id');    
            $table->string('ip');
            $table->json('userAgent');
            $table->timestamps();
        
            $table->foreign('posts_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post__activities');
    }
};