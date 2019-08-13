<?php

use Illuminate\Database\Seeder;
use App\Traits\Uuids;

class UsersTableSeeder extends Seeder
{
    use Uuids;

    public function run()
    {
        // DB::table('users')->insert([
        //     'id' => '1',
        //     'username' => "simen",
        //     'password' => "simen",
        //     'full_name' => 'Simen Fjeld-Olsen',
        //     'email' => 'simen.fjeldolsen@piql.com',
        //     'api_token' => '1'
        // ]);
        $user = new App\User();
        $user->username = "simen";
        $user->password = "simen";
        $user->full_name = "Simen Fjeld-Olsen";
        $user->email = "simen.fjeldolsen@piql.com";
        $user->save();
        $user = App\User::findByUsername("simen");
        $user->id = '07da0453-857f-4645-92ac-9a3add6427cf';
        $user->save();
        
    }
}
//            'id' => '07da0453-857f-4645-92ac-9a3add6427cf',
