<?php

use Illuminate\Database\Seeder;
use App\Holding;
use App\Archive;

class HoldingSeeder extends Seeder
{
    public function run()
    {
        Holding::truncate();
        $archives = Archive::all();
        $archives->map( function ($archive) { 
            Holding::create( [ 'title' => 'Images', 'position' => 0, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Images for '.$archive->title ] );
            Holding::create( [ 'title' => 'Documents', 'position' => 1, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Documents for '.$archive->title ] );
            Holding::create( [ 'title' => 'Video', 'position' => 2, 'owner_archive_uuid' => $archive->uuid, 'description' => 'Videos for '.$archive->title ] );
        });
    }
}
