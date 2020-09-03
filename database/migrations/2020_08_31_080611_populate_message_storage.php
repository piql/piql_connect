<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\S3Configuration;
use App\StorageLocation;
use App\User;

class PopulateMessageStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::first();

        // Create configuration when upgrading a system
        // For a fresh migration it will be done by seeders
        if ($user) {
            $s3Configuration = S3Configuration::create([
                'url' => 'https://s3.osl1.safedc.net',
                'key_id' => 'A027DQI8VXPIJETYNXZQ',
                'secret' => 'OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg',
                'bucket' => 'connect-test-messages-inbox'
            ]);

            $storageLocation = StorageLocation::create([
                'locatable_type' => 'App\S3Configuration',
                'locatable_id' => $s3Configuration->id,
                'owner_id' => $user->id,
                'storable_type' => 'App\Message',
                'human_readable_name' => 'Message Inbox'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $storageLocation = StorageLocation::where('storable_type', 'App\Message')->first();
        if ($storageLocation) {
            $storageLocation->forceDelete();

            $s3Configuration = S3Configuration::find($storageLocation->locatable_type);
            if ($s3Configuration) {
                $s3Configuration->forceDelete();
            }
        }
    }
}
