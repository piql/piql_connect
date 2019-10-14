<?php

use Illuminate\Database\Seeder;
use App\Fonds;
use App\Holding;

class FondsSeeder extends Seeder
{
    public function run()
    {
        Fonds::truncate();
        $holdings = Holding::all();
        $holdings->map( function ($holding) { 
            Fonds::create( [ 'title' => 'Images', 'position' => 0, 'owner_holding_uuid' => $holding->uuid, 'description' => 'Images for '.$holding->title ] );
            Fonds::create( [ 'title' => 'Documents', 'position' => 1, 'owner_holding_uuid' => $holding->uuid, 'description' => 'Documents for '.$holding->title ] );
            Fonds::create( [ 'title' => 'Video', 'position' => 2, 'owner_holding_uuid' => $holding->uuid, 'description' => 'Videos for '.$holding->title ] );
        });
    }
}
