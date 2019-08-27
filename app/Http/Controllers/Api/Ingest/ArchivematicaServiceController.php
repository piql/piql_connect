<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Http\Controllers\Controller;
use App\ArchivematicaService;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Bag;
use Log;

class ArchivematicaServiceController extends Controller
{
    private $apiClient;

    public function __construct()
    {
        $service = ArchivematicaService::first();
        $base_uri = $service->url;
        if(!endsWith($base_uri, '/')){
            $base_uri .='/';
        }
        $this->apiClient = new Guzzle([
            'base_uri' => $base_uri,
            'headers' => [
                'Authorization' => 'ApiKey '.$service->api_token,
                'Content-Type' => 'application/json',
            ]
        ]);
    }
    /**
     * Get a listing of known Archivematica Dashboard Instances
     *  - Which in turn host the Archivematica Ingest REST APIs
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceInstances()
    {
        $all = ArchivematicaService::all();

        return response($all, 200)
            ->header("Content-type","application/json");
    }

    /**
     * Get a list of transfers
     * @id the id of the Bag to transfer
     * @return \Illuminate\Http\Response
     */
    public function transferStatus()
    {
        $request = $this->apiClient->get('transfer/status/');
        return response( $request->getBody(), $request->getStatusCode() );
    }


    /**
     * Notify Archivematica a file is ready for transfer
     *
     * @return \Illuminate\Http\Response
     */

    public function startTransfer($id)
    {
        $bag = Bag::find($id);
        $locationId = env('STORAGE_LOCATION_ID');
        $paths = base64_encode($locationId.":/".$bag->zipBagFileName());

        $formData =
            [
                "name" => $bag->name,
                "type" => "zipped bag",
                "accession" => $bag->id,
                "paths[]" => $paths,
                "row_ids[]" => ""
            ];
        $request = $this->apiClient->post('transfer/start_transfer/',
            ['form_params' => $formData]
        );
        return response( $request->getBody(), $request->getStatusCode() );
    }

    public function approveTransfer($id)
    {
        $bag = Bag::find($id);
        Log::info("Approving transfer for bag with id ".$bag->id);

        $formData =
            [
                "type" => "zipped bag",
                "directory" => $bag->zipBagFileName(),
            ];
        $request = $this->apiClient->post('transfer/approve/',
            ['form_params' => $formData]
        );

        return response( $request->getBody(), $request->getStatusCode() );
    }


    /**
     * Get a list of ingests
     *
     * @return \Illuminate\Http\Response
     */
    public function ingestStatus()
    {
        $request = $this->apiClient->get('ingest/status');
        return response( $request->getBody(), $request->getStatusCode() );
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function show(ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function edit(ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArchivematicaService $archivematicaService)
    {
        //
    }
}
