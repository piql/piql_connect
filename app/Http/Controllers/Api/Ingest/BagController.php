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
use BagitUtil;

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
        Bag::create(['name' => $request->bagName, 'owner' => $request->userId ]);
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
        return Response::json(Bag::where('status', '=', 'complete')->get());
    }


  
    public function all()
    {
        Log::debug("Bag all - needs pagination!");
        return Response::json(Bag::latest()->where('status', '=', 'created')->get());
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
        //
        Log::debug("Bag edit");
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

    public function preCommit($id)
    {
        $bag = Bag::find($id);
        $bag->status = 'processing';
        $bag->save();
        return $bag;
    }
    
    /**
     * Commit the bag to archivematica
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function commit($id)
    {
        Log::debug("Bag commit: " . $id);

        $bag = Bag::find($id);

        $bagit = new BagitUtil;

        $files = $bag->files;

        // Create bag output dir
        $bagOuputDir = dirname($bag->storagePathCreated());
        if (!is_dir($bagOuputDir))
        {
            if (!mkdir($bagOuputDir))
            {
                Log::error("Failed to create bag output dir: " . $bagOuputDir);
                // \todo Return error
            }
        }

        // Create a bag
        $bagPath = $bag->storagePathCreated();
        foreach ($files as $file)
        {
            $filePath = $file->storagePathCompleted();
            $bagit->addFile($filePath, $file->filename);
        }
        if (!$bagit->createBag($bagPath))
        {
            Log::error("Failed to create bag: " . $bagit->errorMessage());
            // \todo Return error
        }

        // Change bag status
        $bag->status = 'processing';
        $bag->save();

        Log::debug("Finished");
    }
    
    public function piqlIt($id)
    {
        $bag = Bag::find($id);
        $bag->status = "piqld";
        $bag->save();
        return $bag;
    }
}
