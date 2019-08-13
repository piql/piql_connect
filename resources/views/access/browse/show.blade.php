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

        <form class="form mb-5">
            <div class="row">
                <div class="col-2">
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
                    <input id="fromDate" type="date" class="pl-2 form-control">
                </div>
                <div class="col-2">
                    <label for="toDate" class="col-form-label-sm">{{__('Archived to')}}</label>
                    <input id="toDate" type="date" class="pl-2 form-control">
                </div>

                <div class="col-3 form-group">
                    <label for="searchContents" class="col-form-label-sm">{{__('With contents')}}</label>
                    <div class="input-group">
                        <div class="input-group addon">
                            <input id="searchContents" type="text" class="form-control">
                            <span class="input-group-addon">
                                <i class="fas fa-search search-icon-inline"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    &nbsp;
               </div>

            </div>
        </form>

        <hr class="row m-0">
        <div class="row">
            <div class="col-2 mt-3">
                <fond-select></fond-select>
            </div>
            <div class="col-8">
                @if(false)
                <browser-list><browser-list>
                @endif
                @if(true)
                    <identity></identity>
                @endif
            </div>
            <div class="col mt-3">
                <primary-contact></primary-contact>
            </div>

        </div>

    </div>
@endsection
