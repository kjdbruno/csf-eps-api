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
        Schema::create('user_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID')->index();
            $table->unsignedBigInteger('sexID')->index()->nullable();
            $table->string('number')->nullable();
            $table->integer('verification')->nullable();
            $table->tinyInteger('isVerified')->default(false);
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users');
            $table->foreign('sexID')->references('id')->on('preference_sexes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_clients');
    }
};
