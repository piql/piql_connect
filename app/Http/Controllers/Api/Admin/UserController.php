<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            $users = User::select('id')->whereIn('id', $request->users)->get();
            if (count($users) == 0)
                return response(['message' => 'Parameters do not match any user'], 404);
            $data = [];
            foreach ($users as $u)
                $data[] = ['user_id' => $u, 'disabled_on' => Carbon::now()->toDateTimeString()];
            DB::table('user_permissions')->insert($data);
            return response(['users' => $users, 'disabled' => true], 200);
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
            $users = User::select('id')->whereIn('id', $request->users)->get();
            if (count($users) == 0)
                return response(['message' => 'Parameters do not match any user'], 404);
            $data = [];
            foreach ($users as $u)
                $data[] = ['user_id' => $u, 'disabled_on' => null];
            DB::table('user_permissions')->insert($data);
            return response(['users' => $users, 'enabled' => true], 200);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
