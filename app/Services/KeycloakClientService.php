<?php

namespace App\Services;

use App\Auth\Group;
use App\Interfaces\KeycloakClientInterface;
use App\User;
use Exception;

class KeycloakClientService implements KeycloakClientInterface
{
    /**
     * @param \Keycloak\Admin\KeycloakClient
     */
    private $client;

    public function __construct(Object $keycloakClient = null)
    {
        $this->client = $keycloakClient;
        if ($this->client === null) {
            $this->client = \Keycloak\Admin\KeycloakClient::factory([
                'realm' => env('APP_AUTH_SERVICE_REALM'),
                'username' => env('APP_AUTH_SERVICE_USERNAME'),
                'password' => env('APP_AUTH_SERVICE_PASSWORD'),
                'client_id' => env('APP_AUTH_SERVICE_CLIENT_ID'),
                'baseUri' => env('APP_AUTH_SERVICE_BASE_URL')
            ]);
        }
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

    public function getUserById($id)
    {
        return $this->client->getUser(['id' => $id]);
    }

    public function searchOrganizationUsers($orgId, $params = [], $limit = 20, $offset = 0)
    {
        if ($orgId == null || count($orgId) == 0) throw new Exception("organisation id is required for this operation");
        $params = collect($params)->only(['email', 'firstName', 'lastName', 'username', 'search'])->all();
        $params = array_merge($params, ['first' => 0, 'max' => $limit]);
        return $this->client->getUsers($params);
    }

    public function getRoles(): array
    {
        $res = $this->client->getRealmRoles();
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function getRoleByName($name): array
    {
        $res = $this->client->getRealmRole(['role-name' => $name]);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function getRoleUsers($name): array
    {
        $res = $this->client->getRealmRoleUsers(['role-name' => $name]);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function createGroup(Group $group): array
    {
        $res = $this->client->createGroup([
            'name' => $group->name,
            'attributes' => $group->attributes,
        ]);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function getGroups($q='', $limit = 20, $offset = 0): array
    {
        $params = ['search'=>$q, 'max'=>$limit];
        if($offset > 0) $params['first'] = $offset;
        $res = $this->client->getGroups($params);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function getGroupUsers($limit = 20, $offset = 0): array
    {
        $params = ['max'=>$limit];
        if($offset > 0) $params['first'] = $offset;
        $res = $this->client->getGroupMembers($params);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function getUserGroups($q='', $limit = 20, $offset = 0): array
    {
        $params = ['search'=>$q, 'max'=>$limit];
        if($offset > 0) $params['first'] = $offset;
        $res = $this->client->getUserGroups($params);
        $this->validateKeycloakResponse($res);
        return $res;
    }

    public function addUserToGroup($userId, $groupId): array
    {
        //todo: implement with custom client
        return [];
    }

    public function addRoleToGroup($roleId, $groupId): array
    {
        //todo: implement with custom client
        return [];
    }

    public function getGroupRoles($limit = 20, $offset = 0): array
    {
        $params = ['max'=>$limit];
        if($offset > 0) $params['first'] = $offset;
        $res = $this->client->getGroupRoleMappings($params);
        $this->validateKeycloakResponse($res);
        return $res;
    }
}
