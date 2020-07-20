<?php

namespace App\Services;

use App\Enums\AccessControlType;
use App\AccessControl;
use App\Http\Resources\AccessControlResource;
use App\RolePermission;
use App\UserAccessControl;
use Illuminate\Support\Facades\DB;
use App\Traits\Uuids;
use Exception;

class AccessControlManager 
{
    use Uuids;

    private const uuidToString = 
            "LOWER(CONCAT(".
                "SUBSTR(HEX(user_id), 1,  8), '-',".
                "SUBSTR(HEX(user_id), 9,  4), '-',".
                "SUBSTR(HEX(user_id), 13, 4), '-',".
                "SUBSTR(HEX(user_id), 17, 4), '-',".
                "SUBSTR(HEX(user_id), 21)".
            "))";

    public static function createPermissionGroup($name, $description) 
    {
        $group = new AccessControl;
        $group->name = $name;
        $group->description = $description;
        $group->type = AccessControlType::PermissionGroup;
        return $group;
    } 

    private static function validatePermissionGroup($groupId) {
        if($groupId == null) return;
        $group = AccessControl::find($groupId);
        if($group == null) throw new Exception("Permission Group with id '$groupId' does not exist");
        else if ($group->type != AccessControlType::PermissionGroup)
            throw new Exception("$group->name is not a Permission Group");
        return $group;
    }

    public static function createPermission($groupId, $name, $description) 
    {
        self::validatePermissionGroup($groupId);
        $permission = new AccessControl;
        $permission->name = $name;
        $permission->description = $description;
        $permission->group_id = $groupId;
        $permission->type = AccessControlType::Permission;
        return $permission;
    } 

    public static function createRole($name, $description) 
    {
        $role = new AccessControl;
        $role->name = $name;
        $role->description = $description;
        $role->type = AccessControlType::Role;
        return $role;
    } 

    public static function delete($id) {
        $accessControl = AccessControl::findOrFail($id);
        if ($accessControl->delete()) {
            UserAccessControl::where('access_control_id', $id)->delete();
            if(in_array($accessControl->type, [AccessControlType::PermissionGroup, AccessControlType::Role])) {
                UserAccessControl::where('access_control_id', $id)->delete();
                $permissions = AccessControl::select('id')->where('group_id', $id)->get();
                if(count($permissions) > 0) {
                    $ids = collect($permissions)->map(function($a){
                        return $a->id;
                    });
                    UserAccessControl::whereIn('access_control_id', $ids)->delete();
                }
                AccessControl::where('group_id', $id)->update(['group_id' => null]);
            }
            return $accessControl;
        }
        return null;
    }

