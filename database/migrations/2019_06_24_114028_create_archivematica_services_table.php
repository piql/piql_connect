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
        Schema::create('archivematica_services', function (Blueprint $table) {
            $table->string('url');
            $table->string('api_token');
            $table->timestamps();
        });
        $this->createBinary16Column('archivematica_services', 'id');
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
