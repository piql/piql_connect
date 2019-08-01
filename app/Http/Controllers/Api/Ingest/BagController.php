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
        Log::debug("Creating new bag with name ".$request->bagName.".");
        $bagName = trim($request->bagName);
        if(empty($bagName))
        {
            $bagName = Carbon::now()->format("YmdHis");
            Log::debug("Creating bag with name ".$bagName);
        }
        Bag::create(['name' => $bagName, 'owner' => $request->userId ]);
        $bag = Bag::latest()->first();
        Log::debug("Bag with id ".$bag->id." created.");
        return $bag;
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
        Log::debug("Bag show files");
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
        return Response::json(Bag::latest()->all());
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
        Log::debug("Bag update");
        if($request->filled("bagName"))
        {
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
        Log::debug("emitting ProcessFilesEvent");
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