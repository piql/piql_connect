@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-upload titleIcon"></i>Upload
@endsection

@section('content')
    <div class="content">
        <Upload><buton class="browse">Add files</button></Upload>

        <div class="row" style="height: 100px;"></div> 

        <Bags></Bags>
    </div>
@endsection
