@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-clipboard-check titleIcon"></i>{{__('ingest.offlineStorage.status.header')}}
@endsection

@section('content')
    <div class="contentContainer">
        <div class="col-6">
            <em class="mb-3 mt-2">
            {{__('ingest.offlineStorage.status.ingress')}}
            </em>
        </div>
        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
    </div>
@endsection
