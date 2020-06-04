<?php

namespace App\Services;

use App\Enums\PermissionType;
use App\Permission;
use Illuminate\Support\Facades\DB;

class PermissionManager 
{
    public static function createGroup($name, $description) 
    {
        $group = new Permission;
        $group->name = $name;
        $group->description = $description;
        $group->type = PermissionType::Group;
        return $group;
    } 

    public static function createAction($groupId, $name, $description) 
    {
        $action = new Permission;
        $action->name = $name;
        $action->description = $description;
        $action->parent_id = $groupId;
        $action->type = PermissionType::Action;
        return $action;
    } 

    public static function delete($id) {
        $permission = Permission::findOrFail($id);
        if ($permission->delete()) {
            if($permission->type == PermissionType::Group) 
                Permission::where('parent_id', $id)->delete();
            return $permission;
        }
        return null;
    }

    public static function assignPermissionsToUsers(array $permissions, array $users) {
        $perms = Permission::select('id')->whereIn('id', $permissions)->get();
        $data = [];
        foreach($perms as $p) foreach($users as $u) {
            $data[] = ['user_id'=> $u, 'permission_id'=>$p->id];
        }
        if(!DB::table('user_permissions')->insert($data)) return null;
        return [
            'users' => $users, 'permissions' => $perms, 'operation'=>'assigned',
        ];
    }


    public static function removePermissionsFromUsers(array $permissions, array $users) {
        $perms = Permission::select('id')->whereIn('id', $permissions)->get();
        foreach($perms as $p) foreach($users as $u) {
            DB::table('user_permissions')->where([
                'user_id'=> $u, 'permission_id'=>$p->id
            ])->delete();
        }
        return [
            'users' => $users, 'permissions' => $perms, 'operation'=>'unAssigned',
        ];
    }
}