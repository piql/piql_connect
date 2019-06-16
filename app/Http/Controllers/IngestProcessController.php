<?php

namespace App\Http\Controllers;

use App\IngestProcess;
use Illuminate\Http\Request;

class IngestProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ingest.process.all');
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
     * @param  \App\IngestProcess  $ingestProcess
     * @return \Illuminate\Http\Response
     */
    public function show(IngestProcess $ingestProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\IngestProcess  $ingestProcess
     * @return \Illuminate\Http\Response
     */
    public function edit(IngestProcess $ingestProcess)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\IngestProcess  $ingestProcess
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IngestProcess $ingestProcess)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\IngestProcess  $ingestProcess
     * @return \Illuminate\Http\Response
     */
    public function destroy(IngestProcess $ingestProcess)
    {
        //
    }
}
