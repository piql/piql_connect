@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>{{ __('ingest.offlineStorage') }}
@endsection

@section('content')
    <div class="contentContainer">
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: true, metadata: true, config: true, delete: true, defaultAction: true}"/>
    </div>
@endsection
