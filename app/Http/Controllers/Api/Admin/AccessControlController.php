<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AccessControlType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessControlResource;
use App\Http\Resources\UserResource;
use App\accessControl;
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
            $accessControls = accessControl::paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($accessControls);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermissionGroup(Request $request)
    {
       try { $group = AccessControlManager::createPermissionGroup($request->input('name'), $request->input('description'));
        if ($group->save()) return new AccessControlResource($group);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissionGroups(Request $request)
    {
        try {$limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
        $groups = accessControl::where('type', AccessControlType::PermissionGroup)->paginate($limit, ['*'], 'page');
        return AccessControlResource::collection($groups);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissions(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : 10;
            $roles = accessControl::where('type', AccessControlType::Permission)->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($roles);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function getPermissionGroup($id)
    {
        try {
            $group = accessControl::where('type', AccessControlType::PermissionGroup)->where('id', $id)->first();
            if ($group == null) return response(['message' => 'Group Not Found!'], 404);
            $group->actions = accessControl::select('id', 'name')->where('parent_id', $id)->get();
            return new AccessControlResource($group);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function listPermissionGroupPermissions(Request $request, $id)
    {
        try {
            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $actions = accessControl::where(['type' => AccessControlType::Permission, 'parent_id' => $id])->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($actions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermission(Request $request)
    {
        try {
            $action = AccessControlManager::createAction(null, $request->input('name'), $request->input('description'));
            if ($action->save()) return new AccessControlResource($action);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function createPermissionGroupPermission(Request $request, $id)
    {
        try {
            $action = AccessControlManager::createAction($id, $request->input('name'), $request->input('description'));
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
            $accessControl = accessControl::find($id);
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
        $accessControl =  accessControl::findOrFail($id);
        $accessControl->name = $request->input('name');
        $accessControl->description = $request->input('description');
        if ($accessControl->save()) return new AccessControlResource($accessControl);
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
