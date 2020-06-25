<?php

namespace App\Services;

use App\Mail\ConfirmUserRegistration;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class UserRegistrationService
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public static function registerUser($name, $username, $email, $host){
        $user = User::where('email', $email)->orWhere('username', $username)->first();
        if($user != null){
            if($email == $user->email) throw new Exception('User with this email already exists');
            if($username == $user->username) throw new Exception('User with this username already exists');
        }            
        $user = new User;
        $user->full_name = $name;
        $user->username = $username;
        $user->email = $email;
        $user->confirmation_token = encrypt(time().$email);
        $user->password = encrypt(time().$user->email);        
        if(!$user->save()) 
            throw new Exception('Failed to create user');
        Mail::to($user->email)->send(new ConfirmUserRegistration($user, $host));
        return $user;
    }
    
    public static function confirmUser($token, $password){
        $user = User::where('confirmation_token', $token)->first();
        if($user == null) return null;
        $user->password = encrypt($password);
        $user->confirmation_token = null;
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        if(!$user->save()) 
            throw new Exception('Failed to confirm user');
        return $user;
    }
}
