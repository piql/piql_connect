<?php

use Illuminate\Database\Seeder;
use App\Archive;
use \App\Traits\SeederOperations;

class FmuArchiveSeeder extends Seeder
{
    use SeederOperations;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Archive::truncate();

        if($this->seedFromFile(function($param) {
                Archive::create($param);
            })){

            return;
        }
    }
}
