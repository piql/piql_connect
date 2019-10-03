<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\BinaryColumn;

class CreateArchivematicaServicesTable extends Migration
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

        Schema::create('archivematica_services', function (Blueprint $table) use ($driver){
            if("sqlite" == $driver) {
                $table->binary('id');
            }
            $table->string('url');
            $table->string('api_token');
            $table->timestamps();
        });

        if("sqlite" != $driver) {
            $this->createBinary16Column('archivematica_services', 'id');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivematica_services');
    }
}
