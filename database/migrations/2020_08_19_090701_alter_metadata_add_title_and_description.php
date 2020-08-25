<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMetadataAddTitleAndDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metadata', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string("type")->nullable();
            $table->index(["type", "uuid"], "type_uuid_index");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metadata', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('type');
            $table->dropIndex("type_uuid_index");
        });
    }
}
