@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
@endsection

@section('content')

    @if( $enable_offline_actions )
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: true, metadata: true, config: true, delete: true, defaultAction: true}" />
    @else
        <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: false, metadata: false, config: false, delete: false, defaultAction: false}"/>
    @endif
@endsection
