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
        Schema::create('story_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('media_id');
            $table->text('file_path');
            $table->text('url');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_images');
    }
};
