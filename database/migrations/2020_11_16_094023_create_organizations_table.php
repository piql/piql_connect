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
     * Create organizaton table, a default organization and update relations in users and accounts.
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
            $table->uuid('organization_uuid');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('organization_uuid');
        });

        if (User::count() || Account::count())
        {
            $org = Organization::create([
                "name" => "Default Organization",
                "uuid" => Uuid::generate()->string
            ]);

            User::query()->update(['organization_uuid' => $org->uuid]);
            Account::query()->update(['organization_uuid' => $org->uuid]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_uuid');
        });

    }

    /**
     * Reverse the migrations. WARNING: This will be a destructive migration if multiple organizations/account pairs have been added.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('account_uuid');
        });

        if (User::count() || Account::count()) {
            // Squash all users into the first account
            $account = Account::first();
            Account::where('uuid', '!=', $account->uuid)->orWhereNull('uuid')->delete();
            User::query()->update(['account_uuid' => $account->uuid]);
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('organization_uuid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('organization_uuid');
        });

        Schema::dropIfExists('organizations');
    }
}
