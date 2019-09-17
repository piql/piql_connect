@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("Create Archives")}}</h1>
        <p class="mb-5 ml-3">Create archives and assign holdings to them here to plan the preservation structure of your archives.</p>

        <holdings-settings></holdings-settings>
    </div>

@endsection


