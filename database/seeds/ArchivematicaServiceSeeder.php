<?php

use App\ArchivematicaService;
use Illuminate\Database\Seeder;

class ArchivematicaServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $found = App\ArchivematicaService::first();
        $service = $found ?? new ArchivematicaService();
        $service->url = "http://172.17.0.1:62080/api";
        $service->api_token = "test:test";
        $service->save();
    }
}
