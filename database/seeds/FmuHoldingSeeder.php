<?php

use Illuminate\Database\Seeder;
use App\Holding;
use App\Archive;
use \App\Traits\SeederOperations;

class FmuHoldingSeeder extends Seeder
{
    use SeederOperations;

    public function run()
    {
        Holding::truncate();
        $index = 0;
        if($this->seedFromFile(function($param) use ($index) {
            $param["position"] = $index;
            $holding = Holding::create($param);
            $index++;

            $metadata = \App\HoldingMetadata::create([
                "modified_by" => "",
                "metadata" => ["dc" => [
                    "title" => $holding->title,
                    "description" => $holding->description,
                ]]
            ]);
            $metadata->parent()->associate($holding);
            $metadata->save();
        })){

            return;
        }

        $archives = Archive::all();
        $archives->map( function ($archive) {
            Holding::create( [ 'title' => 'Images', 'position' => 0, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Images for '.$archive->title ] );
            Holding::create( [ 'title' => 'Documents', 'position' => 1, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Documents for '.$archive->title ] );
            Holding::create( [ 'title' => 'Video', 'position' => 2, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Videos for '.$archive->title ] );
        });
    }
}
