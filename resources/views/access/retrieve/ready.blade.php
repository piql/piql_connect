@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__('access.retrieve.readyToRetrieve')}}</h1>

        <em>
            {{ __('access.ready.ingress') }}
        </em><br/>
        <ready-to-retrieve/>
    </div>

@endsection


