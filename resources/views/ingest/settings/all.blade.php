@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-cogs titleIcon"></i>Ingest Settings
@endsection

@section('content')
    <div class="contentContainer">
        Available Archivematica instances
        <Services></Services>
    </div>
@endsection
