@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
@endsection

@section('content')
    <job-list :job-list-url="'/api/v1/ingest/offline_storage/pending'" :action-icons="{list: true, metadata: true, config: true, delete: true, defaultAction: true}"/>
@endsection
