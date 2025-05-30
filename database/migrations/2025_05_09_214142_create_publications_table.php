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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();

            $table->text('title')->nullable();
            $table->date('published_at')->nullable();
            $table->unsignedBigInteger('publisher_id')->index()->nullable();
            $table->unsignedBigInteger('citation_count')->nullable();
            $table->string('doi')->index()->nullable();
            $table->unsignedBigInteger('openalex_id')->index()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
