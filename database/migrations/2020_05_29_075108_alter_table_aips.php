<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aips', function (Blueprint $table) {
            $table->bigInteger('size')->default(0);
        });

        \App\FileObject::select(\DB::raw('storable_id as id, sum(size) as size'))
            ->where("storable_type", 'App\\Aip')
            ->groupBy("storable_id")
            ->chunk(100, function($objs) {
                foreach($objs as $obj) {
                    return \App\Aip::find($obj->id)->update(["size" => $obj->size]);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aips', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
}
