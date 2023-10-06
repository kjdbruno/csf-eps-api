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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoryID')->index();
            $table->longText('title');
            $table->longText('content');
            $table->longText('file')->nullable();
            $table->tinyInteger('isActive')->default(true);
            $table->timestamp('expire_on', 0);
            $table->timestamps();

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
        Schema::dropIfExists('discussions');
    }
};
