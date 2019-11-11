<?php

use Illuminate\Database\Seeder;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Webpatser\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    use Uuids;

    public function run()
    {
        $found = App\User::where('username', '=', 'simen')->first();
        $user = $found ?? new App\User();
        $user->username = "simen";
        $user->password = Hash::make("simen");
        $user->full_name = "Simen Fjeld-Olsen";
        $user->email = "simen.fjeldolsen@piql.com";
        $user->save();

        $found = App\User::where('username', '=', 'kare')->first();
        $found ?? App\User::where('email', '=', 'kare.andersen@piql.com')->first();
        $user = $found ?? new App\User();
        $user->username = "kare";
        $user->password = Hash::make("kare");
        $user->full_name = "Kåre Andersen";
        $user->email = "kare.andersen@piql.com";
        $user->save();

        $found = App\User::where('username', '=', 'swhl')->first();
        $user = $found ?? new App\User();
        $user->username = "swhl";
        $user->password = Hash::make("swhl");
        $user->full_name = "Håkon Larsson";
        $user->email = "hakon.larsson@piql.com";
        $user->save();

        $found = App\User::where('username', '=', 'simon')->first();
        $user = $found ?? new App\User();
        $user->username = "simon";
        $user->password = Hash::make("simon");
        $user->full_name = "Simon Bruce-Cassidy";
        $user->email = "simon.brucecassidy@piql.com";
        $user->save();


        // Create a Personal Access Client if no one exists
        $clientRepository = new ClientRepository();
        try {
            $clientRepository->personalAccessClient();
        } catch (RuntimeException $exception) {
            $clientRepository->createPersonalAccessClient(null, "piqlConnect Personal Access Client", "http://localhost");
        }

        // Add default Archivematica Client for accessing Piql Connect trigger API
        $found = App\User::where('username', '=', 'archivematica')->first();
        $user = $found ?? new App\User();
        $user->username = "archivematica";
        $user->password = Hash::make(Uuid::generate());
        $user->full_name = "Archivematica callback client";
        $user->email = "";
        $user->save();
        $token  = $user->createToken("archivematica_callback_token")->accessToken;
        dump("Remember to set/update Archivematica storage server callbacks with these headers:");
        dump("Accept: application/json");
        dump("Authorization: Bearer ".$token);
    }
}
