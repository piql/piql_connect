<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AccessControlType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessControlResource;
use App\Http\Resources\UserResource;
use App\AccessControl;
use App\Services\AccessControlManager;
use App\User;
use Throwable;

class AccessControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $accessControls = AccessControl::paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($accessControls);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermissionGroup(Request $request)
    {
        try {
            $group = AccessControlManager::createPermissionGroup($request->input('name'), $request->input('description'));
            if ($group->save()) return new AccessControlResource($group);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissionGroups(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $groups = AccessControl::where('type', AccessControlType::PermissionGroup)->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($groups);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissions(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : 10;
            $roles = AccessControl::where('type', AccessControlType::Permission)->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($roles);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listRoles(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : 10;
            $roles = AccessControl::where('type', AccessControlType::Role)->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($roles);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listRolePermissions(Request $request, $id)
    {
        try {
            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $permissions = AccessControl::where('type', AccessControlType::Permission)
                ->join('role_permissions', function ($join) {
                    $join->on('access_controls.id', '=', 'role_permissions.role_id');
                })->where('role_permissions.id', $id)
                ->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($permissions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function addPermissionsToRole(Request $request, $id)
    {
        try {
            $permissions = AccessControlManager::addPermissionsToRole($id, $request->permissions);
            return AccessControlResource::collection($permissions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function getPermissionGroup($id)
    {
        try {
            $group = AccessControl::where('type', AccessControlType::PermissionGroup)->where('id', $id)->first();
            if ($group == null) return response(['message' => 'Group Not Found!'], 404);
            $group->actions = AccessControl::select('id', 'name')->where('group_id', $id)->get();
            return new AccessControlResource($group);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissionGroupPermissions(Request $request, $id)
    {
        try {
            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $actions = AccessControl::where(['type' => AccessControlType::Permission, 'group_id' => $id])->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($actions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermission(Request $request)
    {
        try {
            $action = AccessControlManager::createPermission(null, $request->input('name'), $request->input('description'));
            if ($action->save()) return new AccessControlResource($action);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createRole(Request $request)
    {
        try {
            $action = AccessControlManager::createRole($request->input('name'), $request->input('description'));
            if ($action->save()) return new AccessControlResource($action);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermissionGroupPermission(Request $request, $id)
    {
        try {
            $action = AccessControlManager::createPermission($id, $request->input('name'), $request->input('description'));
            if ($action->save()) return new AccessControlResource($action);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function assignUsers(Request $request)
    {
        try {
            $data = AccessControlManager::assignAccessControlsToUsers($request->access_controls, $request->users);
            if (array_key_exists("error", $data)) return response($data, 400);
            return $data;
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }


    public function unAssignUsers(Request $request)
    {
        try {
            $data = AccessControlManager::removeAccessControlsFromUsers($request->access_controls, $request->users);
            if (array_key_exists("error", $data)) return response($data, 400);
            return $data;
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function userHasAccessControl(Request $request)
    {
        try {
            return AccessControlManager::userHasAccessControl(
                $request->user,
                $request->access_control
            );
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $accessControl = AccessControl::find($id);
            return ($accessControl != null) ? new AccessControlResource($accessControl) : response([
                'message' => 'accessControl Not Found!'
            ], 404);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $accessControl =  AccessControl::findOrFail($id);
        $accessControl->name = $request->input('name');
        $accessControl->description = $request->input('description');
        if ($accessControl->save()) return new AccessControlResource($accessControl);
    }

    public function updateGroup(Request $request, $id, $groupId)
    {
        try {
            $accessControl = AccessControlManager::assignPermissionToPermissionGroup($id, $groupId);
            return new AccessControlResource($accessControl);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $accessControl = AccessControlManager::delete($id);
        return new AccessControlResource($accessControl);
    }

    /**
     * Display a listing of users assigned the accessControl
     *
     * @return \Illuminate\Http\Response
     */
    public function users($id, Request $request)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $users = User::rightJoin('user_access_controls', 'users.id', '=', 'user_access_controls.user_id')
            ->where('user_access_controls.access_control_id', $id)
            ->paginate($limit, ['*'], 'page');
        return UserResource::collection($users);
    }
}
