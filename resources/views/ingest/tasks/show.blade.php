@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>FILE LIST
@endsection

@section('content')
    <div class="contentContainer">
        <file-list :bag-id="'{{ $bagId }}'">
        </file-list>
    </div>
@endsection
