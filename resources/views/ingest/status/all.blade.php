@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="mb-5">
        <div class="row">
          <div class="col-sm-1 text-right">
              <i class="fas fa-clipboard-check titleIcon"></i>
          </div>
          <div class="col-sm-6 text-left">
              <h1> Status</h1>
          </div>
        </div> 
        <div class="row">
           <div class="col-sm-1"></div>
           <div class="col-sm-6 text-left">
                <div>Monitor the archival workflow for piqlFilm.</div>
           </div>
      </div>
    </div>



        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
@endsection
