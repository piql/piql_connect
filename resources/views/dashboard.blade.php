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
        <div class="chart-container chart">
            <canvas id="myChart"></canvas>
        </div>
        <div class="chart-container chart">
            <canvas id="myChart2"></canvas>
        </div>
        <div class="chart-container chart">
            <canvas id="myChart3"></canvas>
        </div>
        <div class="chart-container chart">
            <canvas id="myChart4"></canvas>
        </div>
    </div>
</div>
@endsection
