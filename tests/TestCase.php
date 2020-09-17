<?php

namespace Tests;

use App\Providers\KeycloakTestUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        app()->bind(UserProvider::class, function() { 
            return new KeycloakTestUserProvider();
        });
    }
}
