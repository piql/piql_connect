<?php

use Illuminate\Database\Seeder;

class FmuDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //TODO: env to control which seeders to run
        $this->call(FmuSafeSpringS3ConfigurationSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);
        $this->call(FmuUsersTableSeeder::class);
        $this->call(FmuAccountSeeder::class);
        $this->call(FmuArchiveSeeder::class);
        $this->call(FmuHoldingSeeder::class);
    }
}
