<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngestProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingest_processes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('incoming_file_id');
            $table->string('bag_id');
            $table->String('sip_id');
            $table->string('internal_status');
            $table->string('last_known_external_status');
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
        Schema::dropIfExists('ingest_processes');
    }
}
