<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 200)->unique();
            $table->string('slug', 200)->unique();
            $table->longText('content');
            $table->uuid('user_id');        
            $table->uuid('category_id');   
            $table->string('thumbnail', 255)->nullable();
            $table->date('published_at')->nullable();
            $table->enum('status', ['publish', 'draft']);
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_description', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};