<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('external_uuid');
            $table->uuid('owner');
            $table->uuid('aip_external_uuid')->nullable();
            $table->bigInteger('storage_location_id')->nullable();
            $table->string('storage_path')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('dips');
    }
}
