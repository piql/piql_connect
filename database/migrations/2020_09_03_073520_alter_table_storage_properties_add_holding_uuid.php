<?php

use App\Holding;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStoragePropertiesAddHoldingUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storage_properties', function (Blueprint $table) {
            $table->uuid("holding_uuid")->nullable();
        });

        Holding::distinct('title')->get()->map(function($holding) {
            \App\StorageProperties::where('holding_name', $holding->title)
                ->update(['holding_uuid' => $holding->uuid]);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_properties', function (Blueprint $table) {
            $table->dropColumn("holding_uuid");
        });
    }
}
