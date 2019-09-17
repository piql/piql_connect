@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>{{ __('ingest.offlineStorage.header') }}
@endsection

@section('content')
    <div class="contentContainer">
        <em>
            {{__('ingest.offlineStorage.ingress')}}
        </em>
        <br/>
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: true, metadata: true, config: true, delete: true, defaultAction: true}"/>
    </div>
@endsection
