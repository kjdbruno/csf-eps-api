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
        Schema::create('feedback_evidence', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedbackID')->index();
            $table->longText('evidence');
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('feedbackID')->references('id')->on('feedback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback_evidence');
    }
};
