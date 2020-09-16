<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //TODO: env to control which seeders to run
        $this->call(SafeSpringS3ConfigurationSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(ArchiveSeeder::class);
        $this->call(HoldingSeeder::class);
    }
}
