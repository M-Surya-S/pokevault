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
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pokemon_id')->unique();
            $table->string('name', 100);
            $table->string('image_url', 255);
            $table->json('types');
            $table->string('nickname', 30)->nullable();
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('notes', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
