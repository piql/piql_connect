<?php

use Illuminate\Database\Seeder;
use App\Account;
use \App\Traits\SeederOperations;

class AccountSeeder extends Seeder
{
    use SeederOperations;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::truncate();

        if($this->seedFromFile(function($param) {
            $account = Account::create($param);
            $metadata = \App\AccountMetadata::create([
                "modified_by" => "",
                "metadata" => ["dc" => [
                    "title" => $account->title,
                    "description" => $account->description,
                ]]
            ]);
            $metadata->parent()->associate($account);
            $metadata->save();
            })) {
            return;
        }

    }
}
