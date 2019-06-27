<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ArchivematicaService;

class IngestSettingsController extends Controller
{
    public function show()
    {
            return view('ingest.settings.all', ['archivematicaServices' => ArchivematicaService::all()]);
    }
}
