<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventLogEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_log_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type'); /* Event type */
            $table->string('severity'); /* Event severity */
            $table->longText('message'); /* Event message */
            $table->longText('exception')->nullable(); /* Exception / detailed description */
            $table->nullableMorphs('context'); /*  */
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
        Schema::dropIfExists('event_log_entries');
    }
}
