@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("Manage Fonds for Holdings")}}</h1>

        <holdings-fonds-settings></holdings-fonds-settings>
    </div>

@endsection


