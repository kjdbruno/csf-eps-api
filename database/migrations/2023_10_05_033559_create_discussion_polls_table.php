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
        Schema::create('discussion_polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discussionID')->index();
            $table->string('label');
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('discussionID')->references('id')->on('discussions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussion_polls');
    }
};
