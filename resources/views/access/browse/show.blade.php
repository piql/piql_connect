@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')
    <div class="content">
        <i class="fas fa-hdd titleIcon"></i>
        <h1>{{__('Browse')}}</h1>
        <em>{{__('access.browse.ingress')}}</em>
        <br/>
        <browse></browse>
    </div>
@endsection
