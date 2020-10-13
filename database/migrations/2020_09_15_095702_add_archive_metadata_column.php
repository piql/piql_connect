<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchiveMetadataColumn extends Migration
{
    /**
     * Add the defaultMetadataTemplate to the archives table.
     * A copy of this field is made for each transfer in this archive.
     *
     * Its model is responsible for supplying a good default value.
     **/
 
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
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
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn('defaultMetadataTemplate');
        });
    }
}
