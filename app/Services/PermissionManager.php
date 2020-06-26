<?php

namespace App\Services;

use App\Enums\PermissionType;
use App\Permission;
use App\UserPermission;
use Illuminate\Support\Facades\DB;
use App\Traits\Uuids;

class PermissionManager 
{
    use Uuids;

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
        $role = new Permission;
        $role->name = $name;
        $role->description = $description;
        $role->parent_id = $groupId;
        $role->type = PermissionType::Role;
        return $role;
    } 

    public static function delete($id) {
        $permission = Permission::findOrFail($id);
        if ($permission->delete()) {
            UserPermission::where('permission_id', $id)->delete();
            if($permission->type == PermissionType::Group) {
                UserPermission::where('permission_id', $id)->delete();
                $roles = Permission::select('id')->where('parent_id', $id)->get();
                if(count($roles) > 0) {
                    $ids = collect($roles)->map(function($a){
                        return $a->id;
                    });
                    UserPermission::whereIn('permission_id', $ids)->delete();
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
            $pm = ['user_id'=> self::uuid2Bin($u), 'permission_id'=>$p->id];
            if(UserPermission::where($pm)->exists()) continue;
            $data[] = array_merge($pm, $timestamps);
            $results['permissions'][] = $p->id;
        }
        if(!UserPermission::insert($data)) return [
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
            UserPermission::where([
                'user_id'=> self::uuid2Bin($u), 'permission_id'=>$p->id
            ])->delete();
            $results['permissions'][] = $p->id;
        }
        $results["unassigned"] = true;
        return $results;
    }

    public static function userHasPermission($userId, $permissionId) {
        $rolePermissionEnum = PermissionType::Role;
        $groupPermissionEnum = PermissionType::Group;
        $permissionTable = (new Permission)->getTable();
        $userPermissionTable = (new UserPermission)->getTable();
        $userIdFormat = 
            "LOWER(CONCAT(".
                "SUBSTR(HEX(user_id), 1,  8), '-',".
                "SUBSTR(HEX(user_id), 9,  4), '-',".
                "SUBSTR(HEX(user_id), 13, 4), '-',".
                "SUBSTR(HEX(user_id), 17, 4), '-',".
                "SUBSTR(HEX(user_id), 21)".
            "))";
        
        $permissionsQuery = 
            "select roles.id role_id, `groups`.id group_id " .
            "from (select id, parent_id from $permissionTable where type=$rolePermissionEnum) roles " .
            "  left join (select id from $permissionTable where type=$groupPermissionEnum) `groups` " .
            "    on roles.parent_id=`groups`.id " .
            "where $permissionId in (roles.id, `groups`.id)";
        $userQuery = 
            "select ap.user_uuid from (".
                "select $userIdFormat user_uuid, permission_id from $userPermissionTable".
            ") ap where user_uuid='$userId' and permission_id in (role_id, group_id)";
        
        $permission = DB::table(DB::raw("($permissionsQuery) p"))
            ->select(DB::raw(
                "p.role_id, p.group_id, ($userQuery) user_id"
            ))->get();
            
        return (empty($permission) || !isset($permission[0])) ? [] : (array)$permission[0];
    }
}