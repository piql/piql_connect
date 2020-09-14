<?php

use App\Account;
use App\Archive;
use Illuminate\Database\Migrations\Migration;
use Webpatser\Uuid\Uuid;

class UpdateArchiveWithDefaultAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = Archive::where("account_uuid" , null);
        if($query->count())
        {
            $account = Account::get()->first();
            if(!$account) {
                $account = Account::create([
                    "title" => "Default Account",
                    "description" => "Dummy account made for testing purpose only",
                    "uuid" => Uuid::generate()->string
                ]);
                $metadata = \App\AccountMetadata::create([
                    "modified_by" => "",
                    "metadata" => ["dc" => [
                        "title" => $account->title,
                        "description" => $account->description,
                    ]]
                ]);
                $metadata->parent()->associate($account);
                $metadata->save();
            }
            $query->update(["account_uuid" => $account->uuid]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
