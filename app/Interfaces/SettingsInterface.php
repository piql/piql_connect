<?php

namespace App\Interfaces;

interface SettingsInterface
{
    public function forAuthUser();
    public function forUser( \App\User $user );
    public function forUserId( string $userId );
}
