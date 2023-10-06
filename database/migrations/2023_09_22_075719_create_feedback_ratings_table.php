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
        Schema::create('feedback_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('responseID')->index();
            $table->integer('rating')->default(0);
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('responseID')->references('id')->on('feedback_responses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback_ratings');
    }
};
