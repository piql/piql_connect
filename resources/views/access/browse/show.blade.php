@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')
@if (false)
        <div class="row">
            <i class="fas fa-hdd titleIcon"></i>
            <div class="col-sm-2 pageHeaderText">{{__('access.browse')}}</div>
            <div class="col-sm-6 text-center ingressText"> 
                {{__('access.browse.ingress')}}
            </div>
        </div>
@endif
        <browse></browse>
@endsection
