<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_objects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('fullpath');
            $table->text('filename');
            $table->text('path');
            $table->integer('size');
            $table->string('object_type');
            $table->string('info_source');
            $table->string('mime_type');
            $table->uuidMorphs('storable');
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
        Schema::dropIfExists('file_objects');
    }
}
