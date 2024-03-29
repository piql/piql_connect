<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Interfaces\IngestValidationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
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
     * Persist a file's path and associate it with a bag id.
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
        return new FileResource($file);
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

    public function deleteUploadedTemp( $request ) {
        $folderToDelete = "incoming/chunks/{$request}";

        if( !Storage::exists( $folderToDelete ) ) {
            Log::debug("Temp upload folder: {$folderToDelete} does not exist.");
            return response()->json( "Temporary upload folder ${folderToDelete} already deleted or never created.", 200 );
        }
        Log::debug("Deleting temp upload folder: {$folderToDelete}");
        $result = Storage::deleteDirectory( $folderToDelete );
        if( $result == false) {
            return response()->json( "Could not delete temporary files; reason unknown", 500 );
        }
        return response()->json( "Deleted temporary upload folder for {$request}", 200 );
    }

    public function validateFileName(Request $request) {
        $service = app()->make( IngestValidationInterface::class );
        return response()->json( $service->validateFileName($request->fileName), 200 );
    }
}
