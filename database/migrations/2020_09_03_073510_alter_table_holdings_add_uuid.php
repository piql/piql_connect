<?php

use App\Holding;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Webpatser\Uuid\Uuid;

class AlterTableHoldingsAddUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holdings', function (Blueprint $table) {
            $table->uuid("uuid");
        });

        Holding::all()->map(function($holding) {
            return $holding->update(["uuid" => Uuid::generate()->string]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holdings', function (Blueprint $table) {
            //
            $table->dropColumn("uuid");
        });
    }
}
