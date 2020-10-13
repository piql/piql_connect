<?php

use App\Account;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid("account");
        });

        $account = Account::whereTitle("Default Account")->first();
        if(!$account) {
            $account = Account::create([
                "title" => "Default Account",
                "description" => "Default Account",
            ]);
        }
        User::query()->update(["account" => $account->uuid]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn("account");
        });
    }
}
