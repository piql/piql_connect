<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Auth\User;

class KeycloakTestUserProvider implements UserProvider
{
    public function __construct()
    {
    }

    public function retrieveById($identifier)
    {
        return new User($this->getUser());
    }

    public function retrieveByToken($identifier, $token)
    {
        return new User($this->getUser());
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        return new User($this->getUser());
    }

    public function retrieveByCredentials(array $credentials)
    {
        return new User($this->getUser());
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return new User($this->getUser());
    }

    private function getUser()
    {
        $user = json_decode(file_get_contents(resource_path('data/keycloak/users/token.json')));
        return (array) $user;
    }
}
