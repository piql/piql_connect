@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("User Settings")}}</h1>

        <div class="container-fluid">
        <form method="POST" action="settings">
            @csrf
            <div class="form-group row">
                <label class="col-sm-5" for="interfaceLanguage">{{__("User interface language")}}</label>
                <select class="selectpicker col-sm-5" name="interfaceLanguage" id="interfaceLanguage" >
                    <option value="en" {{ $settings->interfaceLanguage == 'en' ? 'selected = "true"' : ''}}>English</option>
                    <option value="nb_no" {{ $settings->interfaceLanguage == 'nb_no' ? 'selected = "true"' : ''}}>Norsk Bokmål</option>
                </select>
            </div>

            <div class="form-group row mt-5">
                <label class="col-sm-5" for="defaultAipStorageLocation">{{__("Default AIP storage location")}}</label>
                <select class="selectpicker col-sm-5" name="defaultAipStorageLocation" id="defaultAipStorageLocation" >
                    @foreach ( $aipStorageLocations as $id => $name )
                        <option value="{{$id}}"  {{$id == $settings->defaultAipStorageLocationId ? 'selected = "true"' : ''}}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-5" for="defaultDipStorageLocation">{{__("Default AIP storage location")}}</label>
                <select class="selectpicker col-sm-5" name="defaultDipStorageLocation" id="defaultAipStorageLocation" >
                    @foreach ( $dipStorageLocations as $id => $name )
                        <option value="{{$id}}"  {{$id == $settings->defaultDipStorageLocationId ? 'selected = "true"' : ''}}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group row mt-5">
                <input type="submit" class="btn btn-lg" value="{{__("Save")}}">
            </div>
        </form>
    </div>
</div>

@endsection


