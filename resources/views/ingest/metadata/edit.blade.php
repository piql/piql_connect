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
    <!--div>
        <a href="{{ URL::previous() }}">
            <i class="breadcrumbs noTextTransform">&lt;&lt; Back</i>
        </a>
    </div-->

    <div class="container-fluid">
        <form class="form">
        <div class="row">
            <p class="col-sm-2 mr-4 mt-2">
                {{__('ingest.metadata.editFile.fileName')}}:</p>
            <em class="col"> {{ $file->filename}}</em>
        </div>

        <div class="row">
                <label for="m_title">Title</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_title">
            </div>
        </div>

        <div class="row">
                <label for="m_author">Author</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_author">
            </div>
        </div>

        <div class="row">
                <label for="m_subject">Subject</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_subject">
            </div>
        </div>

        <div class="row">
                <label for="m_description">Description</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_description">
            </div>
        </div>


        <div class="row">
                <label for="m_publisher">Publisher</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_publisher">
            </div>
        </div>


        <div class="row">
                <label for="m_date">Date</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_date">
            </div>
        </div>


        <div class="row">
                <label for="m_resourcetype">Resource type</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_resourcetype">
            </div>
        </div>


        <div class="row">
                <label for="m_resourceidentifier">Resource Identifier</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_resourceidentifier">
            </div>
        </div>


        <div class="row">
                <label for="m_language">Language</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_language">
            </div>
        </div>


        <div class="row">
                <label for="m_otherContributor">Other Contributor</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_othercontributor">
            </div>
        </div>


        <div class="row">
                <label for="m_format">Format</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_format">
            </div>
        </div>


        <div class="row">
                <label for="m_source">Source</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_source">
            </div>
        </div>


        <div class="row">
                <label for="m_relation">Relation</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_relation">
            </div>
        </div>


        <div class="row">
                <label for="m_coverage">Coverage</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_coverage">
            </div>
        </div>


        <div class="row">
                <label for="m_Rights Management">Rights Management</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="m_rightsmanagement">
            </div>
        </div>





        <div class="row mt-0">
            <div class="col-sm-2">&nbsp;</div>
            <div class="col p-4">
            <a href="{{ URL::previous() }}">
                <button class="btn p-3">Cancel</button>
                <button class="btn p-3">Save</button>
            </a>
            </div>
        </div>
        </form>
    </div>

@endsection
