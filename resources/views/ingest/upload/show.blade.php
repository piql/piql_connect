@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-upload titleIcon"></i>Upload
@endsection

@section('content')
    <div class="content">
        <Upload>
        </Upload>
    </div>
@endsection
