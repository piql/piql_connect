<?php

use App\Organization;
use App\Archive;
use App\Collection;
use App\Holding;
use Illuminate\Database\Seeder;
use Webpatser\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $org = Organization::create([
            'name' => 'Default Organization',
            'uuid' => Uuid::generate()->string
        ]);

        $archive = Archive::create([
            'title' => 'Default Archive',
            'description' => '',
            'organization_uuid' => $org->uuid,
            'uuid' => Uuid::generate()->string
        ]);

        //TODO: The default seeder creates a simple archival arrangement with only one
        //      collection and holding. Consider removing this and use specific seeders for
        //      development, testing and demo.
        $collection = Collection::create([
            'title' => 'Default Collection',
            'description' => '',
            'archive_uuid' => $archive->uuid,
            'uuid' => Uuid::generate()->string
        ]);

        Holding::create([
            'title' => 'Default Holding',
            'description' => '',
            'collection_uuid' => $collection->uuid,
            'uuid' => Uuid::generate()->string
        ]);

        //TODO: Consider seeding only a default admin here
        $this->call(UsersTableSeeder::class);
        $this->call(SafeSpringS3ConfigurationSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);
    }
}
