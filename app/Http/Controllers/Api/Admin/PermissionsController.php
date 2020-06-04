<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\PermissionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Permission;
use App\Services\PermissionManager;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
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
        $limit = $request->limit ? $request->limit : 10;
        $groups = Permission::where('type', PermissionType::Group)->paginate($limit, ['*'], 'page');
        return PermissionResource::collection($groups);
    }

    public function getGroup($id)
    {
        $group = Permission::where('type', PermissionType::Group)
            ->where('id', $id)->first();
        if($group == null) response(['message' => 'Group Not Found!'], 404);
        $group->actions = Permission::select('id', 'name')->where('parent_id', $id)->get();
        return new PermissionResource($group);
    }
    
    public function listGroupActions(Request $request, $id)
    {
        $limit = $request->limit ? $request->limit : 10;
        $actions = Permission::where('type', PermissionType::Action)->where('parent_id', $id)->paginate($limit, ['*'], 'page');
        return PermissionResource::collection($actions);
    }

    public function createAction(Request $request, $id)
    {
        $action = PermissionManager::createAction($id, $request->input('name'), $request->input('description'));
        if ($action->save()) return new PermissionResource($action);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $permission = $request->isMethod('put') ? Permission::findOrFail($request->id) : new Permission;
    //     $permission->type = $request->input('type');
    //     $permission->name = $request->input('name');
    //     $permission->description = $request->input('description');
    //     $permission->parent_id = $request->input('parent_id');
    //     if ($permission->save()) return new PermissionResource($permission);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return new PermissionResource($permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $permission = Permission::findOrFail($id);
        if ($permission->delete()) return new PermissionResource($permission);
    }
}
