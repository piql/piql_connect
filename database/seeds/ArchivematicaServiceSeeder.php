<?php

use App\Account;
use App\ArchivematicaService;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Log;

class ArchivematicaServiceSeeder extends Seeder
{
    /*
     * Print a message to the screen and also write it to the info log
     */

    private function printAndLog(string $message)
    {
        print($message."\n");
        Log::info($message);
    }

    private function printAndLogError(string $message)
    {
        print("\033[31m".$message."\033[39m\n");
        Log::error($message);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ArchivematicaService::truncate();
        $dashboard = ArchivematicaService::create([
            'url' => "http://172.17.0.1:62080/api",
            'api_token' => "test:test",
            'service_type' => 'dashboard'
        ]);

        $storage = ArchivematicaService::create([
            'url' => "http://172.17.0.1:62081/api",
            'api_token' => "test:test",
            'service_type' => 'storage'
        ]);


        // Create a Personal Access Client if no one exists
        $clientRepository = new ClientRepository();
        try {
            $clientRepository->personalAccessClient();
        } catch (RuntimeException $exception) {
            $clientRepository->createPersonalAccessClient(null, "piqlConnect Personal Access Client", "http://localhost");
        }

        // Add default Archivematica Client for accessing Piql Connect trigger API
        $amUser = App\User::firstOrNew(['username' => 'archivematica', "account_uuid" => Account::first()->uuid]);
        $amUser->username = "archivematica";
        $amUser->password = Hash::make(Uuid::generate());
        $amUser->full_name = "Archivematica callback client";
        $amUser->email = "";
        $amUser->id = 'a762ca3f-7f14-40af-b3ba-e0d84f62881e';
        $amUser->save();


        $token  = $this->getAccessToken($amUser);

        $this->printAndLog("Important! Archivematica storage server POST-store AIP and DIP callbacks (GET) must be updated with url:\n"
            . route( 'api.ingest.triggers.am.callback', [$storage->id, '']).'/<package_uuid>'."\n"
            . "And headers:\n"
            . "Accept: application/json \n"
            . "Authorization: Bearer ".$token);
    }

    private function getAccessToken(\App\User $user) {

        $client = new GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://auth.piqlconnect.com',
            'http_errors' => false
        ]);

        $response = $client->post('/auth/realms/Demo/protocol/openid-connect/token', [
            'form_params' => [
                'client_id' => 'piql-service-callback',
                'grant_type' => 'password',
                'client_secret' => 'bd4240b7-de4b-4c14-99e6-ee79583221c3',
                'scope' => 'openid',
                'username' => $user->username,
                'password' => '1234',
            ],
        ]);
        if($response->getStatusCode() >= 400) {
            $this->printAndLogError("Get access token failed !!");
            $this->printAndLogError("Server responded: ". $response->getBody());
        }
        return json_decode((string) $response->getBody(), true)["access_token"] ?? "";
    }
}
