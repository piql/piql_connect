<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePermissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('permissions', 'access_controls');
        Schema::rename('user_permissions', 'user_access_controls');
        Schema::table('user_access_controls', function (Blueprint $table) {
            $table->renameColumn('permission_id', 'access_control_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('access_controls', 'permissions');
        Schema::rename('user_access_controls', 'user_permissions');
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->renameColumn('access_control_id', 'permission_id');
        });
    }
}
