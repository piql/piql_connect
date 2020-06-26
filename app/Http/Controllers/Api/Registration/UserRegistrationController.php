<?php

namespace App\Http\Controllers\Api\Registration;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserRegistrationService;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserRegistrationController extends Controller
{
    /**
     * Creates a user from the information supplied
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|min:3',
            'email'    => 'required|unique:users|email',
            'name'     => 'required|min:5',
        ]);
        if ($validator->fails()) return response([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 400);
        try {
            UserRegistrationService::registerUser(
                $request->name,
                $request->username,
                $request->email,
                $request->getSchemeAndHttpHost()
            );
            return response(['message' => __('Check your email to verify registration')], 201);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Validates user confirmation information.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:6|
               regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
        ]);
        if ($validator->fails()) return response([
            'message' => 'Invalid Registration Parameters',
            'errors' => $validator->errors(),
        ], 400);
        try {
            $user = UserRegistrationService::confirmUser($request->token, $request->password);
            if($user == null) return response(['message' => __('Invaild or expired token')], 400);
            return response(['message' => __('Account confirmed')], 200);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
