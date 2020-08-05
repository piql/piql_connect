<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoragePropertiesTableAddName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storage_properties', function (Blueprint $table) {
            //
            $table->string('name')->nullable();
        });

        \App\StorageProperties::query()
            ->whereHas("bag")
            ->get()
            ->map(function($obj) {
                $obj->update(["name" => $obj->bag->name]);
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
            //
            $table->dropColumn(['name']);
        });
    }
}
