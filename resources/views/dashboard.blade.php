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
        <h1>Dashboard</h1>
        <em>Short descriptive text of view.</em>
        <p>Other type of text or content goes here.</p>
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
