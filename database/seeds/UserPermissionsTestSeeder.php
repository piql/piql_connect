<?php

use Illuminate\Database\Seeder;
use Webpatser\Uuid\Uuid;
use App\User;
use App\Bag;
use App\File;
use App\Holding;
use Faker\Factory as Faker;

class UserPermissionsTestSeeder extends Seeder
{
    /**
     * Quick hack to seed some data for testing user access separation
     *
     * @return void
     */
    public function run()
    {
        // Set up the basic stuff
        $this->call(HoldingSeeder::class);
        $this->call(FondsSeeder::class);
        $this->call(ArchivematicaServiceSeeder::class);

        $faker = Faker::create();

        // Create demo users
        $found = App\User::where('username', '=', 'Alfredo')->first();
        $alfredo= $found ?? new App\User();
        $alfredo->username = "Alfredo";
        $alfredo->password = Hash::make("Alfredo");
        $alfredo->full_name = "Alfredo Trujillo";
        $alfredo->email = "alfredo.trujillo@piql.com";
        $alfredo->save();

        $found = App\User::where('username', '=', 'kare')->first();
        $kare = $found ?? new App\User();
        $kare->username = "kare";
        $kare->password = Hash::make("kare");
        $kare->full_name = "Kåre Andersen";
        $kare->email = "kare.andersen@piql.com";
        $kare->save();

        // Create 20 Alfredo Bags
        for( $i = 0; $i < 20; $i++) {
            $bag = new App\Bag();
            $bag->name = $faker->date().$faker->randomNumber(6);
            $bag->owner = $alfredo->id;
            $bag->created_at = $faker->dateTime();
            $bag->save();
            $bag->status = "complete";
            $bag->save();

            $bag->storage_properties->holding_name = "Documents";
            $bag->storage_properties->archive_uuid = Holding::all()->where('title', 'Forsvarsmuseet')->first()->uuid;
            $bag->storage_properties->save();

            $file = new App\File();
            $file->fileName = "alfredos Apollo 11 {$i}.mp4";
            $file->bag_id = $bag->id;
            $file->filesize = 952672;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "alfredos Fur Elise {$i}.ogg";
            $file->bag_id = $bag->id;
            $file->filesize = 2891840;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "alfredos Great Sphinx of Giza {$i}.tif";
            $file->bag_id = $bag->id;
            $file->filesize = 21020416;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "alfredos I have a dream {$i}.mp4";
            $file->bag_id = $bag->id;
            $file->filesize = 110019;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "alfredos Mona Lisa, by Leonardo da Vinci, from C2RMF retouched {$i}.jpg";
            $file->bag_id = $bag->id;
            $file->filesize = 94311158;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "alfredos The Digital Dilemma {$i}.pdf";
            $file->bag_id = $bag->id;
            $file->filesize = 4808545;
            $file->uuid = Uuid::generate();
            $file->save();
        }

        // Create 20 Kare Bags
        for( $i = 0; $i < 20; $i++) {
            $bag = new App\Bag();
            $bag->name = $faker->date().$faker->randomNumber(6);
            $bag->owner = $kare->id;
            $bag->created_at = $faker->dateTime();
            $bag->save();
            $bag->status = "complete";
            $bag->save();

            $bag->storage_properties->holding_name = "Documents";
            $bag->storage_properties->archive_uuid = Holding::all()->where('title', 'Forsvarsmuseet')->first()->uuid;
            $bag->storage_properties->save();

            $file = new App\File();
            $file->fileName = "kares Apollo 11 {$i}.mp4";
            $file->bag_id = $bag->id;
            $file->filesize = 952672;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "kares Fur Elise {$i}.ogg";
            $file->bag_id = $bag->id;
            $file->filesize = 2891840;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "kares Great Sphinx of Giza {$i}.tif";
            $file->bag_id = $bag->id;
            $file->filesize = 21020416;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "kares I have a dream {$i}.mp4";
            $file->bag_id = $bag->id;
            $file->filesize = 110019;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "kares Mona Lisa, by Leonardo da Vinci, from C2RMF retouched {$i}.jpg";
            $file->bag_id = $bag->id;
            $file->filesize = 94311158;
            $file->uuid = Uuid::generate();
            $file->save();

            $file = new App\File();
            $file->fileName = "kares The Digital Dilemma {$i}.pdf";
            $file->bag_id = $bag->id;
            $file->filesize = 4808545;
            $file->uuid = Uuid::generate();
            $file->save();
        }

        $job = new App\Job();
        $job->name = "alfredos_reel";
        $job->owner = $alfredo->id;
        $job->save();
        $job->status = "ingesting";
        $job->save();

        $job = new App\Job();
        $job->name = "kares_reel";
        $job->owner = $kare->id;
        $job->save();
        $job->status = "ingesting";
        $job->save();
    }
}
