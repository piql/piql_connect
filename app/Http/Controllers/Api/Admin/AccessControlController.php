<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AccessControlType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessControlResource;
use App\Http\Resources\UserResource;
use App\AccessControl;
use App\RolePermission;
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
    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/permission-groups",
     *     summary="Creates new Permission Group",
     *     operationId="createPermissionGroup",
     *     @OA\RequestBody(
     *         description="Permission Group data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of permission group",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Descriptive text",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Operation",
     *         @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     ),
     *     @OA\Response( response=400, description="Bad User Data"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
     */
    public function createPermissionGroup(Request $request)
    {
        try {
            $group = AccessControlManager::createPermissionGroup($request->input('name'), $request->input('description'));
            if ($group->save()) return new AccessControlResource($group);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/permission-groups",
     *     summary="Lists Permission Group",
     *     operationId="listPermissionGroup",
     *     @OA\Response(
     *         response=200,
     *         description="Permission Groups",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/permissions",
     *     summary="Lists Permissions",
     *     operationId="listPermissions",
     *     @OA\Response(
     *         response=200,
     *         description="permissions list",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/roles",
     *     summary="Lists Roles",
     *     operationId="listRoles",
     *     description="gets roles",
     *     @OA\Response(
     *         response=200,
     *         description="roles list",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         ),
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/roles/{id}/permissions",
     *     summary="Lists Permissions",
     *     operationId="listRolePermissions",
     *     @OA\Parameter(
     *          name="id",
     *          description="Role ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permissions list",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         ),
     *     )
     * )
     */
    public function listRolePermissions(Request $request, $id)
    {
        try {
            $permissions = RolePermission::select('permission_id')->where('role_id', $id)->get();
            if(empty($permissions)) return AccessControlResource::collection([]);
            $permissionIds = collect($permissions)->map(function($p){
                return $p->permission_id;
            })->all();

            $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE', 10);
            $permissions = AccessControl::whereIn('id', $permissionIds)->paginate($limit, ['*'], 'page');
            return AccessControlResource::collection($permissions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/roles/{id}/permissions ",
     *     summary="Add Permission to Role",
     *     operationId="addPermissionToRole",
     *     @OA\Parameter(
     *          name="id",
     *          description="Role ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         description="Permission  data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of permission",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Descriptive text",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="created permission",
     *          @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     )
     * )
     */
    public function addPermissionsToRole(Request $request, $id)
    {
        try {
            $permissions = AccessControlManager::addPermissionsToRole($id, $request->permissions);
            return AccessControlResource::collection($permissions);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/permission-groups/{id}",
     *     summary="Get Permission Group",
     *     operationId="getPermissionGroup",
     *     @OA\Parameter(
     *          name="id",
     *          description="Group ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permission",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         ),
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/admin/access-control/permission-groups/{id}/permissions",
     *     summary="Lists Permission Group Permissions",
     *     operationId="listPermissionGroupPermissions",
     *     @OA\Parameter(
     *          name="id",
     *          description="Permission Group ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permissions list",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *                property="access_controls",
     *                description="Access Control object",
     *                @OA\Items(ref="#/components/schemas/AccessControl"),
     *                type="array"
     *            )     *          
     *         ),
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/permission-groups/{id}/permissions",
     *     summary="Add Permission to Permission Group",
     *     operationId="addPermissionToPermissionGroup",
     *     @OA\Parameter(
     *          name="id",
     *          description="Permission Group ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         description="Permission data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of permission",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Descriptive text",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permissions list",
     *         @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     )
     * )
     */
    public function createPermissionGroupPermission(Request $request, $id)
    {
        try {
            $action = AccessControlManager::createPermission($id, $request->input('name'), $request->input('description'));
            if ($action->save()) return new AccessControlResource($action);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/users/assign",
     *     summary="Assign Permission to User",
     *     operationId="assignPermissionToUser",
     *     @OA\RequestBody(
     *         description="Assignment Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="users",
     *                     description="List of user IDs",
     *                     @OA\Items(type="string"),
     *                     type="array",
     *                 ),
     *                 @OA\Property(
     *                     property="access_controls",
     *                     description="List of IDs of group/permission/role to assign",
     *                     @OA\Items(type="integer"),
     *                     type="array"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permission",
     *         @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/users/unassign",
     *     summary="Unassign Permission to User",
     *     operationId="unassignPermissionToUser",
     *     @OA\RequestBody(
     *         description="Assignment Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="users",
     *                     description="List of user IDs",
     *                     @OA\Items(type="string"),
     *                     type="array",
     *                 ),
     *                 @OA\Property(
     *                     property="access_controls",
     *                     description="List of IDs of group/permission/role to unassign",
     *                     @OA\Items(type="integer"),
     *                     type="array"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="permission",
     *         @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/admin/access-control/users/has-access-control",
     *     summary="Check if user has permission",
     *     operationId="assignPermissionToUser",
     *     @OA\RequestBody(
     *         description="Assignment Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     description="user ID",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="access_control",
     *                     description="ID of group/permission/role",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="assignment response",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property( property="user_id", description="UserID", type="string"),          
     *           @OA\Property( property="permission_id", description="PermissionID", type="integer"),          
     *           @OA\Property( property="group_id", description="groupID", type="integer"),          
     *           @OA\Property( property="role_id", description="roleID", type="integer"),          
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/AccessControl")
     *     )
     * )
     */
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
    
    public function userPermissions(Request $request, $id)
    {
        try {
            return AccessControlManager::getUserPermissions($id);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    
    
    }public function userAccess(Request $request, $id)
    {
        try {
            return AccessControlManager::getPermissionGrouping($id);
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
