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
        <form>
        <div class="container-fluid pt-4">
            <div class="row d-flex flex-row align-items-baseline pb-1">
                    <div class="small pr-2" > {{__('ingest.metadata.editFile.fileName')}}</div>
                    <div class="text-nowrap" id="m_filename"> {{ isset($file) ? $file->filename : $job->name }}</div>
            </div>
        </div>
        <div class="row">
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
    </div>
    </form>
    <div class="row mt-1">
        <div class="col-sm-3"></div>
        <div class="col-sm-8 p-4 right">
            <a @if( isset( $fromUpload ) ) href="{{ route('upload') }} " @else  href="{{ URL::previous() }}" @endif >
                <button class="btn btn p-3 mr-5 w-300">Cancel</button>
            </a>
            <a href="{{ URL::previous() }}">
                <button class="btn btn-ln btn-default w-300 p-3" @if( $readonly ) class="btn-disabled" @endif>Save</button>
            </a>
        </div>
    </div>
</div>
@endsection
