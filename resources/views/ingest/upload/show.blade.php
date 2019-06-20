@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-upload titleIcon"></i>Upload
@endsection

@section('content')
    <div class="content">
        <Upload><button class="browse">Add files</button></Upload>

    </div>
@endsection
