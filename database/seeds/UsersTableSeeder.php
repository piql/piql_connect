<?php

use Illuminate\Database\Seeder;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    use Uuids;

    public function run()
    {
        $found = App\User::where('username', '=', 'simen')->first();
        $user = $found ?? new App\User();
        $user->username = "simen";
        $user->password = Hash::make("simen");
        $user->full_name = "Simen Fjeld-Olsen";
        $user->email = "simen.fjeldolsen@piql.com";
        $user->save();
        $user = App\User::findByUsername("simen");
        $user->id = '07da0453-857f-4645-92ac-9a3add6427cf';
        $user->save();

        $found = App\User::where('username', '=', 'kare')->first();
        $user = $found ?? new App\User();
        $user = new App\User();
        $user->username = "kare";
        $user->password = Hash::make("kare");
        $user->full_name = "Kåre Andersen";
        $user->email = "kare.andersen@piql.com";
        $user->save();

    }
}
//            'id' => '07da0453-857f-4645-92ac-9a3add6427cf',
