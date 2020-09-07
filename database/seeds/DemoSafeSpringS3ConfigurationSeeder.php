<?php

use Illuminate\Database\Seeder;
use App\S3Configuration;
use App\StorageLocation;
use App\User;

class DemoSafeSpringS3ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ss_test_key_id = "A027DQI8VXPIJETYNXZQ";
        $ss_test_secret = "OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg";
        $ss_test_url = "https://s3.osl1.safedc.net";
        $ss_test_bucket = "demo-burgerbibliothek";

        if( S3Configuration::where('key_id', $ss_test_key_id)->count() !== 0 ) {
            echo "Safespring S3 test already seeded";
            return;
        }

        $s3Config = S3Configuration::create([
            'url' => $ss_test_url,
            'key_id' => $ss_test_key_id,
            'secret' => $ss_test_secret,
            'bucket' => $ss_test_bucket
        ]);

        $owner = User::where('username','kare')->first() ?? User::first() ?? factory(App\User::class, 1)->create()->first();

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Aip',
            'human_readable_name' => "Safespring S3 Aip Store"
        ]);

        StorageLocation::create([
            'owner_id' => $owner->id,
            'locatable_id' => $s3Config->id,
            'locatable_type' => 'App\S3Configuration',
            'storable_type' => 'App\Dip',
            'human_readable_name' => "Safespring S3 Dip Store"
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

        // Message
        $ss_test_key_id = "A027DQI8VXPIJETYNXZQ";
        $ss_test_secret = "OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg";
        $ss_test_url = "https://s3.osl1.safedc.net";
        $ss_test_bucket = "demo-burgerbibliothek-messages-inbox";

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
            'human_readable_name' => "Safespring S3 Message Inbox"
        ]);

        // Job
        $ss_test_key_id = "A027DQI8VXPIJETYNXZQ";
        $ss_test_secret = "OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg";
        $ss_test_url = "https://s3.osl1.safedc.net";
        $ss_test_bucket = "jobs-outbox";

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
            'human_readable_name' => "Safespring S3 Job Outbox"
        ]);
    }
}
