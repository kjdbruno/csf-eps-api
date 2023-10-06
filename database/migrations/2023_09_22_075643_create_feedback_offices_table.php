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
        Schema::create('feedback_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedbackID')->index();
            $table->unsignedBigInteger('officeID')->index();
            $table->tinyInteger('isReceived')->default(false);
            $table->tinyInteger('isDelayed')->default(false);
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('feedbackID')->references('id')->on('feedback');
            $table->foreign('officeID')->references('id')->on('preference_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback_offices');
    }
};
