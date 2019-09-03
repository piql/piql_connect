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

        $found = App\User::where('username', '=', 'kare')->first();
        $found ?? App\User::where('email', '=', 'kare.andersen@piql.com')->first();
        $user = $found ?? new App\User();
        $user->username = "kare";
        $user->password = Hash::make("kare");
        $user->full_name = "Kåre Andersen";
        $user->email = "kare.andersen@piql.com";
        $user->save();

        $found = App\User::where('username', '=', 'swhl')->first();
        $user = $found ?? new App\User();
        $user->username = "swhl";
        $user->password = Hash::make("swhl");
        $user->full_name = "Håkon Larsson";
        $user->email = "hakon.larsson@piql.com";
        $user->save();

    }
}