    public static function assignPermissionToPermissionGroup($permissionId, $groupId) {
        if(self::validatePermissionGroup($groupId)->type != AccessControlType::PermissionGroup) return null;
        AccessControl::where([
            'type' => AccessControlType::Permission, 'id' => $permissionId
        ])->update(['group_id' => $groupId]);
        return AccessControl::find($permissionId);
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

    public static function addPermissionsToRole($roleId, $permissions) {
        $role = AccessControl::where(['id'=>$roleId, 'type'=>AccessControlType::Role])->get();
        if($role == null) throw new Exception("Role with id '$roleId' does not exist");
        $perms = AccessControl::select('id')->whereIn('id', $permissions)->get();
        if(count($perms) == 0) throw new Exception('permissions supplied are invalid or empty');
        $permIds = collect($perms)->map(function($p){return $p->id;})->all();
        $existing = RolePermission::select('permission_id')->where('role_id', $roleId)->whereIn('permission_id', $permIds)->get();
        if(count($existing) > 0) {
            $ids = []; //remove existing from insert
            foreach($existing as $p) if(!in_array($p->permission_id, $permIds)) $ids[] = $p->$p->permission_id;
            $permIds = $ids;
        }
        if(count($permIds) > 0)
            RolePermission::insert(collect($permIds)->map(function($p) use($roleId) {
                return ['role_id' => $roleId, 'permission_id'=>$p];
            })->all());
        return AccessControl::whereIn('id', $permIds)->get();
    }

    public static function userHasAccessControl($userId, $accessControlId) {
        $groupEnum = AccessControlType::PermissionGroup;
        $roleEnum = AccessControlType::Role;
        $userIdFormat = self::uuidToString;        
        $table = 
        "select $userIdFormat user_id, p.permission_id, p.group_id, p.role_id
        from user_access_controls a
            left join (
                select pm.permission_id,
                    IF(pm.type=$groupEnum, cast(pm.permission_id as int), pm.group_id) group_id,
                    CASE
                        WHEN g.role_id is not null THEN cast(g.role_id as int)
                        WHEN pm.type=$roleEnum THEN cast(pm.permission_id as int)
                        ELSE r.role_id
                    END role_id
                from (
                    select id permission_id, group_id, type from access_controls
                ) pm
                    left join role_permissions r
                       on pm.permission_id = r.permission_id
                    left join (
                        select group_id, role_id
                        from (
                            select id group_id from access_controls where type=$groupEnum
                        ) grp
                            left join role_permissions r
                               on grp.group_id = r.permission_id
                    ) g
                        on g.group_id = pm.group_id
        ) p
            on a.access_control_id in (p.group_id, p.role_id, p.permission_id)";
        $accessControl = DB::table(DB::raw("($table) permissions"))
            ->select(DB::raw('user_id, permission_id, group_id, role_id'))
            ->where([
                'user_id'=> $userId,
                'permission_id' => $accessControlId,
            ])->get();
        return (empty($accessControl) || !isset($accessControl[0])) ? [] : (array)$accessControl[0];
    }
    
    public static function getUserPermissions($userId) {
      $groupEnum = AccessControlType::PermissionGroup;
      $roleEnum = AccessControlType::Role;
      $userIdFormat = self::uuidToString;  

      $uacTable = 
      "select $userIdFormat user_id, p.permission_id
      from user_access_controls a
        left join (
          select pm.permission_id,
            IF(pm.type = $groupEnum, pm.permission_id, pm.group_id) group_id,
            CASE
              WHEN g.role_id is not null THEN g.role_id
              WHEN pm.type = $roleEnum THEN pm.permission_id
              ELSE r.role_id
            END role_id
          from (
            select id permission_id, group_id, type from access_controls
          ) pm
              left join role_permissions r
                on pm.permission_id=r.permission_id
              left join (
                select group_id, role_id
                from (
                  select id group_id from access_controls where type=$groupEnum
                ) grp
                  left join role_permissions r
                    on grp.group_id = r.permission_id
                ) g
                  on g.group_id = pm.group_id
      ) p
          on a.access_control_id in (p.group_id, p.role_id, p.permission_id)";

      $acTable = DB::table(DB::raw("($uacTable) uac"))
        ->select('user_id', 'permission_id')->whereRaw("user_id='$userId'")
        ->toSql();
      
      $accessControl = AccessControl::selectRaw('id permission_id, IF(ac.user_id is not null, true, false) has_permission')
          ->leftJoin(DB::raw("($acTable) ac"), 'ac.permission_id', '=', 'access_controls.id')
          ->get();
      
      return collect($accessControl)->mapWithKeys(function($p){
        return [$p['permission_id'] => $p['has_permission'] ? true : false];
      })->all();
    }

    public static function getPermissionAllocation() {
      $groupEnum = AccessControlType::PermissionGroup;

      $groupTable = 
      "select group_id, role_id
        from (
              select id group_id from access_controls where type = $groupEnum
        ) grp
              left join role_permissions r2
                on grp.group_id = r2.permission_id";

      return DB::table(DB::raw("(select id permission_id, group_id from access_controls) pm"))
        ->select(
          'pm.permission_id', 'pm.group_id',
          DB::raw('IF(g.role_id is not null, g.role_id, r.role_id) role_id')
        )
        ->leftJoin(DB::raw('role_permissions r'), 'pm.permission_id', 'r.permission_id')
        ->leftJoin(DB::raw("($groupTable) g"), 'g.group_id', 'pm.group_id')
        ->get();
    }
    
    public static function getPermissionGrouping($userId = null) {
      $accessControl = collect(AccessControl::all())->mapWithKeys(function($p){
        return [$p['id'] => $p];
      })->all();
      if(empty($accessControl)) return [];
      $data = [];
      $access = ($userId == null) ? [] : self::getUserPermissions($userId);
      foreach(self::getPermissionAllocation() as $d) {
        $id = $d->permission_id;
        if(AccessControlType::Permission != $accessControl[$id]->type) continue;
        $groupId = ($d->group_id == null) ? 0 : $d->group_id;
        if(!isset($data[$groupId])) {          
          $group = [
            'id'=> $groupId,
            'name'=> ($groupId == 0) ? 'Unassigned' : $accessControl[$groupId]['name'],
            'type'=> AccessControlType::getDescription(AccessControlType::PermissionGroup),
            'roles' => []
          ];
          $data[$groupId] = $group;
        }
        $roleId = ($d->role_id == null) ? 0 : $d->role_id;
        if(!isset($data[$groupId][$roleId])) {          
          $role = [
            'id'=> $roleId,
            'name'=> ($roleId == 0) ? 'Unassigned' : $accessControl[$roleId]['name'],
            'type'=> AccessControlType::getDescription(AccessControlType::Role),
            'permissions' => []
          ];
          $data[$groupId]['roles'][$roleId] = $role;
        }
        $permission = [
          'id'=> $id,
          'name'=> $accessControl[$d->permission_id]['name'],
          'type'=> AccessControlType::getDescription($accessControl[$id]->type),
        ];
        if($userId != null) $permission['allowed'] = $access[$id];
        $data[$groupId]['roles'][$roleId]['permissions'][] = $permission;
      }
      return $data;
    }


    private static function userHasGroupOrPermission($userId, $accessControlId) {
        if(!is_numeric($accessControlId)) 
            throw new Exception("Access control ID supplied is not numeric");
        
        $permissionEnum = AccessControlType::Permission;
        $groupEnum = AccessControlType::PermissionGroup;
        $accessControlTable = (new AccessControl)->getTable();
        $userAccessControlTable = (new UserAccessControl)->getTable(); 
        $userIdFormat = self::uuidToString;

        $accessControlsQuery = 
            "select permissions.id permission_id, `groups`.id group_id " .
            "from (select id, group_id from $accessControlTable where type=$permissionEnum) permissions " .
            "  left join (select id from $accessControlTable where type=$groupEnum) `groups` " .
            "    on permissions.group_id=`groups`.id " .
            "where $accessControlId in (`permissions`.id, `groups`.id)";
        $userQuery = 
            "select ap.user_uuid from (".
                "select $userIdFormat user_uuid, access_control_id from $userAccessControlTable".
            ") ap where user_uuid='$userId' and access_control_id in (permission_id, group_id)";
        
        $accessControl = DB::table(DB::raw("($accessControlsQuery) p"))
            ->select(DB::raw(
                "p.permission_id, p.group_id, ($userQuery) user_id"
            ))->get();
            
        return (empty($accessControl) || !isset($accessControl[0])) ? [] : (array)$accessControl[0];
    }


    private static function userHasRoleOrPermission($userId, $accessControlId) {
        if(!is_numeric($accessControlId)) 
            throw new Exception("Access control ID supplied is not numeric");
        
        $userAccessControlTable = (new UserAccessControl)->getTable();
        $userIdFormat = self::uuidToString;

        $accessControlsQuery = "select permission_id, role_id " .
            "from role_permissions where $accessControlId in (permission_id, role_id)";
        $userQuery = 
            "select ap.user_uuid from (".
                "select $userIdFormat user_uuid, access_control_id from $userAccessControlTable".
            ") ap where user_uuid='$userId' and access_control_id in (permission_id, role_id)";
        
        $accessControl = DB::table(DB::raw("($accessControlsQuery) p"))
            ->select(DB::raw(
                "p.permission_id, p.role_id group_id, ($userQuery) user_id"
            ))->get();
            
        return (empty($accessControl) || !isset($accessControl[0])) ? [] : (array)$accessControl[0];
    }

    
}