@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("Create Holdings")}}</h1>
        <p class="mb-5 ml-3">Create holdings and assign fonds to them here to plan the preservation structure of your archives.</p>

        <holdings-settings></holdings-settings>
    </div>

@endsection


