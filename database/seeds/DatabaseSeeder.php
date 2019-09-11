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
        $this->call(UsersTableSeeder::class);
        //$this->call(FilesTableSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);

        if(env('APP_DEBUG_DB_SEED_JOB_DATA', false)) {
            $this->call(DummyDataSeeder::class);
        }
    }
}
