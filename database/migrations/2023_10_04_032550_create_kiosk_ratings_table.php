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
        Schema::create('kiosk_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('officeID')->index();
            $table->integer('phyRating')->index();
            $table->integer('serRating')->index();
            $table->integer('perRating')->index();
            $table->integer('ovrRating')->index();
            $table->longText('content')->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->string('email')->nullable();
            $table->date('date')->nullable();
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('kiosk_ratings');
    }
};
