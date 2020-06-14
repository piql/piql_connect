<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            $limit = $request->limit ? $request->limit : 10;
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
            $uid = hex2bin(str_replace('-', '', $id));
            $user = User::where('id', $uid)->first();
            return ($user != null) ? new UserResource($user) : response([
                'message' => 'User Not Found!'
            ], 404);
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
    public function disable(Request $request)
    {
        $params = $request->users;
        if (!is_array($params) || count($params) == 0)
            return response(['message' => 'An array of user ids is required'], 400);
        try {
            $users = User::select('id')->whereIn('id', $params)->get();
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
        $params = $request->users;
        if (!is_array($params) || count($params) == 0)
            return response(['message' => 'An array of user ids is required'], 400);
        try {
            $users = User::select('id')->whereIn('id', $params)->get();
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
