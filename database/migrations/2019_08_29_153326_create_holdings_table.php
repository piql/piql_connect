<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holdings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('owner_archive_uuid'); /* In a system with multiple separate organizations/holdings, this determines the owner */
            $table->bigInteger('parent_id')->nullable(); /*Poor mans tree structure - if effiency becomes a problem, use something proper */
            $table->integer('position'); /* Position in tree, 0 leftmost, increasing to the right */
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
        Schema::dropIfExists('holdings');
    }
}
