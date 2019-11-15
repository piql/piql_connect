<?php

namespace App\Services;

class UserSettingsService implements \App\Interfaces\SettingsInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function all()
    {
        return \Auth::user()->settings;
    }
}
