@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')
<div class="contentContainer">
    <div class="content">
        <i class="fas fa-tachometer-alt titleIcon"></i>
        <h1>{{__('Dashboard')}}</h1>
        <em>{{__('dashboard.ingress')}}</em>
        <p>{{__('dashboard.content')}}</p>
        <br>
        <div class="row">
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $chart1->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $chart1->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $chart2->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $chart2->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $chart3->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $chart3->script() !!}
            </div>
            <div class="col">
                <h4>{{ $infoArray['numberOfStoredAIPs'] }} AIPs stored</h4>
                <h4>#### pages stored on piqlFilm</h4>
                <h4>{{ $infoArray['sizeOfStoredAIPs'] }} Used for online storage</h4>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {!! $chart4->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $chart4->script() !!}
            </div>
        </div>
    </div>
</div>
@endsection