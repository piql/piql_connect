<?php

namespace App\Http\Controllers;

use App\Bag;
use App\File;
use App\FileObject;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('access.browse.show');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function metadata($fileId)
    {
        $file = FileObject::findOrFail($fileId);
        $readonly = true;
        return view('access.browse.metadata.edit', compact( 'file', 'readonly'));
    }

    public function accessShowMetadata($fileId)
    {
        $file = FileObject::findOrFail($fileId);
        $dc = [
            "title" => "", "creator" => "", "subject" => "", "description" => "", "publisher" => "",
            "contributor" => "", "date" => "", "type" => "", "format" => "", "identifier" => "",
            "source" => "", "language" => "", "relation" => "", "coverage" => "", "rights" => ""
        ];

        try {
            $dc = $file->metadata->first()->metadata["dc"];
        } catch ( \Exception $ex )
        {
            //TODO: TEMP HACK TO GET AROUND MISSING METADATA. DESTROY.
        }
        $readonly = true;
        return view('access.browse.metadata.show', compact( ['file', 'readonly', 'dc']));
    }
}
