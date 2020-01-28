@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
<div>
        <div class="row">
            <div class="col-sm-12 mb-5">
                <h1><i class="fas fa-cog titleIcon"></i>{{__("User Settings")}}</h1>
            </div>
        </div>
        <form method="POST" action="settings" onchange="submit()">
            @csrf
            <div class="form-group row">
                <label class="col-sm-3" for="interfaceLanguage">{{__("User interface language")}}</label>
                <select class="selectpicker col-sm-4" name="interfaceLanguage" id="interfaceLanguage" >
                    <option value="en" {{ $settings->interfaceLanguage == 'en' ? 'selected = "true"' : ''}}>English</option>
                    <option value="nb_no" {{ $settings->interfaceLanguage == 'nb_no' ? 'selected = "true"' : ''}}>Norsk Bokmål</option>
                </select>
            </div>

            @if( $singleFileIngestOption )
            <div class="row mt-5">
                <label class="form-check-label col-sm-3" for="ingestCompoundModeEnabled">{{__("Ingest mode")}}</label>
                <div class="col-sm-4 btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-light btn-secondary {{ $settings->ingestCompoundModeEnabled ? 'active' : '' }} ">
                        <input type="radio" name="ingestCompoundMode" id="ingestCompoundMode" value="compound">{{__("Compound")}}</input>
                    </label>
                    <label class="btn btn-light btn-secondary {{ $settings->ingestCompoundModeEnabled ? '' : 'active' }} ">
                        <input type="radio" name="ingestCompoundMode" id="ingestCompoundMode" value="single">{{__("Single file")}}</input>
                    </label>
                </div>
            </div>
            @endif

            <div class="form-group row mt-5">
                <label class="col-sm-3" for="defaultAipStorageLocation">{{__("Default AIP storage location")}}</label>
                <select class="selectpicker col-sm-4" name="defaultAipStorageLocation" id="defaultAipStorageLocation" >
                    @foreach ( $aipStorageLocations as $id => $name )
                        <option value="{{$id}}"  {{$id == $settings->defaultAipStorageLocationId ? 'selected = "true"' : ''}}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-3" for="defaultDipStorageLocation">{{__("Default DIP storage location")}}</label>
                <select class="selectpicker col-sm-4" name="defaultDipStorageLocation" id="defaultAipStorageLocation" >
                    @foreach ( $dipStorageLocations as $id => $name )
                        <option value="{{$id}}"  {{$id == $settings->defaultDipStorageLocationId ? 'selected = "true"' : ''}}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group row">
                <label class="col-sm-3" for="ingestMetadataAsFile">{{__("Ingest metadata as file")}}</label>
                <select class="selectpicker col-sm-4" name="ingestMetadataAsFile" id="ingestMetadataAsFile" >
                    <option value="false" {{ $settings->ingestMetadataAsFile ? 'selected = "true"' : ''}}>Disabled</option>
                    <option value="true" {{ $settings->ingestMetadataAsFile ? 'selected = "true"' : ''}}>Enabled</option>
                </select>
            </div>

        </form>
    </div>
</div>
@endsection


