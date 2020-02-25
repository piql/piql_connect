@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
        @isset($file)
            <i class="fas fa-tags titleIcon"></i>{{__('ingest.metadata.editFile')}}
        @endisset
        @isset($job)
            <i class="fas fa-tags titleIcon"></i>{{__('ingest.metadata.editJob')}}
        @endisset
@endsection

@section('content')
    <div class="contentContainer">
    <!-- for future use -->
    @php
        $readonly = isset($readonly) ? $readonly : false;
    @endphp

    @isset($file)
        <div class="col-8">
            <em class="mb-3 mt-1">
            @if($readonly)
                {{__('ingest.metadata.showFile.ingress')}}
            @else
                {{__('ingest.metadata.editFile.ingress')}}
            @endif
            </em>
        </div>
        @if( empty($fromUpload) )
            {{ Breadcrumbs::render('metadata_view', $file) }}
        @endif
    @endisset
    @isset($job)
        <div class="col-8">
            <em class="mb-3 mt-1">
                {{__('ingest.offlineStorage.metadata.ingress')}}
            </em>
        </div>
        <div class="col-8">
        {{ Breadcrumbs::render('offline_storage_metadata_view', $job) }}
        </div>
    @endisset
        <div class="col-12">
            <div class="container-fluid pt-4">
                <div class="row d-flex flex-row align-items-baseline pb-1">
                    <div class="small pr-2" > {{__('ingest.metadata.editFile.fileName')}}</div>
                    <div class="text-nowrap" id="m_filename"> {{ isset($file) ? $file->filename : $job->name }}</div>
                </div>
            </div>
            <metadata :fileId="'{{$file->id}}'"/>
        </div>
    </div>
@endsection
