@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("Settings for Holdings and Fonds")}}</h1>

        <holdings-fonds-settings></holdings-fonts-settings>
    </div>

@endsection


