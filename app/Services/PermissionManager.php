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
            DB::table('user_permissions')->where('permission_id', $id)->delete();
            if($permission->type == PermissionType::Group) {
                DB::table('user_permissions')->where('permission_id', $id)->delete();
                $actions = Permission::select('id')->where('parent_id', $id)->get();
                if(count($actions) > 0) {
                    $ids = collect($actions)->map(function($a){
                        return $a->id;
                    });
                    DB::table('user_permissions')->whereIn('permission_id', $ids)->delete();
                }
                Permission::where('parent_id', $id)->delete();
            }
            return $permission;
        }
        return null;
    }

    public static function assignPermissionsToUsers(array $permissions, array $users) {
        $perms = Permission::select('id')->whereIn('id', $permissions)->get();
        $data = ["assigned"=>false];
        if(count($perms) == 0) return array_merge([
            "error"=> "Invalid or empty permissions",
        ], $data);
        if(count($users) == 0) return array_merge([
            "error"=> "Invalid or empty users",
        ], $data);
        $timestamps = [
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ];
        $data = [];
        $results = ['users' => $users, 'permissions'=>[]];
        foreach($perms as $p) foreach($users as $u) {
            $pm = ['user_id'=> $u, 'permission_id'=>$p->id];
            if(DB::table('user_permissions')->where($pm)->exists()) continue;
            $data[] = array_merge($pm, $timestamps);
            $results['permissions'][] = $p->id;
        }
        if(!DB::table('user_permissions')->insert($data)) return [
            'error' => "failed to create permissions"
        ];
        $results["assigned"] = count($data) > 0;
        return $results;
    }


    public static function removePermissionsFromUsers(array $permissions, array $users) {
        $perms = Permission::select('id')->whereIn('id', $permissions)->get();
        $data = ["unassigned"=>false];
        if(count($perms) == 0) return array_merge([
            "error"=> "Invalid or empty permissions",
        ], $data);
        if(count($users) == 0) return array_merge([
            "error"=> "Invalid or empty users",
        ], $data);
        $results = array_merge(['users' => $users, 'permissions'=>[]], $data);
        foreach($perms as $p) foreach($users as $u) {
            DB::table('user_permissions')->where([
                'user_id'=> $u, 'permission_id'=>$p->id
            ])->delete();
            $results['permissions'][] = $p->id;
        }
        $results["unassigned"] = true;
        return $results;
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