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
        $service->url = "http://192.168.100.2:61080/api";
        $service->api_token = "test:test";
        $service->save();
    }
}
