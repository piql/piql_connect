<?php

use App\Account;
use Illuminate\Database\Seeder;
use App\S3Configuration;
use App\StorageLocation;
use App\User;

class S3ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ss_test_key_id = "";
        $ss_test_secret = "";
        $ss_test_url = "";
        $ss_test_bucket = "";

        if( S3Configuration::where('key_id', $ss_test_key_id)->count() !== 0 ) {
            echo "S3ConfigurationSeeder already applied";
            return;
        }

        $s3Config = S3Configuration::create([
            'url' => $ss_test_url,
            'key_id' => $ss_test_key_id,
            'secret' => $ss_test_secret,
            'bucket' => $ss_test_bucket
        ]);

        $owner = User::where('username','kare')->first()
            ?? User::first()
            ?? factory(App\User::class)->create([
                "account_uuid" => Account::first()->uuid
            ]);
        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Aip',
            'human_readable_name' => "S3 Aip Store"
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Dip',
            'human_readable_name' => "S3 Dip Store"
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Aip',
            'human_readable_name' => "Other Aip Store"
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Dip',
            'human_readable_name' => "Other Dip Store"
        ]);

        // Messages
        $ss_test_key_id = "";
        $ss_test_secret = "";
        $ss_test_url = "";
        $ss_test_bucket = "";

        $s3Config = S3Configuration::create([
            'url' => $ss_test_url,
            'key_id' => $ss_test_key_id,
            'secret' => $ss_test_secret,
            'bucket' => $ss_test_bucket
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Message',
            'human_readable_name' => "S3 Message Inbox"
        ]);

        // Jobs
        $ss_test_key_id = "A027DQI8VXPIJETYNXZQ";
        $ss_test_secret = "OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg";
        $ss_test_url = "https://s3.osl1.safedc.net";
        $ss_test_bucket = "piqlfilm";

        $s3Config = S3Configuration::create([
            'url' => $ss_test_url,
            'key_id' => $ss_test_key_id,
            'secret' => $ss_test_secret,
            'bucket' => $ss_test_bucket
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Job',
            'human_readable_name' => "S3 PiqlFilm Outbox"
        ]);
    }
}