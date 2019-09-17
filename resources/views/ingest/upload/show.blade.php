@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-upload titleIcon"></i>{{__("upload.header")}}
@endsection

@section('content')
    <div class="content">
        <Upload><button class="browse">{{__("Add files")}}</button></Upload>

    </div>
@endsection
