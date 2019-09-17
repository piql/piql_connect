<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoragePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('bag_uuid');
            $table->uuid('archive_uuid')->nullable();
            $table->string('holding_name')->nullable();
            $table->uuid('sip_uuid')->nullable();
            $table->uuid('dip_uuid')->nullable();
            $table->uuid('aip_uuid')->nullable();
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
        Schema::dropIfExists('storage_properties');
    }
}
