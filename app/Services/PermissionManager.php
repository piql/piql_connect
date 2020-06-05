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
        $timestamps = [
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ];
        foreach($perms as $p) foreach($users as $u) {
            $data[] = array_merge(['user_id'=> $u, 'permission_id'=>$p->id], $timestamps);
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

    public static function userHasPermission($userId, $permissionId) {
        $actionPermissionEnum = PermissionType::Action;
        $groupPermissionEnum = PermissionType::Group;
        
        $permissionsQuery = 
            "select actions.id action_id, `groups`.id group_id " .
            "from (select id, parent_id from permissions where type=$actionPermissionEnum) actions " .
            "  left join (select id from permissions where type=$groupPermissionEnum) `groups` " .
            "    on actions.parent_id=`groups`.id " .
            "where $permissionId in (actions.id, `groups`.id)";
        $userQuery = "select user_id from user_permissions where user_id=$userId and permission_id in (action_id, group_id)";
        
        $permission = DB::table(DB::raw("($permissionsQuery) p"))
            ->select(DB::raw(
                "p.action_id, p.group_id, ($userQuery) user_id"
            ))->get();

        return $permission;
    }
}