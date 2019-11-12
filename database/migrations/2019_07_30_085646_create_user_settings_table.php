<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*
 * The user_settings table contain json array fields for interface, workflow
 * and data configuration settings.
 * This allows for completely dynamic options, casted by the model
 * to respective php arrays for easy access.
 *
 * Often used fields can be cached by the model to avoid deserializing on read.
 *
 */

class CreateUserSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user');
            $table->json('interface');
            $table->json('workflow');
            $table->json('storage');
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
