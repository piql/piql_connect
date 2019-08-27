@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <div>
        <i class="fas fa-tags titleIcon"></i>{{__('ingest.metadata.editFile')}}
    </div>
@endsection

@section('content')
    <!-- for future use -->
    <em>{{__('ingest.metadata.editFile.ingress')}}</em>
    <div>
        <a href="{{ URL::previous() }}">
            <i class="breadcrumbs noTextTransform">&lt;&lt; Back</i>
        </a>
    </div>

    <div class="container-fluid">
        <div class="row"><h4 class="col-sm-2 mr-4">{{__('ingest.metadata.editFile.fileName')}}:</h4><h4 class="col"> {{ $file->filename}}</h4></div>
        <div class="row">
            <div class="col-sm-5">
                <label for="m_title">Title</label>
                <input type="text" id="m_title">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <label for="m_author">Author</label>
                <input type="text" id="m_author">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <label for="m_subject">Subject</label>
                <input type="text" id="m_subject">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <label for="m_description">Description</label>
                <input type="text" id="m_description">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_publisher">Publisher</label>
                <input type="text" id="m_publisher">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_date">Date</label>
                <input type="text" id="m_date">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_resourcetype">Resource type</label>
                <input type="text" id="m_resourcetype">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_resourceidentifier">Resource Identifier</label>
                <input type="text" id="m_resourceidentifier">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_language">Language</label>
                <input type="text" id="m_language">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_otherContributor">Other Contributor</label>
                <input type="text" id="m_othercontributor">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_format">Format</label>
                <input type="text" id="m_format">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_source">Source</label>
                <input type="text" id="m_source">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_relation">Relation</label>
                <input type="text" id="m_relation">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_coverage">Coverage</label>
                <input type="text" id="m_coverage">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-5">
                <label for="m_Rights Management">Rights Management</label>
                <input type="text" id="m_rightsmanagement">
            </div>
        </div>





        <div class="row mt-1">
            <div class="col-sm-2">&nbsp;</div>
            <div class="col p-4">
            <a href="{{ URL::previous() }}">
                <button class="btn p-3">Cancel</button>
                <button class="btn p-3">Save</button>
            </a>
            </div>
        </div>
    </div>

@endsection
