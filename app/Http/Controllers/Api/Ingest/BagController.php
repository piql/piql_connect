<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Controller;
use App\Bag;
use App\File;
use App\User;
use App\Job;
use App\Archive;
use App\StorageProperties;
use App\Http\Resources\BagResource;
use App\Http\Resources\BagCollection;
use App\Http\Resources\FileCollection;
use Response;
use App\Events\PreProcessBagEvent;
use App\Events\FileUploadedEvent;
use Carbon\Carbon;
use Log;

class BagController extends Controller
{

    private $nameValidationRule = '/^([^:\\<>"\/?*|]*){3,}$/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function latest(Request $request)
    {
        $user = Auth::user();
        $bag = null;
        if( $user->bags->count() > 0){
            $bag = $user->bags()->latest()->first();
        }
        if( $bag == null || $bag->status !== 'open' ){
            $bag = Bag::create(['name' => ""]);
            $bag->storage_properties()->update([
                'archive_uuid' => Archive::first()->uuid,
                'holding_name' => Archive::first()->holdings()->first()->title ?? ""
            ]);
        }

        $resultBag = Bag::with([ 'files' ])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
            ->leftJoin('holdings', 'holdings.title', 'storage_properties.holding_name')
            ->where('bags.id','=', $bag->id)
            ->select('bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.title AS holding_name')
            ->first();

        return new BagResource( $resultBag );
    }

    public function offline()
    {
        $jobs = \App\Job::where('status', '=', 'ingesting')->get();
        $bags = $jobs->map( function ($job) {
            return $job->bags()->get()->map( function($bag) {
                return $bag;
            } );
        })->flatten();
        return new BagCollection($bags);
    }

