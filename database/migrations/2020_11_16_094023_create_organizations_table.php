<?php

use App\Organization;
use App\Account;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Webpatser\Uuid\Uuid;

class CreateOrganizationsTable extends Migration
{
    /**
     * Create organizaton table and rename foreign keys in related models.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid("uuid")->unique();
            $table->string("name");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('account_uuid', 'organization_uuid');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('organization_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('organization_uuid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('organization_uuid', 'account_uuid');
        });

        Schema::dropIfExists('organizations');
    }
}
