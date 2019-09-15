<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Controller;
use App\Bag;
use App\File;
use App\User;
use App\Job;
use App\Holding;
use App\StorageProperties;
use App\Http\Resources\BagResource;
use App\Http\Resources\BagCollection;
use Response;
use App\Events\BagFilesEvent;
use Carbon\Carbon;
use Log;

class BagController extends Controller
{
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
        $bag = User::first()->bags()->latest()->first(); //TODO: Authenticated user!
        if($bag == null){
            $bagName = Carbon::now()->format("YmdHis");
            $bag = Bag::create(['name' => $bagName, 'owner' => User::first()->id]);
            $bag->fresh();
            $bag->storage_properties()->update(['archive_uuid' => Holding::first()->uuid, 'holding_name' => Holding::first()->fonds()->first()->title]);
        }

        $resultBag = Bag::with(['files'])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
            ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
            ->where('bags.id','=', $bag->id)
            ->select('bags.*', 'holdings.title AS archive_title',
                'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
            ->first();


        return new BagResource($resultBag);
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
    public function store(Request $request)
    {
        $bagName = trim($request->bagName);
        if(empty($bagName))
        {
            $bagName = Carbon::now()->format("YmdHis");
        }
        $bag = new Bag();
        $bag->name = $bagName;
        $bag->owner = $request->userId;
        if($bag->save()){
            if($request->filled('archive_uuid') && $request->filled('holding_name') )
            {
                $bag->storage_properties->update([
                    'archive_uuid' => $request->archive_uuid,
                    'holding_name' => $request->holding_name
                ]);

                $resultBag =
                    Bag::query()
                        ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
                        ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
                        ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
                        ->select('bags.*', 'holdings.title AS archive_title',
                            'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
                        ->find($bag->id);

                return new BagResource($resultBag);
            }
            else
            {
                $resultBag =
                    Bag::query()
                        ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
                        ->select('bags.*')
                        ->find($bag->id);
                return new BagResource($resultBag);
            }
        }

        abort(501, "Could not create bag with name ".$bagName." and owner ".$request->userId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
   {
        $bag = Bag::with(['files'])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
            ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
            ->select('bags.*', 'holdings.title AS archive_title',
                'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
                ->find($id);
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
        $files = Bag::find($id)->files()->get();
        $result = $files->map( function ($file) {
            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
            return collect(["fupath" => $file->uuid.".".$ext])->merge($file);
        });
        return Response::json($result);
    }

    public function complete()
    {
        return Response::json(Bag::latest()->where('status', '=', 'complete')->get());
    }

    public function processing()
    {
        $bags = Bag::latest()
            ->where(  'status', '=', 'closed')
            ->orWhere('status', '=', 'bag_files')
            ->orWhere('status', '=', 'move_to_outbox')
            ->orWhere('status', '=', 'initiate_transfer')
            ->orWhere('status', '=', 'approve_transfer')
            ->orWhere('status', '=', 'transferring')
            ->orWhere('status', '=', 'ingesting')
            ->orWhere('status', '=', 'complete')
            ->paginate(6);
        foreach($bags as $bag) {
            $bag->status = __('ingest.processing.status.'.$bag->status);
        }
        return Response::json($bags);
    }

    public function all(Request $request)
    {
        $q = Bag::query();
        if(empty($request->query())){
            $q->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
              ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
              ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
              ->join('bag_job', 'bags.id', '=', 'bag_job.bag_id')
              ->join('jobs', 'bag_job.job_id', '=', 'jobs.id')
              ->select('bags.*', 'holdings.title AS archive_title',
              'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
              ->distinct()->latest();

            return new BagCollection($q->paginate(7));
        }


        $q->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
          ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
          ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
          ->join('bag_job', 'bags.id', '=', 'bag_job.bag_id')
          ->join('jobs', 'bag_job.job_id', '=', 'jobs.id')
          ->select('bags.*', 'holdings.title AS archive_title',
          'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
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
        $q->paginate(7);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bag = Bag::find($id);
        if($request->filled("bagName"))
        {
            $bag->update(['name' => $request->bagName]);
        }

        if($request->filled("archive_uuid") && $request->filled("holding_name"))
        {
            $bag->storage_properties->update([
                'archive_uuid' => $request->archive_uuid, 
                'holding_name' => $request->holding_name
            ]);
        }

        $resultBag = Bag::with(['files'])
            ->join('storage_properties', 'storage_properties.bag_uuid', 'bags.uuid')
            ->join('holdings', 'holdings.uuid', 'storage_properties.archive_uuid')
            ->join('fonds', 'fonds.title', 'storage_properties.holding_name')
            ->where('bags.id','=', $id)
            ->select('bags.*', 'holdings.title AS archive_title',
                'holdings.uuid AS archive_uuid', 'fonds.title AS holding_name')
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

        try {
            $bag->applyTransition('close');
            Log::debug("emitting ProcessFilesEvent for bag with id " . $id);
            event(new BagFilesEvent($bag));
        } catch (BagTransitionException $e) {
            abort(501, "Caught an exception closing bag with id " . $id . ". Exception: {$e}");
            Log::debug("Caught an exception closing bag with id " . $id . ". Exception: {$e}");
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


}
