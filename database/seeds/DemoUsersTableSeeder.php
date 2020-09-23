<?php

use App\Account;
use App\Traits\SeederOperations;
use App\User;
use Illuminate\Database\Seeder;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Webpatser\Uuid\Uuid;

class DemoUsersTableSeeder extends Seeder
{
    use Uuids;
    use SeederOperations;

    public function run()
    {
        $account = Account::first();
        $seedSuccess = $this->seedFromFile(function($param) use ($account) {
            // assume plaintext passwords
            $param["password"] = Hash::make($param["password"] ?? Uuid::generate());
            $param["account_uuid"] = $account->uuid;
            // add user if it doesn't exists
            User::where('username', $param["username"])->first() ?? User::create($param);
        });

        if(!$seedSuccess)
        {
            exit("Seeding failed\n");
        }

        // Create a Personal Access Client if no one exists
        $clientRepository = new ClientRepository();
        try {
            $clientRepository->personalAccessClient();
        } catch (RuntimeException $exception) {
            $clientRepository->createPersonalAccessClient(null, "piqlConnect Personal Access Client", "http://localhost");
        }

    }
}
