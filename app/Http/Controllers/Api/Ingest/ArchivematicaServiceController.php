<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Http\Controllers\Controller;
use App\ArchivematicaService;
use Illuminate\Http\Request;

class ArchivematicaServiceController extends Controller
{
    /**
     * Display a listing of known Archivematica Instances
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $all = ArchivematicaService::all();

        return response($all, 200)
            ->header("Content-type","application/json");
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
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function show(ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function edit(ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArchivematicaService $archivematicaService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ArchivematicaService  $archivematicaService
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArchivematicaService $archivematicaService)
    {
        //
    }
}
