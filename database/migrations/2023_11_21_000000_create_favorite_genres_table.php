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
        Schema::create('favorite_genres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('favorite_id');
            $table->unsignedInteger('genre_id');
            $table->timestamps();
            
            $table->foreign('favorite_id')
                  ->references('id')
                  ->on('favorites')
                  ->onDelete('cascade');
                  
            $table->unique(['favorite_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_genres');
    }
};