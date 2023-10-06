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
        Schema::create('user_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID')->index();
            $table->unsignedBigInteger('officeID')->index();
            $table->unsignedBigInteger('positionID')->index();
            $table->unsignedBigInteger('yearID')->index();
            $table->string('employeeID');
            $table->tinyInteger('isVerified')->default(false);
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users');
            $table->foreign('officeID')->references('id')->on('preference_offices');
            $table->foreign('positionID')->references('id')->on('preference_positions');
            $table->foreign('yearID')->references('id')->on('preference_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_admins');
    }
};
