<?php

namespace App\Http\Controllers\Api\Ingest;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Bag;
use App\File;
use App\StorageProperties;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Events\BagFilesEvent;
use Carbon\Carbon;
use App\Http\Resources\BagResource;
use App\Http\Resources\BagCollection;

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
        return new BagResource(Bag::with(['storage_properties', 'files'])->find($bag->id));
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
            $bag->fresh();
            $storage_properties = $bag->storage_properties;
            $storage_properties->archive_uuid = $request->archive_uuid;
            $storage_properties->holding_name = $request->holding_name;
            $storage_properties->save();
            Log::info("Created bag with name ".$bag->name." and id ".$bag->id);
            return new BagResource(Bag::with(['storage_properties', 'files'])->find($bag->id));
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
        $bag = Bag::with(['storage_properties', 'files'])->find($id);
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
        return Response::json(Bag::find($id)->files()->get());
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

    public function all()
    {
        $bags = Bag::latest()->paginate(7);
        return new BagCollection($bags);
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
        $bag = Bag::with(['storage_properties', 'files'])->find($id);
        $storageProperties = StorageProperties::find($bag->storage_properties()->first()->id);

        if($request->filled("bagName"))
        {
            $bag->name = $request->bagName;
            $bag->save();
        }

        if($request->filled("archive_uuid") && $request->filled("holding_name"))
        {

            $storageProperties->archive_uuid = $request->archive_uuid;
            $storageProperties->holding_name = $request->holding_name;
            $storageProperties->save();
        }

        $resultBag = Bag::with(['storage_properties', 'files'])->find($bag->id);
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
}
