<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fonds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('owner_holding_uuid'); /* In a system with multiple separate organizations/holdings, this determines the owner */
            $table->bigInteger('parent_id')->nullable(); /*Poor mans tree structure - if effiency becomes a problem, use something proper */
            $table->bigInteger('lhs')->nullable(); /*Node to the left */
            $table->bigInteger('rhs')->nullable(); /*Node to the right */
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
        Schema::dropIfExists('fonds');
    }
}
