@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__('access.retrieve.downloadable')}}</h1>

        <ready-for-download/>
    </div>

@endsection


