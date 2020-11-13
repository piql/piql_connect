<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;
use Exception;

class KeycloakClientService implements KeycloakClientInterface
{
    private $client;

    public function __construct()
    {
        $this->client = \Keycloak\Admin\KeycloakClient::factory([
            'realm' => env('APP_AUTH_SERVICE_REALM'),
            'username' => env('APP_AUTH_SERVICE_USERNAME'),
            'password' => env('APP_AUTH_SERVICE_PASSWORD'),
            'client_id' => env('APP_AUTH_SERVICE_CLIENT_ID'),
            'baseUri' => env('APP_AUTH_SERVICE_BASE_URL')
        ]);
    }

    public function getUsers()
    {
        return $this->client->getUsers();
    }

    public function getUserById($id)
    {
        return $this->client->getUser(['id' => $id]);
    }

    public function searchOrganizationUsers($orgId, $params = [], $limit = 20, $offset = 0)
    {
        if($orgId == null || count($orgId) == 0) throw new Exception("organisation id is required for this operation");
        $params = collect($params)->only(['email', 'firstName', 'lastName', 'username', 'search'])->all();
        $users = $this->client->getUsers($params);
        return collect($users)->filter(function ($u) use ($orgId) {
            return isset($u['attributes']['organization']) 
                && collect($u['attributes']['organization'])->contains($orgId);
        })->slice($offset, $limit, true);
    }
}
