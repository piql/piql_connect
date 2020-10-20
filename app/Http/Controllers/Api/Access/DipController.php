<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoragePropertiesToDipResource;
use App\Http\Resources\FileObjectResource;
use App\Dip;
use App\StorageProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FilePreviewInterface;
use App\FileObject;
use App\Traits\UserSettingRequest;
use Illuminate\Support\Facades\Auth;
use Log;
use App\Holding;

class DipController extends Controller
{
    use UserSettingRequest;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index( Request $request )
    {
        $q = StorageProperties::query()->whereHas("dip");
        $terms = collect(explode(" ", $request->query('search')))->reject("");
        $archiveUuid = $request->query('archive');
        $holdingUUID = $request->query('holding');
        $fromDate = $request->query('archived_from');
        $toDate = $request->query('archived_to');

        if( $fromDate ) {
            try {
                $cq = new \Carbon\Carbon( $fromDate );
            } catch ( \Exception $ex ) {
                Log::warn( "failed to parse archived_from date ".$ex->getMessage() );
            }
            if( $cq ) {
                $q->whereDate('created_at', '>=', $cq->toDateString() );
            }
        }

        if( $toDate ) {
            try {
                $cq = new \Carbon\Carbon( $toDate );
            } catch ( \Exception $ex ) {
                Log::warn( "failed to parse archived_to date ".$ex->getMessage() );
            }
            if( $cq ) {
                $q->whereDate('created_at', '<=', $cq->toDateString() );
            }
        }

        if($archiveUuid) {
            $q->where('archive_uuid', $archiveUuid);
            if($holdingUUID) {
                $q->where('holding_uuid', $holdingUUID);
            }
        }

        if($terms->count() == 1) {
            $q->where('name', 'LIKE', "%{$terms->first()}%");
        } elseif( $terms->count() > 1) {
            $terms->each( function ($term, $key) use ($q) {
                $q->where('name','LIKE',"%{$term}%");
            });
        }

        return StoragePropertiesToDipResource::collection(
            $q->paginate($this->rowLimit(Auth::user(), $request))
        );
    }

    private function filter_package_thumbnail($dip, FilePreviewInterface $filePreview) {
        return $dip->fileObjects->filter( function ($file, $key) use(&$filePreview) {
            $pathInfo = pathinfo($file->fullpath);
            $ext = strtolower($pathInfo['extension']);
            if ($ext != 'xml' && !$filePreview->isPreviwableFile($file->mime_type, true)) {
                return $file;
            } else {
                return Str::contains( $file->fullpath, '/thumbnails' );
            }
        })->first();
    }

