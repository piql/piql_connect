<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFondsInHoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fonds_in_holdings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('fonds_id');
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('holding_id');
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
        Schema::dropIfExists('fonds_in_holdings');
    }
}
