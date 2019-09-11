@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-suitcase titleIcon"></i>{{__('ingest.offlineStorage.aipList.header')}}
@endsection

@section('content')
    <div class="contentContainer">
        <em>
            {{__('ingest.offlineStorage.aipList.ingress')}}
        </em>
        {{ Breadcrumbs::render('aip_list', $job) }}

        <bag-list :job-id="'{{ $job->id }}' "></bag-list>
    </div>
@endsection
