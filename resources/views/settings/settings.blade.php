@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__("User Settings")}}</h1>

        <form method="POST" action="settings">
            @csrf
            <div class="form-group">
                <label for="interfaceLanguage">{{__("User interface language")}}</label>
                <select class="selectpicker" name="interfaceLanguage" id="interfaceLanguage" >
                    <option value="en" {{ $interfaceLanguage == 'en' ? 'selected = "true"' : ''}}>English</option>
                    <option value="nb_no" {{ $interfaceLanguage == 'nb_no' ? 'selected = "true"' : ''}}>Norsk Bokmål</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-lg" value="{{__("Save")}}">
            </div>
        </form>
    </div>

@endsection


