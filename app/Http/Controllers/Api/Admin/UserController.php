<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
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
            $users = User::paginate($limit, ['*'], 'page');
            return UserResource::collection($users);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
            return ($user != null) ? new UserResource($user) : response([
                'message' => 'User Not Found!'
            ], 404);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|filled'
        ]);
        if ($validator->fails()) return response([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
        try {
            $data = ['users' => [], 'disabled' => true];
            foreach ($request->users as $id){
                $u = User::find($id);
                if($u == null) continue; 
                $u->disabled_on = Carbon::now()->toDateTimeString();
                if($u->save()) $data['users'][] = $u;
            }
            return response($data, 200);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|filled'
        ]);
        if ($validator->fails()) return response([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
        try {
            $data = ['users' => [], 'enabled' => true];
            foreach ($request->users as $id){
                $u = User::find($id);
                if($u == null) continue; 
                $u->disabled_on = null;
                if($u->save()) $data['users'][] = $u;
            }
            return response($data, 200);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
