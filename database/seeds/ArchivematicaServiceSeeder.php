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

        $found = null;
        if(App\ArchivematicaService::count() == 2) {
            $found = App\ArchivematicaService::all()[1];
        }
        $service = $found ?? new ArchivematicaService();
        $service->url = "http://172.17.0.1:62081/api";
        $service->save();
        $service->api_token = "test:test";
        $service->save();
        dump("Remember to set/update Archivematica storage server callbacks with this url: ");
        dump(route( 'api.ingest.triggers.am.callback', [$service->id, '']).'/<package_uuid>');
    }
}
