<?php

namespace App\Services;

use App\Interfaces\KeycloakClientInterface;
use Exception;

class KeycloakClientService implements KeycloakClientInterface
{
    private $client;
    private $gzClient;
    private $gzClientCreds;

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

    private function initGuzzleClient()
    {
        $realm = env('APP_AUTH_SERVICE_REALM');
        $this->gzClient = new \GuzzleHttp\Client(['base_uri' => env('APP_AUTH_SERVICE_BASE_URL')]);
        //todo: consider caching credentials locally for refresh before logging in afresh
        $res = $this->gzClient->post("/auth/realms/$realm/protocol/openid-connect/token", [
            'headers' =>  ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'username' => env('APP_AUTH_SERVICE_USERNAME'),
                'password' => env('APP_AUTH_SERVICE_PASSWORD'),
                'client_id' => env('APP_AUTH_SERVICE_CLIENT_ID'),
                'grant_type' => 'password',
            ]
        ]);
        $this->gzClientCreds = json_decode($res->getBody()->getContents());
    }

    private function getGuzzleClient()
    {
        if ($this->gzClient == null)
            $this->initGuzzleClient();
        return $this->gzClient;
    }

    public function getUsers()
    {
        return $this->client->getUsers();
    }

    public function changePassword($id, $password)
    {
        $realm = env('APP_AUTH_SERVICE_REALM');
        $client = $this->getGuzzleClient();
        $accessToken = $this->gzClientCreds->access_token;
        $res = $client->put("/auth/admin/realms/$realm/users/$id/reset-password", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json'
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'type' => 'password',
                'value' => $password,
                'temporary' => true
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }

    public function logoutUser($id)
    {
        $realm = env('APP_AUTH_SERVICE_REALM');
        $client = $this->getGuzzleClient();
        $accessToken = $this->gzClientCreds->access_token;
        $res = $client->post("/auth/admin/realms/$realm/users/$id/logout", [
            'headers' => ['Authorization' => "Bearer $accessToken"],
        ]);
        return json_decode($res->getBody()->getContents());
    }

    public function blockUser($id)
    {
        return $this->client->updateUser(['id'=>$id, 'enabled'=>false]);
    }

    public function unblockUser($id)
    {
        return $this->client->updateUser(['id'=>$id, 'enabled'=>true]);
    }
}
