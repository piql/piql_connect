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

        $this->seedFromFile(function($param) {
            $account = Account::create($param);
        } );
    }
}
