<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameArchivesToCollections extends Migration
{
    /**
     * Run the migrations.
     * NOTE: If the database contains subholdings, the holding tree structure will be lost.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holdings', function (Blueprint $table) {
            $table->renameColumn('owner_archive_uuid', 'collection_uuid');
            $table->dropColumn('parent_id');
            $table->dropColumn('position');
        });
        Schema::table('storage_properties', function (Blueprint $table) {
            $table->renameColumn('archive_uuid', 'collection_uuid');
        });
        Schema::rename('archives', 'collections');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('collections', 'archives');
        Schema::table('storage_properties', function (Blueprint $table) {
            $table->renameColumn('collection_uuid', 'archive_uuid');
        });
        Schema::table('holdings', function (Blueprint $table) {
            $table->renameColumn('collection_uuid', 'owner_archive_uuid');
            $table->bigInteger('parent_id')->nullable()->default(null);
            $table->integer('position')->default(0);
        });
    }
}
