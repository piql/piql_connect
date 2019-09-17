@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-suitcase titleIcon"></i>{{__('ingest.offlineStorage.aipList.header')}}
@endsection

@section('content')
    <div class="contentContainer">
        <div class="col-6">
            <em class="mb-3 mt-2">
                {{__('ingest.offlineStorage.aipList.ingress')}}
            </em>
        </div>
        <div class="col-8">
            {{ Breadcrumbs::render('aip_list', $job) }}
        </div>

        <bag-list :job-id="'{{ $job->id }}' "></bag-list>
    </div>
@endsection
