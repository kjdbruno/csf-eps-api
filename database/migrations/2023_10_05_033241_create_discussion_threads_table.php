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
        Schema::create('discussion_threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discussionID')->index();
            $table->unsignedBigInteger('userID')->index();
            $table->longText('content');
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('discussionID')->references('id')->on('discussions');
            $table->foreign('userID')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussion_threads');
    }
};
