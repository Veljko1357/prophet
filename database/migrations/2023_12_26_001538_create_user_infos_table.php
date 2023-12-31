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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('instagram_session_id');
            $table->string('username');
            $table->biginteger('instagram_id');
            $table->text('profile_picture_url');
            $table->text('bio');
            $table->biginteger('media_count');
            $table->biginteger('followers_count');
            $table->biginteger('following_count');
            $table->timestamps();

            $table->foreign('instagram_session_id')->references('id')->on('instagram_sessions');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_user_infos');
    }
};
