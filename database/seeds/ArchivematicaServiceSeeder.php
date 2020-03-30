<?php

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

        // Override storage id from config
        $storage->id = env('ARCHIVEMATICA_SERVICE_SEEDER_STORAGE_ID', $storage->id);
        $storage->store();

        // Create a Personal Access Client if no one exists
        $clientRepository = new ClientRepository();
        try {
            $clientRepository->personalAccessClient();
        } catch (RuntimeException $exception) {
            $clientRepository->createPersonalAccessClient(null, "piqlConnect Personal Access Client", "http://localhost");
        }

        // Add default Archivematica Client for accessing Piql Connect trigger API
        $amUser = App\User::firstOrNew(['username' => 'archivematica']);
        $amUser->username = "archivematica";
        $amUser->password = Hash::make(Uuid::generate());
        $amUser->full_name = "Archivematica callback client";
        $amUser->email = "";
        $amUser->save();
        $token  = $amUser->createToken("archivematica_callback_token")->accessToken;

        $this->printAndLog("Important! Archivematica storage server POST-store AIP and DIP callbacks (GET) must be updated with url:\n"
            . route( 'api.ingest.triggers.am.callback', [$storage->id, '']).'/<package_uuid>'."\n"
            . "And headers:\n"
            . "Accept: application/json \n"
            . "Authorization: Bearer ".$token);
    }
}
