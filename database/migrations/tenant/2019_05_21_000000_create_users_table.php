<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\BinaryColumn;

class CreateUsersTable extends Migration
{
    use BinaryColumn;

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        Schema::create('users', function (Blueprint $table) use ($driver) {
            if("sqlite" == $driver) {
                $table->binary('id');
            }
            $table->string('username');
            $table->string('password');
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        if("sqlite" != $driver) {
            $this->createBinary16Column('users', 'id');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
