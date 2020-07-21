<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Log;

class SelfServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;

        // Validate new password
        if (strlen($newPassword) < 8) {
            return response()->json([ 'status' => 1, 'message' => 'The password must be at least 6 characters' ], 200 );
        }

        // Match old password
        if (!Hash::check($oldPassword, $user->password)) {
            return response()->json([ 'status' => 2, 'message' => 'The old password was wrong' ], 200 );
        }

        // Save new password
        $user->password = Hash::make($newPassword);
        $user->save();

        // todo: avoid being logged out

        return response()->json([ 'status' => 0, 'message' => 'Password was updated successfully' ], 200 );
    }

    public function me(Request $request) {
        $user = Auth::user();
        $userSess = array();
        $userSess['full_name'] = $user->full_name;
        $userSess['email'] = $user->email;
        $nameArr = explode(' ', $user->full_name);
        $userSess['first_name'] = $nameArr[0];
        $file = 'custom-files/user/'.$user->id.'.png';
        if (!file_exists(storage_path($file))) {
            $file = 'custom-files/user/avatar.png';
        }
        $userSess['img'] = base64_encode(Storage::disk('local')->get($file));
        return response()->json($userSess, 200);
    }

}
