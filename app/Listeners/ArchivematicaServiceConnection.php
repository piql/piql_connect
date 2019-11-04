<?php


namespace App\Listeners;


use App\ArchivematicaService;

class ArchivematicaServiceConnection
{

    public function __construct(ArchivematicaService $service)
    {
        $base_uri = $service->url;
        if(!endsWith($base_uri, '/')){
            $base_uri .='/';
        }

        $this->apiClient = new \GuzzleHttp\Client([
            'base_uri' => $base_uri,
            'headers' => [
                'Authorization' => 'ApiKey '.$service->api_token,
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    public function doRequest($method, $uri, array $options = []) {
        $request = $this->apiClient->request($method, $uri, $options);
        return (object)[
            'contents' => json_decode($request->getBody()->getContents()) ?? "",
            'statusCode' => $request->getStatusCode(),
        ];
    }
}
