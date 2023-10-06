<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID')->index();
            $table->unsignedBigInteger('discussionID')->index();
            $table->unsignedBigInteger('answerID')->index();
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users');
            $table->foreign('discussionID')->references('id')->on('discussions');
            $table->foreign('answerID')->references('id')->on('discussion_poll_answers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussion_answers');
    }
};
