<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Aip;
use Illuminate\Http\Request;
use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FileArchiveInterface;
use App\FileObject;
use Log;

/**
 * @OA\Info(
 *      version="0.3.0",
 *      title="AIP Controller",
 *      description="Manages AIPs ",
 *      @OA\Contact(
 *          email="kare.andersen@piql.com"
 *      ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

class AipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     * 
     * @OA\Get(
     *     path="/api/v1/access/aips/{aipId}",
     *     summary="Get AIP by ID",
     *     operationId="showAIPById",
     *     @OA\Parameter(
     *          name="aipId",
     *          description="AIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
     */

    public function show( Request $request )
    {
        $aip = Aip::find( $request->aipId );
        return response( $aip );
    }

    /**
     *  @OA\Get(
     *     path="/api/v1/access/aips/{aipId}/filename",
     *     summary="Get filename of AIP by ID",
     *     operationId="getAIPFilenameById",
     *     @OA\Parameter(
     *          name="aipId",
     *          description="AIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
    */
    public function filename( Request $request )
    {
        $aip = Aip::find( $request->aipId );
        return response ( $aip->fileObjects->first()->filename );
    }

    /**
     *  @OA\Get(
     *     path="/api/v1/access/aips/dips/{dipId}/filename",
     *     summary="Get AIP filename by DIP ID",
     *     operationId="getAIPFilenameByDIPId",
     *     @OA\Parameter(
     *          name="dipId",
     *          description="DIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
    */
    public function filenameFromDipId( Request $request )
    {
        $dip = \App\Dip::find ($request->dipId );
        $aip = $dip->storage_properties->aip;
        return response ( $aip->fileObjects->first()->filename );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/access/aips/{aipId}/file/{fileId}/download",
     *     summary="Download AIP from AIP ID and DIP ID",
     *     operationId="getAIPFilenameByDIPId",
     *     @OA\Parameter(
     *          name="aipId",
     *          description="AIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="fileId",
     *          description="File ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
    */
    public function fileDownload(ArchivalStorageInterface $storage, Request $request)
    {
        /* USED FOR SINGLE FILE DOWNLOAD */
        $aip = Aip::findOrFail($request->aipId);
        $file = $aip->fileObjects()->findOrFail($request->fileId);
        $stream = $storage->downloadStream( $aip->online_storage_location, $file->fullpath );
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for aip '{$aip->id}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        return response()->streamDownload(function () use( $stream ) {
            // todo: Avoid setting the execution time here - it was done to support large files
            set_time_limit(10*60);
            fpassthru($stream);
            fclose($stream);
        }, basename( $file->path ), [
            "Content-Type" => $file->mime_type,
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/access/aips/{dipId}/downloads/files/{fileId}",
     *     summary="Download DIP from AIP ID and DIP ID",
     *     operationId="getAIPFilenameByDIPId",
     *     @OA\Parameter(
     *          name="dipId",
     *          description="DIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="fileId",
     *          description="File ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
    */
    public function download(FileArchiveInterface $fileArchiveService, Request $request )
    {
        $aip = Aip::find($request->aipId);
        $file = $aip->fileObjects->first();

        $result = $fileArchiveService->buildTarFromAipIncrementally( $aip );

        return response()->download(function () use( $aip, $result ) {
        }, basename( $result ), [
            "Content-Type" => "application/x-tar",
            "Content-Disposition" => "attachment; { $result }"
        ])->deleteFileAfterSend();
    }
    
    /**
     *  @OA\Get(
     *     path="/api/v1/access/aips/dips/{dipId}/download",
     *     summary="Download file by DIP ID",
     *     operationId="getAIPFilenameByDIPId",
     *     @OA\Parameter(
     *          name="dipId",
     *          description="DIP ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response( response=200, description="Success"),
     *     @OA\Response( response=404, description="Not Found"),
     * )
    */
    public function downloadFromDipId( FileArchiveInterface $fileArchiveService, Request $request )
    { /* USED FOR FULL AIP DOWNLOAD */
        $dip = \App\Dip::find( $request->dipId );
        $aip = $dip->storage_properties->aip;
        $result = $fileArchiveService->buildTarFromAipIncrementally( $aip );
        return response()->download( $result )->deleteFileAfterSend();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function edit(Aip $aip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aip $aip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aip $aip)
    {
        //
    }
}
