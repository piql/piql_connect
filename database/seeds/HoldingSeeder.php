<?php

use Illuminate\Database\Seeder;
use App\Holding;
use App\Archive;
use \App\Traits\SeederOperations;

class HoldingSeeder extends Seeder
{
    //TODO: This class is only used by UserPermissionsTestSeeder, which seems obsolete.
    //      Reevaluate in the next seeder refactoring.

    use SeederOperations;

    public function run()
    {
        Holding::truncate();
        $index = 0;
        if($this->seedFromFile(function($param) use ($index) {
            $param["position"] = $index;
            $holding = Holding::create($param);
            $index++;
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