    public function package_thumbnail( Request $request, ArchivalStorageInterface $storage, FilePreviewInterface $filePreview )
    {
        $dip = Dip::findOrFail( $request->dipId );
        $file = $this->filter_package_thumbnail($dip, $filePreview);
        if ($file === null) {
            Log::error("Failed to find file");
            return response([
                "message" => "Failed to find file"
            ], 400);
        }
        $filePreview->storage($storage)->dip($dip)->fileObject($file);
        try {
            $stream = $filePreview->getContent(true);
        } catch (\Exception $e) {
            Log::error("Failed to download thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download thumbnail"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read thumbnail"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        }, 200, ["Content-Type" , $filePreview->getMimeType()]);
    }

    public function package_preview( Request $request, ArchivalStorageInterface $storage )
    {
        $dip = Dip::findOrFail( $request->dipId );
        $file = $dip->fileObjects->filter( function ($file, $key) {
            return Str::contains( $file->fullpath, '/objects' );
        })->first();
        if ($file === null) {
            Log::error("Failed to find file");
            return response([
                "message" => "Failed to find file"
            ], 400);
        }

        try {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
        } catch (\Exception $e) {
            Log::error("Failed to download preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download preview"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        return response()->stream( function () use( $stream) {
            fpassthru($stream);
            fclose($stream);
        }, 200, ["Content-Type" , "image/jpeg"]);
    }

    public function files( Request $request )
    {
        $dip = Dip::find( $request->dipId );
        $q = $dip->fileObjects()->where( 'path', 'LIKE', "%/objects" );
        if ($search = $request->query('search')) {
            $q->where('filename', 'LIKE', '%' . $search . '%');
        }
        $files = $q->paginate($this->rowLimit(Auth::user(), $request));
        return FileObjectResource::collection( $files );
    }

    public function aipFile( Request $request )
    {
        // todo: The METS paring to figure out the relationship between AIP and DIP files
        //       ought to be moved to the ingest workflow
        $dipFileObject = FileObject::find( $request->fileId );
        $dip = Dip::find( $request->dipId );
        $aip = $dip->storage_properties->aip;

        $metsFileName = "METS.{$aip->external_uuid}.xml";
        $metsFile = Cache::get($metsFileName);
        if($metsFile === null) {
            $metsFileObject = $dip->fileObjects->first(function ($fileObject) use ($metsFileName) {
                return Str::endsWith($fileObject->filename, $metsFileName);
            });
            if($metsFileObject === null) {
                return response( "Mets file not found in dip {$request->dipId} for {$dipFileObject->fullpath}", 404 );
            }

            $storage = \App::make(\App\Interfaces\ArchivalStorageInterface::class );
            $metsFile =  $storage->downloadContent( $dip->storage_location, $metsFileObject->fullpath );

            Cache::put($metsFileName, $metsFile, 60);
        }

        $aipFileObjectPath = Cache::get($dipFileObject->filename);
        if($aipFileObjectPath === null) {
            if (!preg_match('/^(\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/', $dipFileObject->filename, $matches, PREG_OFFSET_CAPTURE)) {
                return response("File not found - no match in dip {$request->dipId} for {$dipFileObject->fullpath}", 404);
            }

            $metsParser = \App::make(\App\Interfaces\MetsParserInterface::class);
            $metsFileId = "file-{$matches[1][0]}";
            $aipFileObjectPath = $metsParser->findOriginalFileName($metsFile, $metsFileId);
            Cache::put($dipFileObject->filename, $aipFileObjectPath, 60);
        }

        $files = $aip->fileObjects()->where('fullpath', 'LIKE', "%".$aipFileObjectPath)->get();
        if(count($files))
            return FileObjectResource::collection( $files );

        return response( "File not found - no match in aip {$aip->id} for  {$aipFileObjectPath} ");
    }

    public function file_preview( Request $request, ArchivalStorageInterface $storage, FilePreviewInterface $filePreview )
    {
        $dip = Dip::findOrFail( $request->dipId );
        $file = $dip->fileObjects->find( $request->fileId );
        $filePreview->storage($storage)->dip($dip)->fileObject($file);

        try {
            $stream = $filePreview->getContent();
        } catch (\Exception $e) {
            Log::error("Failed to download preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download preview"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        }, 200, ["Content-Type" , $filePreview->getMimeType()]);
    }

    public function file_download( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::findOrFail( $request->dipId );
        $file = $dip->fileObjects->findOrFail( $request->fileId );

        try {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
        } catch (\Exception $e) {
            Log::error("Failed to download preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download preview"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        return response()->streamDownload( function () use( $stream ) {
            passthru($stream);
            fclose($stream);
        }, basename( $file->path ), [
            "Content-Type" => "application/octet-stream",
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }

    private function filter_file_thumbnail($dip, $file, FilePreviewInterface $filePreview)
    {
        return $dip->fileObjects->filter( function ($thumb, $key) use( $file, &$filePreview ) {
            if (!$filePreview->isPreviwableFile($file->mime_type, true)) {
                    return $file;
                } else {
                    return Str::contains( $thumb->path, '/thumbnails' );;
                }
            })->filter( function ($thumb, $key) use ( $file ) {
                return Str::contains( $file->filename, pathinfo( $thumb->filename, PATHINFO_FILENAME ) );
            })->first();
    }

    public function showFile($dipId, $fileId)
    {
        $dip = Dip::find( $dipId );
        return $dip->fileObjects->find( $fileId )->toArray();
    }

    public function file_thumbnail( ArchivalStorageInterface $storage, Request $request, FilePreviewInterface $filePreview )
    {
        $dip = Dip::findOrFail( $request->dipId );
        $file = $dip->fileObjects->find( $request->fileId );
        $thumbnail = $this->filter_file_thumbnail($dip, $file, $filePreview);
        $filePreview->storage($storage)->dip($dip)->fileObject($thumbnail);
        try {
            $stream = $filePreview->getContent(true);
        } catch (\Exception $e) {
            Log::error("Failed to download thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download thumbnail"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read thumbnail"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        }, 200, ["Content-Type" , $filePreview->getMimeType()]);
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
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, ArchivalStorageInterface $storage )
    {
        $dip = Dip::find( $request->dipId );
        return $dip->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function edit(Dip $dip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dip $dip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dip $dip)
    {
        //
    }
}
