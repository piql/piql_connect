<?php


namespace App\Http\Controllers\Api\Ingest;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessArchivematicaServiceCallback;
use Log;

class ArchivematicaServiceCallback extends Controller
{
    public function packageUploaded($serviceUuid, $packageUuid) {
        $this->dispatch(new ProcessArchivematicaServiceCallback($serviceUuid, $packageUuid));
        return response("OK", 200);
    }
}
