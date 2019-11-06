<?php


namespace App\Listeners;


use App\ArchivematicaService;
use GuzzleHttp\Exception\RequestException;
use Log;

class ArchivematicaServiceConnection
{

    private $apiClient;
    private $service;

    public function __construct(ArchivematicaService $service = null)
    {
        $this->service = $service ?? ArchivematicaService::first();
        $base_uri = $this->service->url;
        if(!endsWith($base_uri, '/')){
            $base_uri .='/';
        }

        $this->apiClient = new \GuzzleHttp\Client([
            'base_uri' => $base_uri,
            'headers' => [
                'Authorization' => 'ApiKey '.$this->service->api_token,
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    public function doRequest($method, $uri, array $options = []) {
        try {
            $request = $this->apiClient->request($method, $uri, $options);
            return (object)[
                'contents' => json_decode($request->getBody()->getContents()) ?? "",
                'statusCode' => $request->getStatusCode(),
            ];
        } catch (RequestException $exception) {
            $message = $exception->getMessage() ?? "";
            $message .= ". ArchivematicaService : " . json_encode($this->service);
            return (object)[
                'contents' => (object) [
                    'message' => $message,
                    'error' =>  'true'
                ],
                'statusCode' => 500,
            ];
        }
    }
}
