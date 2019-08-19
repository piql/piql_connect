<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Bag;
use App\File;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Events\BagFilesEvent;
use Carbon\Carbon;

class BagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Log::debug("Bag index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Log::debug("Bag create");
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
            Log::info("Created bag with name ".$bagName." and id ".$bag->id);
            return response()->json(['id' => $bag->id, 'name' => $bagName]);
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
        Log::debug("Bag show");
        return Response::json(Bag::find($id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFiles($id)
    {
        //Log::debug("Bag show files");
        return Response::json(Bag::find($id)->files()->get());
    }

    public function complete()
    {
        Log::debug("Bags complete - needs pagination!");
        return Response::json(Bag::latest()->where('status', '=', 'complete')->get());
    }

    public function processing()
    {
        Log::debug("Bags in processing - needs pagination!");
        return Response::json(Bag::latest()->where('status', '=', 'ingesting')->get());
    }
  
    public function all()
    {
        Log::debug("Bag all - needs pagination!");
        return Response::json(Bag::paginate(5));
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
        if($request->filled("bagName"))
        {
            Log::debug("Bag update");
            $bag = Bag::find($id);
            $bag->name = $request->bagName;
            $bag->save();
            $bag->fresh();
            return Response::json($bag);
        }
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
        Log::debug("emitting ProcessFilesEvent for bag with id ".$id);
        event( new BagFilesEvent($id) );
    }
    
    public function piqlIt($id)
    {
        $bag = Bag::find($id);
        $bag->status = "piqld";
        $bag->save();
        return $bag;
    }
}
