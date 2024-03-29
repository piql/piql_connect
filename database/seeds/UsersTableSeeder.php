<?php

use App\Organization;
use App\Traits\SeederOperations;
use App\User;
use Illuminate\Database\Seeder;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Webpatser\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    use Uuids;
    use SeederOperations;

    public function run()
    {
        $org = Organization::first();

        $seedSuccess = $this->seedFromFile(function($param) use($org) {
            // assume plaintext passwords
            $param["password"] = Hash::make($param["password"] ?? Uuid::generate());
            $param["organization_uuid"] = $org->uuid;
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
