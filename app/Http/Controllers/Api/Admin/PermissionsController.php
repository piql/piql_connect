<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\PermissionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Permission;
use App\Services\PermissionManager;
use App\User;
use Throwable;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $permissions = Permission::paginate($limit, ['*'], 'page');
        return PermissionResource::collection($permissions);
    }

    public function createGroup(Request $request)
    {
        $group = PermissionManager::createGroup($request->input('name'), $request->input('description'));
        if ($group->save()) return new PermissionResource($group);
    }

    public function listGroups(Request $request)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $groups = Permission::where('type', PermissionType::Group)->paginate($limit, ['*'], 'page');
        return PermissionResource::collection($groups);
    }

    public function listRoles(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $roles = Permission::where('type', PermissionType::Role)->paginate($limit, ['*'], 'page');
        return PermissionResource::collection($roles);
    }

    public function getGroup($id)
    {
        $group = Permission::where('type', PermissionType::Group)->where('id', $id)->first();
        if($group == null) return response(['message' => 'Group Not Found!'], 404);
        $group->actions = Permission::select('id', 'name')->where('parent_id', $id)->get();
        return new PermissionResource($group);
    }
    
    public function listGroupActions(Request $request, $id)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $actions = Permission::where(['type' => PermissionType::Role, 'parent_id' =>$id])->paginate($limit, ['*'], 'page');
        return PermissionResource::collection($actions);
    } 
    
    public function createAction(Request $request)
    {
        $action = PermissionManager::createAction(null, $request->input('name'), $request->input('description'));
        if ($action->save()) return new PermissionResource($action);
    } 

    public function createGroupAction(Request $request, $id)
    {
        $action = PermissionManager::createAction($id, $request->input('name'), $request->input('description'));
        if ($action->save()) return new PermissionResource($action);
    } 
    
    public function assignUsers(Request $request)
    {
        $data = PermissionManager::assignPermissionsToUsers($request->permissions, $request->users);
        if(array_key_exists("error", $data)) return response($data, 400);
        return $data;
    }   
    
    
    public function unAssignUsers(Request $request)
    {
        $data = PermissionManager::removePermissionsFromUsers($request->permissions, $request->users);
        if(array_key_exists("error", $data)) return response($data, 400);
        return $data;
    }

    public function userHasPermission(Request $request)
    {
        return PermissionManager::userHasPermission(
            $request->user, $request->permission
        );
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
            $permission = Permission::find($id);
            return ($permission != null) ? new PermissionResource($permission) : response([
                'message' => 'Permission Not Found!'
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
        $permission =  Permission::findOrFail($id);
        $permission->name = $request->input('name');
        $permission->description = $request->input('description');
        if ($permission->save()) return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = PermissionManager::delete($id);
        return new PermissionResource($permission);
    }

    /**
     * Display a listing of users assigned the permission
     *
     * @return \Illuminate\Http\Response
     */
    public function users($id, Request $request)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $users = User::rightJoin('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->where('user_permissions.permission_id', $id)
            ->paginate($limit, ['*'], 'page');
        return UserResource::collection($users);
    }
}
