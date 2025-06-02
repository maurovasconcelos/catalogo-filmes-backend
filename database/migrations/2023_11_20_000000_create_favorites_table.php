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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->unique();
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->text('overview')->nullable();
            $table->date('release_date')->nullable();
            $table->float('vote_average')->nullable();
            $table->text('genre_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};