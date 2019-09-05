@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-clipboard-check titleIcon"></i>STATUS
@endsection

@section('content')
    <div class="contentContainer">
        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
    </div>
@endsection
