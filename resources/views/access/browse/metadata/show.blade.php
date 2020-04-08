@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
        @isset($file)
            <i class="fas fa-tags titleIcon"></i>{{__('access.browse.metadata.editFile')}}
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
    @endisset
    @isset($job)
        <div class="col-8">
            <em class="mb-3 mt-1">
                {{__('ingest.offlineStorage.metadata.ingress')}}
            </em>
        </div>
        <div class="col-8">
        {{ /* Breadcrumbs::render('offline_storage_metadata_view', $job) */}}
        </div>
    @endisset
        <div class="col-12">
        <form>
        <div class="container-fluid pt-4 mb-5">
            <div class="row d-flex flex-row align-items-baseline pb-1">
                    <div class="small pr-2" > {{__('ingest.metadata.editFile.fileName')}}</div>
                    <div class="text-nowrap" id="m_filename"> {{ $file->filename }} </div>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_title">Title</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_title" value="{{ $dc['title'] }}"  >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_author">Author</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_author" value="{{ $dc['creator'] }}" >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_subject">Subject</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_subject" value="{{ $dc['subject'] }}" >
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_description">Description</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_description" value="{{ $dc['description'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_publisher">Publisher</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_publisher" value="{{ $dc['publisher'] }}" >
                </div>
            </div>
        </div>

        <div class="col-sm-4">

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_date">Date</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_date" value="{{ $dc['date'] }}" >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_language">Language</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_language" value="{{ $dc['language'] }}" >
                </div>
            </div>



            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_resourcetype">Resource type</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_resourcetype" value="{{ $dc['type'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_resourceidentifier">Resource Identifier</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_resourceidentifier" value="{{ $dc['identifier'] }}" >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_otherContributor">Other Contributor</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_othercontributor" value="{{ $dc['contributor'] }}" >
                </div>
            </div>
        </div>
        <div class="col-sm-4">

            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_format">Format</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_format" value="{{ $dc['format'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_source">Source</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_source" value="{{ $dc['source'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_relation">Relation</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_relation" value="{{ $dc['relation'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_coverage">Coverage</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_coverage" value="{{ $dc['coverage'] }}" >
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-10 form-group mb-2">
                    <label class="small"  for="m_Rights Management">Rights Management</label>
                    <input  @if ($readonly) readonly @endif class="form-control input-sm" type="text" id="m_rightsmanagement" value="{{ $dc['rights'] }}" >
                </div>
            </div>
        </div>
    </div>
    </form>


    <button class="btn btn-primary btn-lg float-right mt-5" onclick="window.history.go(-1); return false;" >BACK</button>
</div>
@endsection
