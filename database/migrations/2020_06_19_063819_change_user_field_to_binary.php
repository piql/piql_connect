<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeUserFieldToBinary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->renameColumn('user_id', 'user_id_tmp');
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->binary('user_id');
        });
        DB::table('user_permissions')->update([
            'user_id' => DB::raw("UNHEX(REPLACE(user_id_tmp, '-',''))")
        ]);
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropColumn('user_id_tmp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->renameColumn('user_id', 'user_id_tmp');
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->string('user_id');
        });
        DB::table('user_permissions')->update([
            'user_id' => DB::raw(
                "LOWER(CONCAT(SUBSTR(HEX(user_id_tmp), 1, 8), '-', SUBSTR(HEX(user_id_tmp), 9, 4), '-', SUBSTR(HEX(user_id_tmp), 13, 4), '-',SUBSTR(HEX(user_id_tmp), 17, 4), '-', SUBSTR(HEX(user_id_tmp), 21)"
            )
        ]);
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropColumn('user_id_tmp');
        });
    }
}
