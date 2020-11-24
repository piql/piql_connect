<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;
use App\User;
use Exception;

class KeycloakClientService implements KeycloakClientInterface
{
    private $client;
    private $gzClient;
    private $gzClientCreds;

    private $realm;
    private $username;
    private $password;
    private $clientId;
    private $baseUri;

    private function init($params = [])
    {
        $p = collect($params)->only(['realm','username','password','client_id','baseUri', ])->all();
        $this->realm = isset($p['realm']) ? $p['realm'] : env('APP_AUTH_SERVICE_REALM');
        $this->username = isset($p['username']) ? $p['username'] : env('APP_AUTH_SERVICE_USERNAME');
        $this->password = isset($p['password']) ? $p['password'] : env('APP_AUTH_SERVICE_PASSWORD');
        $this->clientId = isset($p['client_id']) ? $p['client_id'] : env('APP_AUTH_SERVICE_CLIENT_ID');
        $this->baseUri = isset($p['baseUri']) ? $p['baseUri'] : env('APP_AUTH_SERVICE_BASE_URL');
    }

    private function params($keys=[])
    {
        $p = [
            'realm' => $this->realm,
            'username' => $this->username,
            'password' => $this->password,
            'client_id' => $this->clientId,
            'baseUri' => $this->baseUri,
        ];
        if(empty($keys)) return $p;
        return collect($p)->only($keys)->all();
    }

    public function __construct($params = [])
    {
        $this->init($params);
        if(isset($params['client'])) $this->client = $params['client'];
        else $this->client = \Keycloak\Admin\KeycloakClient::factory($this->params());
        if(isset($params['gzClient'])) $this->gzClient = $params['gzClient'];
    }

    private function initGuzzleClient()
    {
        if($this->gzClient != null) return;
        $this->gzClient = new \GuzzleHttp\Client(['base_uri' => $this->baseUri]);
        //todo: consider caching credentials locally for refresh before logging in afresh
        $params = $this->params(['username', 'password', 'client_id']);
        $res = $this->gzClient->post("/auth/realms/$this->realm/protocol/openid-connect/token", [
            'headers' =>  ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => array_merge($params, ['grant_type' => 'password'])
        ]);
        $this->gzClientCreds = json_decode($res->getBody()->getContents());
    }

    private function getGuzzleClient()
    {
        if ($this->gzClient == null)
            $this->initGuzzleClient();
        return $this->gzClient;
    }

    private function customRequest(callable $consumer) {
        try {
            $res = $consumer($this->getGuzzleClient(), $this->gzClientCreds->access_token);
            $this->validateKeycloakResponse($res);
            return $res;
        } catch (Exception $e) {
            throw $e;
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
            'attributes' => ['organization' => $organizationId],
            'requiredActions' => ['UPDATE_PASSWORD']
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
        if($orgId == null || count($orgId) == 0) throw new Exception("organisation id is required for this operation");
        $params = collect($params)->only(['email', 'firstName', 'lastName', 'username', 'search'])->all();
        $users = $this->client->getUsers($params);
        return collect($users)->filter(function ($u) use ($orgId) {
            return isset($u['attributes']['organization'])
                && collect($u['attributes']['organization'])->contains($orgId);
        })->slice($offset, $limit, true);
    }

    public function changePassword($id, $password)
    {
        return $this->customRequest(function(\GuzzleHttp\Client $client, string $token) use ($id, $password) {
            $res = $client->put("/auth/admin/realms/$this->realm/users/$id/reset-password", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json'
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'type' => 'password',
                    'value' => $password,
                    'temporary' => true
                ]
            ]);
            return json_decode($res->getBody()->getContents());
        });
    }

    public function logoutUser($id)
    {
        return $this->customRequest(function(\GuzzleHttp\Client $client, string $token) use ($id) {
            $res = $client->post("/auth/admin/realms/$this->realm/users/$id/logout", [
                'headers' => ['Authorization' => "Bearer $token"],
            ]);
            return json_decode($res->getBody()->getContents());
        });
    }

    public function blockUser($id)
    {
        return $this->client->updateUser(['id' => $id, 'enabled' => false]);
    }

    public function unblockUser($id)
    {
        return $this->client->updateUser(['id' => $id, 'enabled' => true]);
    }
}
