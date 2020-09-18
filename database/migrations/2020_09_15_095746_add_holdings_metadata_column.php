<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHoldingsMetadataColumn extends Migration
{
    /**
     * Add the defaultMetadataTemplate to the holdings table.
     * A copy of this field is made for each transfer in this holding.
     *
     * Its model is responsible for supplying a good default value.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('holdings', function (Blueprint $table) {
            $table->json('defaultMetadataTemplate');
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
            $table->dropColumn('defaultMetadataTemplate');
        });
    }
}
