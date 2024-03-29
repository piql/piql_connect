<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\ArchivematicaService;
use Illuminate\Http\Request;
use App\Bag;
use Log;

class ArchivematicaServiceController extends Controller
{
    private $guzzle;

    public function __construct( \GuzzleHttp\Client $guzzleClient = null )
    {
        $this->guzzle = $guzzleClient;
    }

    private function apiClient()
    {
        $service = ArchivematicaService::first();
        if( $service == null ) {
            abort( response()->json( ['error' => 428, 'message' => 'No archivematica service configuration could be found on this instance. A valid service configuration must be created first.'], 428 ) );
        }
        $base_uri = $service->url;
        if(!endsWith($base_uri, '/')){
            $base_uri .='/';
        }

        return $this->guzzle ?? new \GuzzleHttp\Client([
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
        $request = $this->apiClient()->get('transfer/status/');
        return response( $request->getBody(), $request->getStatusCode() );
    }


    /**
     * Notify Archivematica a file is ready for transfer
     *
     * @return \Illuminate\Http\Response
     */

    public function startTransfer($id)
    {
        $validate = $this->validateId( $id );
        if( $validate !== true ) {
            return $validate;
        }

        $bag = Bag::find($id);
        $expectedState = 'initiate_transfer';
        if( $bag->status != $expectedState ) {
            return response()->json(['status' => 409, 'messages' => ['state' => $this->stateError($expectedState, $bag->status)]], 409);
        }

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
        $request = $this->apiClient()->post('transfer/start_transfer/',
            ['form_params' => $formData]
        );
        return response( $request->getBody(), $request->getStatusCode() );
    }

    public function approveTransfer( $id )
    {
        $validate = $this->validateId( $id );
        if( $validate !== true ) {
            return $validate;
        }

        $bag = Bag::find($id);
        Log::info("Approving transfer for bag with id ".$bag->id);

        $expectedState = "approve_transfer";
        if( $bag->status != $expectedState ) {
            return response()->json(['status' => 409, 'messages' => ['state' => $this->stateError($expectedState, $bag->status)]], 409);
        }

        $formData =
            [
                "type" => "zipped bag",
                "directory" => $bag->zipBagFileName(),
            ];
        $request = $this->apiClient()->post('transfer/approve/',
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
        $request = $this->apiClient()->get('ingest/status');
        return response( $request->getBody(), $request->getStatusCode() );
    }

    public function transferHideStatus($id)
    {
        $validate = $this->validateId( $id );
        if( $validate !== true ) {
            return $validate;
        }

        $bag = Bag::find( $id );
        $expectedState = "complete";
        if( $bag->status != $expectedState ) {
            return response()->json(['status' => 409, 'messages' => ['state' => $this->stateError($expectedState, $bag->status)]], 409);
        }

        $request = $this->apiClient()->delete("transfer/{$id}/delete/");
        return response( $request->getBody(), $request->getStatusCode() );
    }

    public function ingestHideStatus($id)
    {
        $expectedState = "approve_transfer";
        if( $bag->status != $expectedState ) {
            return response()->json(['status' => 409, 'messages' => ['state' => $this->stateError($expectedState, $bag->status)]], 409);
        }

        $request = $this->apiClient()->delete("ingest/{$id}/delete/");
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

    private function stateError( $expected, $actual )
    {
        return "The selected bag is in the wrong state. It should be [{$expected}] but was [{$actual}].";
    }

    private function validateId( $id )
    {
        $validator = Validator::make(
            [ 'bagId' => $id ],
            [ 'bagId' => 'required|numeric|min:1|exists:bags,id']
        );

        if( $validator->fails() ){
            return response()->json(['status' => 422, 'messages' => $validator->errors() ], 422);
        }

        return true;
    }

}
