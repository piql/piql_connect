<?php

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //TODO: env to control which seeders to run
        $this->call(TestSafeSpringS3ConfigurationSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);
        $this->call(TestUsersTableSeeder::class);
        $this->call(TestAccountSeeder::class);
        $this->call(TestArchiveSeeder::class);
        $this->call(TestHoldingSeeder::class);
    }
}
