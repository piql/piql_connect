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
}
