<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('locatable'); /* Storage config for s3: local, etc */
            $table->uuid('owner_id'); /* Owner model type name, App\User, App\Group etc */
            $table->string('storable_type'); /* Model type name to store: App\Aip, App\Dip, etc */
            $table->string('human_readable_name'); /*Presented in the user interface to ease configuration */
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
        Schema::dropIfExists('storage_locations');
    }
}
