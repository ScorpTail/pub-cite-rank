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
        Schema::create('author_ranks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id')->index();
            $table->unsignedBigInteger('total_publications');
            $table->unsignedBigInteger('total_citations');
            $table->unsignedBigInteger('h_index');
            $table->decimal('rank_score', 8, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_ranks');
    }
};
