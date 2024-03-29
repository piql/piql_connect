<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bag_id');
            $table->string('filename');
            $table->string('filesize');
            $table->string('uuid');
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
        Schema::dropIfExists('files');
    }
}
