<?php

namespace App\Services;

use App\Enums\AccessControlType;
use App\AccessControl;
use App\UserAccessControl;
use Illuminate\Support\Facades\DB;
use App\Traits\Uuids;

class AccessControlManager 
{
    use Uuids;

    public static function createGroup($name, $description) 
    {
        $group = new AccessControl;
        $group->name = $name;
        $group->description = $description;
        $group->type = AccessControlType::Group;
        return $group;
    } 

    public static function createAction($groupId, $name, $description) 
    {
        $role = new AccessControl;
        $role->name = $name;
        $role->description = $description;
        $role->parent_id = $groupId;
        $role->type = AccessControlType::Role;
        return $role;
    } 

    public static function delete($id) {
        $accessControl = AccessControl::findOrFail($id);
        if ($accessControl->delete()) {
            UserAccessControl::where('access_control_id', $id)->delete();
            if($accessControl->type == AccessControlType::Group) {
                UserAccessControl::where('access_control_id', $id)->delete();
                $roles = AccessControl::select('id')->where('parent_id', $id)->get();
                if(count($roles) > 0) {
                    $ids = collect($roles)->map(function($a){
                        return $a->id;
                    });
                    UserAccessControl::whereIn('access_control_id', $ids)->delete();
                }
                AccessControl::where('parent_id', $id)->delete();
            }
            return $accessControl;
        }
        return null;
    }

    public static function assignAccessControlsToUsers(array $accessControls, array $users) {
        $perms = AccessControl::select('id')->whereIn('id', $accessControls)->get();
        $data = ["assigned"=>false];
        if(count($perms) == 0) return array_merge([
            "error"=> "Invalid or empty Access Controls",
        ], $data);
        if(count($users) == 0) return array_merge([
            "error"=> "Invalid or empty users",
        ], $data);
        $timestamps = [
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ];
        $data = [];
        $results = ['users' => $users, 'access_controls'=>[]];
        foreach($perms as $p) foreach($users as $u) {
            $pm = ['user_id'=> self::uuid2Bin($u), 'access_control_id'=>$p->id];
            if(UserAccessControl::where($pm)->exists()) continue;
            $data[] = array_merge($pm, $timestamps);
            $results['access_controls'][] = $p->id;
        }
        if(!UserAccessControl::insert($data)) return [
            'error' => "failed to create Access Controls"
        ];
        $results["assigned"] = count($data) > 0;
        return $results;
    }


    public static function removeAccessControlsFromUsers(array $accessControls, array $users) {
        $perms = AccessControl::select('id')->whereIn('id', $accessControls)->get();
        $data = ["unassigned"=>false];
        if(count($perms) == 0) return array_merge([
            "error"=> "Invalid or empty AccessControls",
        ], $data);
        if(count($users) == 0) return array_merge([
            "error"=> "Invalid or empty users",
        ], $data);
        $results = array_merge(['users' => $users, 'access_controls'=>[]], $data);
        foreach($perms as $p) foreach($users as $u) {
            UserAccessControl::where([
                'user_id'=> self::uuid2Bin($u), 'access_control_id'=>$p->id
            ])->delete();
            $results['access_controls'][] = $p->id;
        }
        $results["unassigned"] = true;
        return $results;
    }

    public static function userHasAccessControl($userId, $accessControlId) {
        $roleAccessControlEnum = AccessControlType::Role;
        $groupAccessControlEnum = AccessControlType::Group;
        $accessControlTable = (new AccessControl)->getTable();
        $userAccessControlTable = (new UserAccessControl)->getTable();
        $userIdFormat = 
            "LOWER(CONCAT(".
                "SUBSTR(HEX(user_id), 1,  8), '-',".
                "SUBSTR(HEX(user_id), 9,  4), '-',".
                "SUBSTR(HEX(user_id), 13, 4), '-',".
                "SUBSTR(HEX(user_id), 17, 4), '-',".
                "SUBSTR(HEX(user_id), 21)".
            "))";
        
        $accessControlsQuery = 
            "select roles.id role_id, `groups`.id group_id " .
            "from (select id, parent_id from $accessControlTable where type=$roleAccessControlEnum) roles " .
            "  left join (select id from $accessControlTable where type=$groupAccessControlEnum) `groups` " .
            "    on roles.parent_id=`groups`.id " .
            "where $accessControlId in (roles.id, `groups`.id)";
        $userQuery = 
            "select ap.user_uuid from (".
                "select $userIdFormat user_uuid, access_control_id from $userAccessControlTable".
            ") ap where user_uuid='$userId' and access_control_id in (role_id, group_id)";
        
        $accessControl = DB::table(DB::raw("($accessControlsQuery) p"))
            ->select(DB::raw(
                "p.role_id, p.group_id, ($userQuery) user_id"
            ))->get();
            
        return (empty($accessControl) || !isset($accessControl[0])) ? [] : (array)$accessControl[0];
    }
}