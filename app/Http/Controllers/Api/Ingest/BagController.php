<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Holding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Bag;
use App\File;
use App\Archive;
use App\BagTransitionException;
use App\Http\Resources\BagResource;
use App\Http\Resources\BagCollection;
use App\Http\Resources\FileCollection;
use App\Events\FileUploadedEvent;
use App\Traits\UserSettingRequest;
use Log;

class BagController extends Controller
{

    use UserSettingRequest;
    private $nameValidationRule = '/(^[^:\\<>"\/?*|]{3,64}$)/';
    private $newBagNameValidationRule = '/(^[^:\\<>"\/?*|]{0,64}$)/';

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
        // todo: ask Kare
        //$userId = $request->query('userId');
        //$userIdBytes = Uuid::import( $userId )->bytes;
        //$user = User::findOrFail( $userIdBytes );

        $bag = null;
        if( $user->bags->count() > 0){
            $bag = $user->bags()->latest()->first();
        }
        if( $bag == null || $bag->status !== 'open' ){
            $bag = Bag::create(['name' => ""]);
            $bag->storage_properties()->update([
                'archive_uuid' => Archive::first()->uuid,
                'holding_uuid' => Archive::first()->holdings()->first()->uuid ?? ""
            ]);
        }

        $resultBag = Bag::with([ 'files' ])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
            ->leftJoin('holdings', 'holdings.uuid', 'storage_properties.holding_uuid')
            ->where('bags.id','=', $bag->id)
            ->select('bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid')
            ->first();

        return new BagResource( $resultBag );
    }

    public function offline()
    {
        $jobs = \App\Job::whereIn('status', ['transferring','preparing','writing','storing'])->get();
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
        if($this->validateFailes($bagName, $this->newBagNameValidationRule)) {
            abort(response()->json(["error" => 424, "message" => "Bag doesn't have a valid name: {$bagName}"], 424));
        }
        $bag = Bag::create(['name' => $bagName]);
        if( $bag )
        {
            if( $request->filled( 'archive_uuid' ) && $request->filled( 'holding_uuid' ) )
            {
                $validatedData = $request->validate([
                    'archive_uuid' => 'required|uuid',
                    'holding_uuid' => 'required|uuid',
                ]);

                $holding = Holding::where("uuid", $request->holding_uuid)->get()->first();
                if(!$holding) {
                    abort(response()->json(["error" => 424, "message" => "No such Holding: {$request->holding_uuid}"], 424));
                }
                $bag->storage_properties->update(array_merge($validatedData, ["holding_name" => $holding->title]));
                $resultBag =
                    Bag::query()
                        ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
                        ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
                        ->leftJoin('holdings', 'holdings.uuid', 'storage_properties.holding_uuid')
                        ->select('bags.*', 'archives.title AS archive_title',
                            'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid')
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
            ->leftJoin( 'holdings', 'holdings.uuid', 'storage_properties.holding_uuid' )
            ->select( 'bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid' )
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
        /*TODO: Move to AIP        
            $result = $files->map( function ($file) {
            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
            return collect(["fupath" => $file->uuid.".".$ext])->merge($file);
        });
        */
        return new FileCollection($files);
    }

    public function showFile(File $file)
    {
        return $file->toArray();
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
              ->leftJoin('holdings', 'holdings.uuid', 'storage_properties.holding_uuid')
              ->leftJoin('bag_job', 'bags.id', '=', 'bag_job.bag_id')
              ->leftJoin('jobs', 'bag_job.job_id', '=', 'jobs.id')
              ->select('bags.*', 'archives.title AS archive_title',
              'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid')
              ->distinct()->latest();

          return new BagCollection(
              $q->paginate( env("DEFAULT_ENTRIES_PER_PAGE") )
          );
        }


        $q->leftJoin('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
          ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
          ->leftJoin('holdings', 'holdings.uuid', 'storage_properties.holding_uuid')
          ->leftJoin('bag_job', 'bags.id', '=', 'bag_job.bag_id')
          ->leftJoin('jobs', 'bag_job.job_id', '=', 'jobs.id')
          ->select('bags.*', 'archives.title AS archive_title',
          'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid')
          ->distinct();
        if( $request->has( 'holding' ) ) {
            $q->where( 'holding_uuid', $request->query( 'holding' ) );
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

        if( $request->filled( "archive_uuid" ) && $request->filled( "holding_uuid" ) )
        {
            $validatedData = $request->validate([
                'archive_uuid' => 'required|uuid',
                'holding_uuid' => 'required|uuid',
            ]);

            $holding = Holding::where("uuid", $request->holding_uuid)->get()->first();
            if(!$holding) {
                abort(response()->json(["error" => 424, "message" => "No such Holding: {$request->holding_uuid}"], 424));
            }
            $bag->storage_properties->update(array_merge($validatedData, ["holding_name" => $holding->title]));
        }

        $resultBag = Bag::with([ 'files' ])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->leftJoin('archives', 'archives.uuid', 'storage_properties.archive_uuid')
            ->leftJoin('holdings', 'holdings.uuid', 'storage_properties.holding_uuid')
            ->where('bags.id','=', $id)
            ->select('bags.*', 'archives.title AS archive_title',
                'archives.uuid AS archive_uuid', 'holdings.uuid AS holding_uuid')
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
            $this->setMetadata($bag);
            $bag->applyTransition('close');
            $bag->save();
            $bag->storage_properties->update(["name" => $bag->name]);
            Log::debug("closed bag with id " . $bag->id);
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
            $this->setMetadata($bag);
            $bag->applyTransition('close');
            $bag->save();
            $bag->storage_properties->update(["name" => $bag->name]);
            Log::debug("closed bag with id " . $bag->id);
        } catch (BagTransitionException $e) {
            abort(501, "Caught an exception closing bag with id " . $bag->id . ". Exception: {$e}");
            Log::debug("Caught an exception closing bag with id " . $bag->id . ". Exception: {$e}");
        }
    }

    private function setMetadata( Bag $bag) {
        //TODO: Get the metadata in the bag

        $holding = Holding::where('uuid', $bag->storage_properties->holding_uuid)->get()->first();
        if(!$holding) {
            abort(response()->json(["error" => 424, "message" => "No such Holding: {$bag->storage_properties->holding_uuid}"], 424));
        }
        $archive = $holding->owner_archive;
        if(!$archive) {
            abort(response()->json(["error" => 424, "message" => "No archive"], 424));
        }
        $account = $archive->account;
        if(!$account) {
            abort(response()->json(["error" => 424, "message" => "No account"], 424));
        }
        $metadata = [
            'account' => $account->defaultMetadataTemplate,
            'archive' => $archive->defaultMetadataTemplate,
            'holding' => $holding->defaultMetadataTemplate,
        ];
        $bag->metadata = $metadata;
        if(!$bag->save())
            abort(response()->json(["error" => 424, "message" => "Failed to save bag metadata"], 424));
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

    private function validateFailes($name, $rule = null) : bool {
        if(!preg_match($rule ?? $this->nameValidationRule, $name, $matches, PREG_OFFSET_CAPTURE)) {
            return true;
        }
        return false;
    }
}
