<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        Schema::table('users', function (Blueprint $table) use ($driver) {
            if("sqlite" == $driver) {
                $table->string('api_token', 60)->nullable();
            } else {
                $table->string('api_token', 60);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
