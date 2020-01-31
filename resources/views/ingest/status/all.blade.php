@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="mb-2 mt-2">
        <div class="row">
          <div class="col-sm-1 text-left">
              <i class="fas fa-clipboard-check titleIcon"></i>
          </div>
          <div class="col-sm-6 text-left">
              <h1>{{__("ingest.offlineStorage.status.header")}}</h1>
          </div>
        </div> 
        <div class="row">
           <div class="col-sm-1"></div>
           <div class="col-sm-6 text-left">
                <div class="ingressText">{{__("ingest.offlineStorage.status.ingress")}}</div>
           </div>
      </div>
    </div>



        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
@endsection
