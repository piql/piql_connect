<?php

namespace App\Services;

class UserSettingsService implements \App\Interfaces\SettingsInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function forAuthUser()
    {
        if( ! \Auth::check() ) {
            throw new \Exception("UserSettingsService: No user has been authenticated. Did you mean to call 'forUser' or 'forUserId' instead?");
        }
        return \Auth::user()->settings;
    }

    public function forUser( \App\User $user )
    {
        return $user->settings;
    }

    public function forUserId( string $userId )
    {
        $user = \User::find( $userId );
        if( $user == null ) {
            throw new \Exception("UserSettingsService: No user with id {$userId} was found.");
        }
        return $user->settings;
    }
}
