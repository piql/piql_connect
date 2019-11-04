<?php


namespace App\Util;

use App\ArchivematicaService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use \Log;

class ArchivematicaServiceClient
{
    private $apiClient;

    public function __construct( \GuzzleHttp\Client $client = null)
    {
        $service = ArchivematicaService::first();
        if( $service == null ) {
            abort( response()->json( ['error' => 428, 'message' => 'No archivematica service configuration could be found on this instance. A valid service configuration must be created first.'], 428 ) );
        }
        $base_uri = $service->url;
        if(!endsWith($base_uri, '/')){
            $base_uri .='/';
        }
        $this->apiClient = $client ?? new \GuzzleHttp\Client([
                'base_uri' => $base_uri,
                'headers' => [
                    'Authorization' => 'ApiKey '.$service->api_token,
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false,
            ]);
    }

    public function doRequest($method, $uri, array $options = []) {
        try {
            $request = $this->apiClient->request($method, $uri, $options);
            return response( $request->getBody(), $request->getStatusCode() );

        } catch (TransferException $e) {
            Log::warning($e->getMessage());

            if($e->hasResponse()) {
                Log::warning($e->getResponse()->getBody());
                return response( $e->getResponse()->getBody(), $e->getResponse()->getStatusCode() );

            } else {
                $response = json_decode('"error" : "true", "'.$e->getMessage().'"');
                return response( $response, 400 ); //todo: find a suitable error code

            }
        }
    }
}
