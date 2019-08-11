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
            <div class="col-3">
                <label for="holdingPicker" class="col-form-label-sm">
                    {{__('access.browse.selectHolding')}}
                </label>
                <select id="holdingPicker" class="selectpicker">
                    <option>Space Exploration</option>
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

            <div class="col-5">
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
            <div class="col-3">
                <fond-select></fond-select>
            </div>
            <div class="col-6">
                <div>
                    <div class="row plist">
                        <div class="col-1"><input type="checkbox" id="fil1uuid" class="checkbox"></div>
                        <div class="col">
                            Filnavn1
                        </div>
                        <div class="col-3">
                            303
                        </div>
                        <div class="col-2 listActionItems">
                            <i class="fas fa-eye"></i>&nbsp;
                            <i class="fas fa-trash-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-3 listActionItems">
                <ul>
                    <li><a href="#">Download</a></li>
                    <li><a href="#">Add for retrieval</a></li>
            </div>

        </div>



    </div>
@endsection
