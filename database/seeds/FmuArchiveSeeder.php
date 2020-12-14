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
            $archive = Archive::create($param);
            $metadata = \App\CollectionMetadata::create([
                "modified_by" => "",
                "metadata" => ["dc" => [
                    "title" => $archive->title,
                    "description" => $archive->description,
                ]]
            ]);
            $metadata->parent()->associate($archive);
            $metadata->save();
        })){

            return;
        }
    }
}
