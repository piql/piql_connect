<?php

use Illuminate\Database\Seeder;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //TODO: env to control which seeders to run
        $this->call(DemoSafeSpringS3ConfigurationSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);
        $this->call(DemoUsersTableSeeder::class);
        $this->call(DemoAccountSeeder::class);
        $this->call(DemoArchiveSeeder::class);
        $this->call(DemoHoldingSeeder::class);
    }
}
