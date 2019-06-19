<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\Bag;
use Response;
use Illuminate\Support\Facades\Auth;

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
        Log::info("Bag index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Log::info("Bag create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("Creating new bag with name ".$request->bagName.".");
        $bag = Bag::create(['name' => $request->bagName, 'owner' => $request->userId ]);
        Log::info("Bag with id ".$bag->id." created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info("Bag show");
        return Response::json(Bag::find($id));
    }

  
    public function all()
    {
        Log::info("Bag all - needs pagination!");
        return Response::json(Bag::all());
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
        Log::info("Bag edit");
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
        Log::info("Bag edit");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info("Bag destroy");
        //
    }

}
