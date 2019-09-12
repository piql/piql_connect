@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <div>
        @isset($file)
            <i class="fas fa-tags titleIcon"></i>{{__('ingest.metadata.editFile')}}
        @endisset
        @isset($job)
                <i class="fas fa-tags titleIcon"></i>{{__('ingest.metadata.editJob')}}
        @endisset
    </div>
@endsection

@section('content')
    <!-- for future use -->
    @php
        $readonly = isset($readonly) ? $readonly : false;
    @endphp

    @isset($file)
        @if($readonly)
            <em>{{__('ingest.metadata.showFile.ingress')}}</em>
        @else
            <em>{{__('ingest.metadata.editFile.ingress')}}</em>
        @endif
        <br/>
        {{ Breadcrumbs::render('metadata_view', $file) }}
    @endisset
    @isset($job)
        <em>{{__('ingest.offlineStorage.metadata.ingress')}}</em>
        <br/>
        {{ Breadcrumbs::render('offline_storage_metadata_view', $job) }}
    @endisset
        <form>
        <div class="container-fluid">
         <div class="row">
            <div class="row mb-5">
                <div class="small col-sm-4" > {{__('ingest.metadata.editFile.fileName')}}</div>
                <div class="col-sm-8 text-nowrap" id="m_filename"> {{ isset($file) ? $file->filename : $job->name }}</div>
                </div>
            </div>
</div><div class="row">
            <div class="col-sm-4">
                <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_title">Title</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_title">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_author">Author</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_author">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_subject">Subject</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_subject">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_description">Description</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_description">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_publisher">Publisher</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_publisher">
                </div>
            </div>


</div>
<div class="col-sm-4">

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_date">Date</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_date">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_language">Language</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_language">
                </div>
            </div>

 

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_resourcetype">Resource type</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_resourcetype">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_resourceidentifier">Resource Identifier</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_resourceidentifier">
                </div>
            </div>

           <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_otherContributor">Other Contributor</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_othercontributor">
                </div>
            </div>


</div>
<div class="col-sm-4">

            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_format">Format</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_format">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_source">Source</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_source">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_relation">Relation</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_relation">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_coverage">Coverage</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_coverage">
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-7 form-group mb-2">
                    <label class="small"  for="m_Rights Management">Rights Management</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_rightsmanagement">
                </div>
            </div>
</div>

            @if (!$readonly)
            <div class="row mt-1">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col p-4">
                <a href="{{ URL::previous() }}">
                    <button class="btn p-3">Cancel</button>
                    <button class="btn p-3">Save</button>
                </a>
                </div>
            </div>
            @endif
    </form>
@endsection
