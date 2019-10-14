<?php

namespace App\Http\Controllers\Api\Planning;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Holding;
use App\Http\Resources\HoldingResource;
use App\Http\Resources\HoldingCollection;

class HoldingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new HoldingCollection( Holding::all() );
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'owner_archive_uuid' => 'required|uuid|exists:archives,uuid',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding'
        ]);

        if( $validator->fails() )
        {
            $message = $validator->errors();
            return response($message, 422);
        }
        $data = [
            'title' => $request->title,
            'owner_archive_uuid' => $request->owner_archive_uuid,
            'description' => $request->description ?? '',
        ];

        $lhs = $request->lhs;
        $rhs = $request->rhs;

        return new HoldingResource(Holding::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
