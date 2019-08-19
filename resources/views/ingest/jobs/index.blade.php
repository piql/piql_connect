@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="far fa-clock titleIcon"></i>Job list
@endsection

@section('content')
    <div class="contentContainer">
        <task-list>
        </task-list>
    </div>
@endsection
