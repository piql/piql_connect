@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')
    <div class="content">
        <i class="fas fa-hdd titleIcon"></i>
        <h1>{{__('Browse')}}</h1>
        <em>{{__('access.browse.ingress')}}</em>
        <br/>


        <form class="form-inline mb-5">
                <div class="col-2">
                    <label for="holdingPicker" class="col-form-label-sm">
                        {{__('access.browse.selectHolding')}}
                    </label>
                    <select id="holdingPicker" class="selectpicker">
                        <option>Main </option>
                        <option>Customer relations</option>
                        <option>Attic</option>
                    </select>
            </div>

                <div class="col-2">
                    <label for="fromDate" class="col-form-label-sm">{{__('Archived from')}}</label>
                    <input id="fromDate" type="date" class="pl-5 form-control">
                </div>
                <div class="col-2">
                    <label for="toDate" class="col-form-label-sm">{{__('Archived to')}}</label>
                    <input id="toDate" type="date" class="pl-5 form-control">
                </div>

                <div class="col-6">
                    <label for="searchContents" class="col-form-label-sm">{{__('With contents')}}</label>
                    <div class="input-group">
                        <div class="input-group addon ">
                            <input id="searchContents" type="text" class="form-control">
                            <span class="input-group-addon">
                                <i class="fas fa-search search-icon-inline"></i>
                            </span>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row mb-5">

                <div class="col-sm-3">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center active">
                            Text documents
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Audio
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Video
                        </li>
                    </ul>
                </div>
            </div>



    </div>
@endsection
