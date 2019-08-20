@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>Job list
@endsection

@section('content')
    <div class="contentContainer">
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'"/>
    </div>
@endsection
