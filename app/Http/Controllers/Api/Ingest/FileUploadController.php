<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Log;
use App\File;
use App\Bag;
use Response;
use App\Events\FileUploadedEvent;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Log::info("FileUpload index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Log::info("FileUpload create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = new File();
        $file->fileName = $request->fileName;
        $file->filesize = $request->fileSize;
        $file->uuid = pathinfo($request->result["name"])['filename'];
        $file->bag_id = $request->bagId;
        $file->save();
        event( new FileUploadedEvent( $file, Auth::user() ) );
        return $file;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info("FileUpload show");
        return Response::json(File::find($id));
    }

    public function all()
    {
        Log::info("FileUpload all - needs pagination!");
        return Response::json(Bag::where('status', '=', 'processing')->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        Log::info("FileUpload edit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        Log::info("FileUpload edit");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info("FileUpload destroy");
        //
    }
}
