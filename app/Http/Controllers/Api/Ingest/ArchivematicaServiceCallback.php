<?php


namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessArchivematicaServiceCallback;
use Log;

class ArchivematicaServiceCallback extends Controller
{
    public function packageUploaded($serviceUuid, $packageUuid, Request $request) {
        Log::debug("Receiving callback from {$serviceUuid} with {$packageUuid}");

        //TODO: Store the service uuid in StorageProperties
        //Passing it around still only allows for one static service...
        $validator = Validator::make( [
            'serviceUuid' => $serviceUuid,
            'packageUuid' => $packageUuid
        ], [
            'serviceUuid' => 'required|uuid',
            'packageUuid' => 'required|uuid'
        ]);

        if($validator->fails())
        {
            Log::error($validator->errors()->getMessages());
            return response($validator->errors()->getMessages(), 400);
        }

        $this->dispatch( new ProcessArchivematicaServiceCallback( $packageUuid ) );
        return response("OK", 200);
    }
}
