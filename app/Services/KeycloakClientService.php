<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;

class KeycloakClientService implements KeycloakClientInterface
{
    private $client;

    public function __construct()
    {
        $this->client = \Keycloak\Admin\KeycloakClient::factory([
            'realm'=>env('APP_AUTH_SERVICE_REALM'),
            'username'=>env('APP_AUTH_SERVICE_USERNAME'),
            'password'=>env('APP_AUTH_SERVICE_PASSWORD'),
            'client_id'=>env('APP_AUTH_SERVICE_CLIENT_ID'),
            'baseUri'=>env('APP_AUTH_SERVICE_BASE_URL')
        ]);
    }

    public function getUsers()
    {
        return $this->client->getUsers();
    }
}
