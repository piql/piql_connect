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
    }
}
