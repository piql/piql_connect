<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SplitOrganizationFromAccount extends Migration
{
    /**
     * Run the migrations.
     * NOTE: If the database contains subholdings, the holding tree structure will be lost.
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

        Schema::table('holdings', function (Blueprint $table) {
            $table->renameColumn('owner_archive_uuid', 'collection_uuid');
            $table->dropColumn('parent_id');
            $table->dropColumn('position');
        });

        Schema::table('storage_properties', function (Blueprint $table) {
            $table->renameColumn('archive_uuid', 'collection_uuid');
        });

        Schema::rename('archives', 'collections');
        Schema::rename('accounts', 'archives');
        Schema::table('collections', function (Blueprint $table) {
            $table->renameColumn('account_uuid', 'archive_uuid');
        });

        DB::table('metadata')
            ->where("owner_type", "App/Account")
            ->update(["owner_type" => "App/Archive"]);

        $creationTime = new DateTime;
        $organizationUuid = \Webpatser\Uuid\Uuid::generate()->string;
        DB::table('organizations')->insert([
            "name" => "Default organization",
            "uuid" => $organizationUuid,
            "updated_at" => $creationTime->format('y-m-d H:i:s'),
            "created_at" => $creationTime->format('y-m-d H:i:s')
        ]);

        DB::table('archives')
            ->update(["organization_uuid" => $organizationUuid]);
        DB::table('users')
            ->update(["organization_uuid" => $organizationUuid]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('metadata')
            ->where("owner_type", "App/Archive")
            ->update(["owner_type" => "App/Account"]);

        Schema::table('collections', function (Blueprint $table) {
            $table->renameColumn('archive_uuid', 'account_uuid');
        });

        Schema::rename('archives', 'accounts');

        Schema::rename('collections', 'archives');

        Schema::table('storage_properties', function (Blueprint $table) {
            $table->renameColumn('collection_uuid', 'archive_uuid');
        });

        Schema::table('holdings', function (Blueprint $table) {
            $table->renameColumn('collection_uuid', 'owner_archive_uuid');
            $table->bigInteger('parent_id')->nullable()->default(null);
            $table->integer('position')->default(0);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('organization_uuid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('organization_uuid', 'account_uuid');
        });

        if(DB::table('accounts')->count() > 0)
        {
            $accountUuid = DB::table('accounts')->first()->uuid;
            DB::table('users')->update(["account_uuid" => $accountUuid]);
        }

        Schema::dropIfExists('organizations');

    }
}
