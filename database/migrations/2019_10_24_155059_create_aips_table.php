<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('external_uuid');
            $table->uuid('owner');
            $table->bigInteger('online_storage_location_id')->nullable(); //location of online aip file (tarball etc)
            $table->bigInteger('offline_storage_location_id')->nullable(); //same for offline files (amu config etc.)
            $table->string('online_storage_path')->nullable(); //file path relative to storage location entry point for online AIP file ('/1234/5678/some_aip.tar')
            $table->string('offline_storage_path')->nullable(); //same for offline files (path inside afs etc.)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aips');
    }
}
