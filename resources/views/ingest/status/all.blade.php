@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-clipboard-check titleIcon"></i>STATUS
@endsection

@section('content')
    <div class="contentContainer">

        <div>
            <div class="row listFilter">
                <form>
                    <label for="fromDate">{{__('ingest.status.from')}}</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">{{__('ingest.status.to')}}</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">{{__('ingest.status.status')}}</option>
                        <option>{{__('ingest.status.encodingFilter')}}</option>
                        <option>{{__('ingest.status.writingFilter')}}</option>
                        <option>{{__('ingest.status.developingFilter')}}</option>
                    </select>
                    <input type="search" :placeholder="$t('search')">
                </form>
            </div>
            <br/>
            <div class="row plistHeader">
                <div class="col">{{__('ingest.status.jobId')}}</div>
                <div class="col">{{__('ingest.status.bags')}}</div>
                <div class="col">{{__('ingest.status.creationDate')}}</div>
                <div class="col">{{__('ingest.status.status')}}</div>
            </div>

            <div>
                <div class="row plist">
                    <div class="col">
                        3a070c3d-10b1-4188-b877-4c7a0d730b05 
                    </div>
                    <div class="col">
                        1
                    </div>
                    <div class="col">
                        {{ $bag->created_at }}
                    </div>
                    <div class="col">
                        Encoding
                    </div>
                </div>
            </div>
        
        </div>
    
    </div>
@endsection
