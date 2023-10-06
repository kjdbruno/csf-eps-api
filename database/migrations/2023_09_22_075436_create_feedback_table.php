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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID')->index();
            $table->unsignedBigInteger('categoryID')->index();
            $table->longText('content')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamp('expire_on', 0);
            $table->tinyInteger('isActive')->default(true);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users');
            $table->foreign('categoryID')->references('id')->on('preference_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
};
