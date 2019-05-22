<?php

use Illuminate\Database\Seeder;
use App\Traits\Uuids;

class UsersTableSeeder extends Seeder
{
    use Uuids;

    public function run()
    {
        DB::table('users')->insert([
            'id' => $this->generateBin16Uuid(),
            'username' => "tu-".Str::random(4),
            'password' => bcrypt('123123'),
            'full_name' => 'Kåre Andersen',
            'email' => 'kare.andersen'.rand(1,100).'@piql.com'
        ]);
    }
}
