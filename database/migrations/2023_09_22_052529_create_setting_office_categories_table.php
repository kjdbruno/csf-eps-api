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
        Schema::create('setting_office_categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('categoryID')->index();
            $table->unsignedBigInteger('officeID')->index();
            $table->tinyInteger('isActive')->default(true);
            $table->timestamps();

            $table->foreign('categoryID')->references('id')->on('preference_categories');
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
        Schema::dropIfExists('setting_office_categories');
    }
};
