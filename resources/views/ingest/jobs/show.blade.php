@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-suitcase titleIcon"></i>AIP List
@endsection

@section('content')
    <div class="contentContainer">
        {{ Breadcrumbs::render('aip_list', $job) }}

        <bag-list :job-id="'{{ $job->id }}' "></bag-list>
    </div>
@endsection
