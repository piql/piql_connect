<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $bag = Bag::create(['name' => $request->bagName, 'owner' => $request->userId ]);
        Log::debug("Bag with id ".$bag->id." created.");
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


  
    public function all()
    {
        Log::debug("Bag all - needs pagination!");
        return Response::json(Bag::where('status', '=', 'created')->get());
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

    
    /**
     * Commit the bag to archivematica
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function commit($id)
    {
        Log::debug("Bag commit");

        $bag = Bag::find($id);

        $bagit = new BagitUtil;

        $files = File::where('bag_id', '=', $bag->id)->get();

        // Create bag output dir
        $bagOuputDir = '/tmp/bags';
        if (!is_dir($bagOuputDir))
        {
            if (!mkdir($bagOuputDir))
            {
                // \todo Return error
            }
        }
    
        // Create a bag
        $bagPath = $bagOuputDir . '/' . $bag->name . '-' . $bag->uuid . '.zip';
        $tmpDir = sys_get_temp_dir() . '/' . substr(md5(rand()), 0, 7);
        mkdir($tmpDir);
        foreach ($files as $file)
        {
            // Generate fake files
            $filePath = $tmpDir . '/' . $file->filename;
            $fp = fopen($filePath, 'w');
            fseek($fp, 154658-1,SEEK_CUR);
            fwrite($fp,'a');
            fclose($fp);

            // \todo Files are fake - use files from upload
            $bagit->addFile($filePath);
        }
        if (!$bagit->createBag($bagPath))
        {
            // \todo Return error
        }

        // Change bag status
        $bag->status = 'processing';
        $bag->save();

        // \todo We fake AIP creation - it should be done by archivematica
        sleep(60);

        // Change bag status
        $bag->status = 'finished';
        $bag->save();
    }
}
