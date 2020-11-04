<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;
use App\User;

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

    public function createUser(string $accountId, User $user) : User
    {
        $firstName = $this->firstName($user->full_name);
        $lastName = $this->lastName($user->full_name);

        // Add user to keycloak server
        $res = $this->client->createUser([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'enabled' => true,
            'attributes' => ['organization' => $accountId]
        ]);

        if (!isset($res['content'])) {
            $message = isset($res['errorMessage']) ? 'Keycloak: ' . $res['errorMessage'] : 'Keycloak: Unknown error';
            throw new \Exception($message);
        }

        return $user;
    }

    public function editUser(string $userId, User $user) : User
    {
        $firstName = $this->firstName($user->full_name);
        $lastName = $this->lastName($user->full_name);

        $res = $this->client->updateUser([
            'id' => $userId,
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'attributes' => ['organization' => $user->account_uuid]
        ]);

        if (!isset($res['content'])) {
            $message = isset($res['errorMessage']) ? 'Keycloak: ' . $res['errorMessage'] : 'Keycloak: Unknown error';
            throw new \Exception($message);
        }

        return $user;
    }

    public function deleteUser(string $userId) : void
    {
        $res = $this->client->deleteUser([
            'id' => $userId
        ]);

        if (!isset($res['content'])) {
            $message = isset($res['errorMessage']) ? 'Keycloak: ' . $res['errorMessage'] : 'Keycloak: Unknown error';
            throw new \Exception($message);
        }
    }

    private function firstName($fullName) {
        $names = explode(' ', $fullName);
        return count($names) > 0 ? $names[0] : '';
    }

    private function lastName($fullName) {
        // Use all names except for the first as lastname
        $names = explode(' ', $fullName);
        return count($names) > 1 ? implode(' ', array_slice($names, 1)) : '';
    }
}
