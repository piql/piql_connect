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

    public function getUsers() : array
    {
        $response = $this->client->getUsers();

        if (!is_array($response) || (count($response) != 0 && !isset($response[0]['id']))) {
            $message = 'Keycloak: ' . 'Keycloak: Failed to get users';
            throw new \Exception($message);
        }

        return $response;
    }

    public function createUser(string $organizationId, User $user) : User
    {
        $firstName = $this->firstName($user->full_name);
        $lastName = $this->lastName($user->full_name);

        // Add user to keycloak server
        $response = $this->client->createUser([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'enabled' => true,
            'attributes' => ['organization' => $organizationId]
        ]);
        $this->validateKeycloakResponse($response);

        // Get user id from keycloak
        $user->id = $this->userId($user->username);

        return $user;
    }

    public function editUser(string $organizationId, User $user) : User
    {
        $firstName = $this->firstName($user->full_name);
        $lastName = $this->lastName($user->full_name);

        $response = $this->client->updateUser([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'attributes' => ['organization' => $organizationId]
        ]);
        $this->validateKeycloakResponse($response);

        return $user;
    }

    public function deleteUser(string $userId) : void
    {
        $response = $this->client->deleteUser([
            'id' => $userId
        ]);
        $this->validateKeycloakResponse($response);
    }

    private function firstName($fullName) : string
    {
        $names = explode(' ', $fullName);
        return count($names) > 0 ? $names[0] : '';
    }

    private function lastName($fullName) : string
    {
        // Use all names except for the first as lastname
        $names = explode(' ', $fullName);
        return count($names) > 1 ? implode(' ', array_slice($names, 1)) : '';
    }

    private function userId($username) : string
    {
        $users = $this->getUsers($username);
        foreach ($users as $user) {
            if ($user['username'] == $username) {
                return $user['id'];
            }
        }

        throw new \Exception('Keycloak: User with username ' . $username . ' was not found');
    }

    private function validateKeycloakResponse($response) : void
    {
        if (!isset($response['content']) || isset($response['errorMessage'])) {
            $message = isset($response['errorMessage']) ? 'Keycloak: ' . $response['errorMessage'] : 'Keycloak: Unknown error';
            throw new \Exception($message);
        }
    }
}
