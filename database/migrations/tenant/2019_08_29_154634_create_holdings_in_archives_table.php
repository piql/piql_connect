<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldingsInArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holdings_in_archives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('holding_id');
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('archive_id');
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
        Schema::dropIfExists('holdings_in_archives');
    }
}
