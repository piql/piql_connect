<?php

use App\Enums\AccessControlType;
use App\AccessControl;
use App\User;
use App\UserAccessControl;
use Webpatser\Uuid\Uuid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        $admin = new User([
            "username" =>  "admin",
            "password" =>  Hash::make("5er^!C3@D3iN##1" ?? Uuid::generate()),
            "full_name" => "Piql Admin",
            "email" =>     "admin@piql.com"
        ]);
        if (!$admin->save()) {
            DB::rollBack();
            exit("Failed to seed admin user");
        }
        $adminGroup = new AccessControl([
            'name' => 'Admin', 
            'type' => AccessControlType::Role, 
            'description' => 'Service administrator'
        ]);
        if (!$adminGroup->save()) {
            DB::rollBack();
            exit("Failed to seed admin user group");
        }
        (new UserAccessControl([
            'user_id'=> $admin->id, 
            'permission_id'=>$adminGroup->id
        ]))->save();
        DB::commit();
    }
}
