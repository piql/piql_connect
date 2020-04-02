<?php

use Illuminate\Database\Seeder;
use Webpatser\Uuid\Uuid;
use App\User;
use App\Bag;
use App\File;
use App\Archive;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void5
     */
    public function run()
    {
        // Cretae demo user
        $found = App\User::where('username', '=', 'Alfredo')->first();
        $user = $found ?? new App\User();
        $user->username = "Alfredo";
        $user->password = Hash::make("Alfredo");
        $user->full_name = "Alfredo Trujillo";
        $user->email = "alfredo.trujillo@piql.com";
        $user->save();

        // Create Bag
        $found = App\Bag::where('name', '=', 'reader_test_reel')->first();
        $bag = $found ?? new App\Bag();
        $bag->name = "reader_test_reel";
        $bag->owner = $user->id;
        $bag->created_at = "2019-04-14 09:56:35";
        $bag->save();
        $bag->status = "complete";
        $bag->save();

        $bag->storage_properties->holding_name = "Documents";
        $bag->storage_properties->archive_uuid = Archive::all()->where('title', 'Forsvarsmuseet')->first()->uuid;
        $bag->storage_properties->save();

        // Create files

        $found = App\File::where('fileName', '=', 'Apollo 11.mp4')->first();
        $file = $found ?? new App\File();
        $file->fileName = "Apollo 11.mp4";
        $file->bag_id = $bag->id;
        $file->filesize = 952672;
        $file->uuid = Uuid::generate();
        $file->save();

        $found = App\File::where('fileName', '=', 'Fur Elise.ogg')->first();
        $file = $found ?? new App\File();
        $file->fileName = "Fur Elise.ogg";
        $file->bag_id = $bag->id;
        $file->filesize = 2891840;
        $file->uuid = Uuid::generate();
        $file->save();

        $found = App\File::where('fileName', '=', 'Great Sphinx of Giza.tif')->first();
        $file = $found ?? new App\File();
        $file->fileName = "Great Sphinx of Giza.tif";
        $file->bag_id = $bag->id;
        $file->filesize = 21020416;
        $file->uuid = Uuid::generate();
        $file->save();

        $found = App\File::where('fileName', '=', 'I have a dream.mp4')->first();
        $file = $found ?? new App\File();
        $file->fileName = "I have a dream.mp4";
        $file->bag_id = $bag->id;
        $file->filesize = 110019;
        $file->uuid = Uuid::generate();
        $file->save();

        $found = App\File::where('fileName', '=', 'Mona Lisa, by Leonardo da Vinci, from C2RMF retouched.jpg')->first();
        $file = $found ?? new App\File();
        $file->fileName = "Mona Lisa, by Leonardo da Vinci, from C2RMF retouched.jpg";
        $file->bag_id = $bag->id;
        $file->filesize = 94311158;
        $file->uuid = Uuid::generate();
        $file->save();

        $found = App\File::where('fileName', '=', 'The Digital Dilemma.pdf')->first();
        $file = $found ?? new App\File();
        $file->fileName = "The Digital Dilemma.pdf";
        $file->bag_id = $bag->id;
        $file->filesize = 4808545;
        $file->uuid = Uuid::generate();
        $file->save();

        // Create job

        $found = App\Job::where('name', '=', 'reader_test_reel')->first();
        $job = $found ?? new App\Job();
        $job->name = "reader_test_reel";
        $job->owner = $user->id;
        $job->save();
        $job->bags()->toggle($bag);
        $job->status = "ingesting";
        $job->save();

        // Create more files (for dashboard)
        // factory(\App\File::class, 100)->create();
        // $bags = Bag::all()->where('owner', $user->id);
        // foreach ($bags as $bag) {
        //     $bag->applyTransition('complete');
        // }
    }
}
