<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/* These are common user settings that should be set for all users
 * and be cheap to fetch from the database. Another list of
 * settings as key-value pairs will be added, and is useful for per client
 * type of settings (metadata etc).
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
            $table->string('interfaceLanguage');
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
