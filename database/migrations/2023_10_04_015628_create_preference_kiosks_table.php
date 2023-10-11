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
        Schema::create('preference_kiosks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('officeID')->index();
            $table->unsignedBigInteger('positionID')->index();
            $table->longText('description');
            $table->longText('photo');
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('officeID')->references('id')->on('preference_offices');
            $table->foreign('positionID')->references('id')->on('preference_positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preference_kiosks');
    }
};
