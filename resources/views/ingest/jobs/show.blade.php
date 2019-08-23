@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-suitcase titleIcon"></i>Bags
@endsection

@section('content')
    <div class="contentContainer">
        <bag-list :job-id="'{{ $jobId }}' "></bag-list>
    </div>
@endsection
