<?php

use App\Organization;
use App\Account;
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
        //NOTE: Default organization and account are currently created by migrations, but
        //      fallbacks are created here in case seeds are run without migrating.
        $org=Organization::first();
        if(!$org)
        {
            $org = Organization::create([
                'name' => 'Default Organization',
                'uuid' => Uuid::generate()->string
            ]);
        }

        $account = Account::first();
        if(!$account) {
            $account = Account::create([
                'title' => 'Default Account',
                'description' => 'Default Account',
                'organization_uuid' => $org->uuid,
                'uuid' => Uuid::generate()->string
            ]);
        }

        //TODO: The default seeder creates a simple archival arrangement with only one
        //      collection and holding. Consider removing this and use specific seeders for
        //      development, testing and demo.
        $collection = Collection::create([
            'title' => 'Default Collection',
            'description' => '',
            'account_uuid' => $account->uuid,
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
