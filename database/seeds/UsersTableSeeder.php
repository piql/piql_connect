<?php

use Illuminate\Database\Seeder;
use App\Traits\Uuids;

class UsersTableSeeder extends Seeder
{
    use Uuids;

    public function run()
    {
        DB::table('users')->insert([
            'id' => hex2bin(str_replace('-','', (string)Uuid::generate())),
            'username' => "tu-".Str::random(4),
            'password' => bcrypt('123123'),
            'full_name' => 'Kåre Andersen',
            'email' => 'kare.andersen@piql.com',
        ]);
    }
}