    public function online(Request $request)
    {
        $archive = $request->query('archive');
        $bags = Bag::where('status', 'complete')
                    ->whereHas('storage_properties', function ( $query ) use($archive) {
                    $query->where('archive_uuid', $archive);
                })->get();
        return new BagCollection($bags);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Interfaces\SettingsInterface $settingsProvider )
    {
        $settings = $settingsProvider->forAuthUser();

        $bagName = trim( $request->name );
        if($this->validateFailes($bagName)) {
            abort(response()->json(["error" => 424, "message" => "Bag doesn't have a valid name: {$bagName}"], 424));
        }
        $bag = Bag::create(['name' => $bagName]);
        if( $bag )
        {
            if( $request->filled( 'archive_uuid' ) && $request->filled( 'holding_name' ) )
            {

                $validatedData = $request->validate([
                    'archive_uuid' => 'required|uuid',
                    'holding_name' => 'required|string',
                ]);
                $bag->storage_properties->update($validatedData);

                $resultBag =
                    Bag::query()
                        ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
                        ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
                        ->leftJoin('holdings', 'holdings.title', 'storage_properties.holding_name')
                        ->select('bags.*', 'archives.title AS archive_title',
                            'archives.uuid AS archive_uuid', 'holdings.title AS holding_name')
                        ->find($bag->id);

                return new BagResource( $resultBag );
            }
            else
            {
                $resultBag =
                    Bag::query()
                        ->join( 'storage_properties', 'storage_properties.bag_uuid', 'bags.uuid' )
                        ->select( 'bags.*' )
                        ->find( $bag->id );
                return new BagResource( $resultBag );
            }
        }

        abort( 501, "Could not create bag with name ".$bagName." and owner ".Auth::id() );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
   {
        $bag = Bag::with([ 'files' ])
            ->join( 'storage_properties', 'storage_properties.bag_uuid', 'bags.uuid' )
            ->leftJoin( 'archives', 'archives.uuid', 'storage_properties.archive_uuid' )
            ->leftJoin( 'holdings', 'holdings.title', 'storage_properties.holding_name' )
            ->select( 'bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.title AS holding_name' )
                ->find($id);

        if( $bag == null ) {
            abort( response()->json([ 'error' => 404, 'message' => 'Could not find any bag with id '.$id ], 404 ) );
        }
        if( $bag->owner !== Auth::id() ) {
            abort( response()->json([ 'error' => 401, 'message' => 'The current user is not authorized to access bag with id '.$id ], 401 ) );
        }
        return new BagResource($bag);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFiles($id)
    {
        $bag = Bag::find($id);
        if( $bag == null ) {
            abort( response()->json([ 'error' => 404, 'message' => 'Could not find any bag with id '.$id ], 404 ) );
        }
        if( $bag->owner !== Auth::id() ) {
            abort( response()->json([ 'error' => 401, 'message' => 'The current user is not authorized to access bag with id '.$id ], 401 ) );
        }

        $files = $bag->files()->latest()->get();
/*TODO: Move to AIP        $result = $files->map( function ($file) {
            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
            return collect(["fupath" => $file->uuid.".".$ext])->merge($file);
});
 */
        return new FileCollection( $files );
    }

    public function complete()
    {
        $bags = Bag::with('files')->latest()
                ->where( 'owner', Auth::id() )
                ->where('status', 'complete')
                ->paginate(env("DEFAULT_ENTRIES_PER_PAGE"));
        return new BagCollection($bags);
    }

    public function processing( Request $request )
    {
        $bags = Bag::latest()
            ->where(  'status', '=', 'closed')
            ->orWhere('status', '=', 'bag_files')
            ->orWhere('status', '=', 'move_to_outbox')
            ->orWhere('status', '=', 'initiate_transfer')
            ->orWhere('status', '=', 'approve_transfer')
            ->orWhere('status', '=', 'transferring')
            ->orWhere('status', '=', 'ingesting')
            ->where( 'owner', Auth::id() )
            ->paginate(env("DEFAULT_ENTRIES_PER_PAGE"));
        $bags->setPath("/".$request->path() );
        return new BagCollection($bags);
    }

    public function all(Request $request)
    {
        $q = Bag::query()->where('bags.owner', Auth::id() );
        if(empty($request->query())){
            $q->leftJoin('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
              ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
              ->leftJoin('holdings', 'holdings.title', 'storage_properties.holding_name')
              ->leftJoin('bag_job', 'bags.id', '=', 'bag_job.bag_id')
              ->leftJoin('jobs', 'bag_job.job_id', '=', 'jobs.id')
              ->select('bags.*', 'archives.title AS archive_title',
              'archives.uuid AS archive_uuid', 'holdings.title AS holding_name')
              ->distinct()->latest();

          return new BagCollection(
              $q->paginate( env("DEFAULT_ENTRIES_PER_PAGE") )
          );
        }


        $q->leftJoin('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
          ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
          ->leftJoin('holdings', 'holdings.title', 'storage_properties.holding_name')
          ->leftJoin('bag_job', 'bags.id', '=', 'bag_job.bag_id')
          ->leftJoin('jobs', 'bag_job.job_id', '=', 'jobs.id')
          ->select('bags.*', 'archives.title AS archive_title',
          'archives.uuid AS archive_uuid', 'holdings.title AS holding_name')
          ->distinct();
        if( $request->has( 'holding' ) ) {
            $q->where( 'holding_name', $request->query( 'holding' ) );
        }
        $q->get();

        if( $request->has( 'archive' ) ) {
            $q->where( 'archive_uuid', $request->query( 'archive' ) );
        }


        $q->get();
        if( $request->has('search')) {
            $terms = collect(explode(" ", $request->query('search')));
            if($terms->count() == 1){
                $q->where('bags.name','LIKE','%'.$terms->first().'%');
            } else if($terms->count() > 1) {
                $terms->each( function ($term, $key) use ($q) {
                    if($key == 0) {
                        $q->orWhere('bags.name','LIKE','%'.$term.'%');
                    } else {
                        $q->where('bags.name','LIKE','%'.$term.'%');
                    }
                });
            }
        }
        $q->get();

        if( $request->has('location') ) {
            if($request->query('location') == 'offline') {
                    $q->where('jobs.status', 'ingesting');
            }
            else {
                $q->where('bags.status','complete');
            }
        };

        $q->latest();
        $q->paginate(env("DEFAULT_ENTRIES_PER_PAGE"));
        return new BagCollection($q->get());
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
        Log::debug("Bag edit");
    }


    public function deleteFile( Request $request, $bagId, $fileId ){
        $file = \App\File::find($fileId);
        if(! $file ){
            abort( response()->json([ 'error' => 410, 'message' => 'File not found or already deleted for file with id {$fileId}'], 410 ) );
        }
        $filePath = $file->storagePath();
        Log::debug("Trying to delete file {$fileId} from bag {$bagId} at path {$filePath} ");
        try {
            if(! Storage::disk('uploader')->delete( $filePath ) ) {
                throw new \Exception("Reason unknown. Possibly a permissions problem.");
            }
        } catch( \Exception $ex ) {
            $exMessage = $ex->getMessage();
            $trace = $ex->getTrace();
            $jtrace = json_encode( $trace );
            abort( response()->json([ 'error' => 500, 'message' => "Unable to delete file with id {$fileId}: {$exMessage}", 'Stack trace' => $trace ], 410 ) );
        }
        $file->delete();
        return response()->json(["message" => "File {$fileId} from bag {$bagId} deleted."]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        $bag = Bag::find( $id );
        if( $bag == null )
        {
            abort( response()->json([ 'error' => 404, 'message' => 'Bag not found for id '.$id ], 404 ) );
        }
        if( $bag->owner !== Auth::id() ) {
            abort( response()->json([ 'error' => 401, 'message' => 'The current user is not authorized to update bag with id '.$id ], 401 ) );
        }

        if( $request->filled( "name" ) )
        {
            if($this->validateFailes($request->name)) {
                abort(response()->json(["error" => 424, "message" => "Bag doesn't have a valid name: {$request->name}"], 424));
            }
            $bag->update([ 'name' => $request->name ]);
        }

        if( $request->filled( "archive_uuid" ) && $request->filled( "holding_name" ) )
        {
            $bag->storage_properties->update([
                'archive_uuid' => $request->archive_uuid,
                'holding_name' => $request->holding_name
            ]);
        }

        $resultBag = Bag::with([ 'files' ])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
            ->leftJoin('holdings', 'holdings.title', 'storage_properties.holding_name')
            ->where('bags.id','=', $id)
            ->select('bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.title AS holding_name')
            ->first();

        return new BagResource($resultBag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::debug("Bag destroy");
        //
    }


    /**
     * Commit the bag to archivematica
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function commit($id)
    {
        $bag = Bag::find($id);

        if($this->validateFailes($bag->name)) {
            abort(response()->json(["error" => 424, "message" => "Bag doesn't have a valid name: {$bag->name}"], 424));
        }

        try {
            $bag->applyTransition('close');
            $bag->save();
            Log::debug("emitting ProcessFilesEvent for bag with id " . $id);
            event(new PreProcessBagEvent($bag));
        } catch (BagTransitionException $e) {
            abort(501, "Caught an exception closing bag with id " . $id . ". Exception: {$e}");
            Log::debug("Caught an exception closing bag with id " . $id . ". Exception: {$e}");
        }
    }

    public function bagSingleFile( Request $request )
    {
        $bag = Bag::create([
            'name' => $request->fileName,
        ]);

        $file = new File();
        $file->fileName = $request->fileName;
        $file->filesize = $request->fileSize;
        $file->uuid = pathinfo($request->result["name"])['filename'];
        $file->bag_id = $bag->id;
        $file->save();

        event( new FileUploadedEvent( $file, Auth::user() ) );

        try {
            $bag->applyTransition('close');
            $bag->save();
            Log::debug("emitting ProcessFilesEvent for bag with id " . $bag->id);
            event(new PreProcessBagEvent($bag));
        } catch (BagTransitionException $e) {
            abort(501, "Caught an exception closing bag with id " . $bag->id . ". Exception: {$e}");
            Log::debug("Caught an exception closing bag with id " . $bag->id . ". Exception: {$e}");
        }
    }


    public function piqlIt($id)
    {
        $bag = Bag::find($id);
        $bag->status = "piqld";
        $bag->save();
        return $bag;
    }

    public function download($id)
    {
        $bag = Bag::find($id);
        $path = storage_path(env('STORAGE_TRANSFER_PATH')."/{$bag->zipBagFileName()}");
        Log::info("Download path: ".$path);
        return response()->download($path);
    }

    public function downloadFile($fileId)
    {
        $file = \App\File::find($fileId);
        $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
        $filename = $file->uuid.".".$ext;
        $path = storage_path(env('STORAGE_UPLOADER_PATH')."/completed/{$filename}");
        return response()->download($path);
    }

    private function validateFailes($name) : bool {
        return Validator::make(['name' => $name], [
            'name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if(!preg_match($this->nameValidationRule, $value, $matches, PREG_OFFSET_CAPTURE)) {
                        $fail($attribute.' is invalid.');
                    }
                }],
        ])->fails();
    }
}
