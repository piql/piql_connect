<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StorageLocation;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Interfaces\ArchivalStorageInterface  $storage
     * @return \Illuminate\Http\Response
     */

    public function index( Request $request, \App\Interfaces\ArchivalStorageInterface $storage )
    {
        $location = StorageLocation::find( $request->id );
        $result = $storage->ls( $location, "", $recursive = true );
        return json_encode(["data" => [ "files" => $result ]]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Interfaces\ArchivalStorageInterface  $storage
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Interfaces\ArchivalStorageInterface $storage )
    {
        $storageLocation = StorageLocation::find( $request->id );
        $uploadFileAtPath = $request->input('data.path');
        $result = $storage->upload( $storageLocation, "", $uploadFileAtPath );
        return json_encode(["data" => $result ]);
    }

    /**
     * Create a local copy of a resource in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Interfaces\ArchivalStorageInterface  $storage
     * @return \Illuminate\Http\Response
     */

    public function copy(Request $request, \App\Interfaces\ArchivalStorageInterface $storage )
    {
        $storageLocation = StorageLocation::find( $request->id );
        $downloadFileAtRemotePath = $request->input('data.remotePath');
        $storeDownloadedFileAtLocalPath = $request->input('data.localPath');
        $result = $storage->download( $storageLocation, $downloadFileAtRemotePath, $storeDownloadedFileAtLocalPath );
        return json_encode(["data" => $result ]);
    }


    /**
     * Make a deletion - delete a remote file from storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Interfaces\ArchivalStorageInterface  $storage
     * @return \Illuminate\Http\Response
     */
    public function deletion(Request $request, \App\Interfaces\ArchivalStorageInterface $storage )
    {
        $storageLocation = StorageLocation::find( $request->id );
        $fileToDelete = $request->input('data.path');
        $result = $storage->delete( $storageLocation, $fileToDelete );
        $message = $result ? "Successfully deleted file " : "Failed to delete file ";
        return json_encode(["data" => [ "message" => $message . $fileToDelete ] ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
