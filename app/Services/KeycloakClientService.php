<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;
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
        $this->realm = isset($params['realm']) ? $params['realm'] : env('APP_AUTH_SERVICE_REALM');
        $this->username = isset($params['username']) ? $params['username'] : env('APP_AUTH_SERVICE_USERNAME');
        $this->password = isset($params['password']) ? $params['password'] : env('APP_AUTH_SERVICE_PASSWORD');
        $this->clientId = isset($params['client_id']) ? $params['client_id'] : env('APP_AUTH_SERVICE_CLIENT_ID');
        $this->baseUri = isset($params['baseUri']) ? $params['baseUri'] : env('APP_AUTH_SERVICE_BASE_URL');
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
        $this->client = \Keycloak\Admin\KeycloakClient::factory($this->params());
    }

    private function initGuzzleClient()
    {
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
            return $consumer($this->getGuzzleClient(), $this->gzClientCreds->access_token);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUsers()
    {
        return $this->client->getUsers();
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
