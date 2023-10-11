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
            $table->unsignedBigInteger('kioskID')->index();
            $table->integer('phyRating')->index();
            $table->integer('serRating')->index();
            $table->integer('perRating')->index();
            $table->integer('ovrRating')->index();
            $table->longText('content')->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('kioskID')->references('id')->on('preference_kiosks');
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
