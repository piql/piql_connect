@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>{{ __('ingest.offlineStorage.header') }}
@endsection

@section('content')
    <div class="contentContainer">
        <div class="col-8">
            <em class="mb-3 mt-2">
                {{__('ingest.offlineStorage.ingress')}}
            </em>
        </div>
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: true, metadata: true, config: true, delete: true, defaultAction: true}"/>
    </div>
@endsection
