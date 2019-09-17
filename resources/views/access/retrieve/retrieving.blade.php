@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__('access.retrieve.isRetrieving')}}</h1>

    <now-retrieving/>

    </div>

@endsection


