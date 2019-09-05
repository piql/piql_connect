<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bag;
use App\File;


class IngestMetadataController extends Controller
{
    public function edit($bagId, $fileId)
    {
        $bag = Bag::query()->findOrFail($bagId);
        $file = File::query($bag->files())->findOrFail($fileId);
        $readonly = $bag->status != "open";
        return view('ingest.metadata.edit', compact('bag', 'file', 'readonly'));
    }
}
