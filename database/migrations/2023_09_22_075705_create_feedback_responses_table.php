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
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedbackID')->index();
            $table->unsignedBigInteger('userID')->index();
            $table->longText('content')->nullable();
            $table->longText('file')->nullable();
            $table->tinyInteger('isActive')->default(true);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('feedbackID')->references('id')->on('feedback');
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
        Schema::dropIfExists('feedback_responses');
    }
};
