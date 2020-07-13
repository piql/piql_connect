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
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $accessControls = AccessControl::paginate($limit, ['*'], 'page');
        return AccessControlResource::collection($accessControls);
    }

    public function createPermissionGroup(Request $request)
    {
        $group = AccessControlManager::createPermissionGroup($request->input('name'), $request->input('description'));
        if ($group->save()) return new AccessControlResource($group);
    }

    public function listGroups(Request $request)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $groups = AccessControl::where('type', AccessControlType::PermissionGroup)->paginate($limit, ['*'], 'page');
        return AccessControlResource::collection($groups);
    }

    public function listPermissions(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $roles = AccessControl::where('type', AccessControlType::Permission)->paginate($limit, ['*'], 'page');
        return AccessControlResource::collection($roles);
    }

    public function getPermissionGroup($id)
    {
        $group = AccessControl::where('type', AccessControlType::PermissionGroup)->where('id', $id)->first();
        if($group == null) return response(['message' => 'Group Not Found!'], 404);
        $group->actions = AccessControl::select('id', 'name')->where('parent_id', $id)->get();
        return new AccessControlResource($group);
    }
    
    public function listPermissionGroupPermissions(Request $request, $id)
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $actions = AccessControl::where(['type' => AccessControlType::Permission, 'parent_id' =>$id])->paginate($limit, ['*'], 'page');
        return AccessControlResource::collection($actions);
    } 
    
    public function createPermission(Request $request)
    {
        $action = AccessControlManager::createAction(null, $request->input('name'), $request->input('description'));
        if ($action->save()) return new AccessControlResource($action);
    } 

    public function createPermissionGroupPermission(Request $request, $id)
    {
        $action = AccessControlManager::createAction($id, $request->input('name'), $request->input('description'));
        if ($action->save()) return new AccessControlResource($action);
    } 
    
    public function assignUsers(Request $request)
    {
        $data = AccessControlManager::assignAccessControlsToUsers($request->access_controls, $request->users);
        if(array_key_exists("error", $data)) return response($data, 400);
        return $data;
    }   
    
    
    public function unAssignUsers(Request $request)
    {
        $data = AccessControlManager::removeAccessControlsFromUsers($request->access_controls, $request->users);
        if(array_key_exists("error", $data)) return response($data, 400);
        return $data;
    }

    public function userHasAccessControl(Request $request)
    {
        return AccessControlManager::userHasAccessControl(
            $request->user, $request->AccessControl
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
            $AccessControl = AccessControl::find($id);
            return ($AccessControl != null) ? new AccessControlResource($AccessControl) : response([
                'message' => 'AccessControl Not Found!'
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
        $AccessControl =  AccessControl::findOrFail($id);
        $AccessControl->name = $request->input('name');
        $AccessControl->description = $request->input('description');
        if ($AccessControl->save()) return new AccessControlResource($AccessControl);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $AccessControl = AccessControlManager::delete($id);
        return new AccessControlResource($AccessControl);
    }

    /**
     * Display a listing of users assigned the AccessControl
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
