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
        <!-- <em>Short descriptive text of view.</em> -->
        <!-- <p>Other type of text or content goes here.</p> -->
        <!-- <br> -->

        <!-- 'monthlyOnlineAIPsIngested', -->
        <!-- 'monthlyOfflineAIPsIngested', -->
        <!-- 'monthlyOnlineAIPsAccessed', -->
        <!-- 'monthlyOfflineAIPsAccessed', -->
        <!-- 'monthlyOnlineDataIngested', -->
        <!-- 'monthlyOfflineDataIngested', -->
        <!-- 'monthlyOnlineDataAccessed', -->
        <!-- 'monthlyOfflineDataAccessed', -->
        <!-- 'dailyOnlineAIPsIngested', -->
        <!-- 'dailyOfflineAIPsIngested', -->
        <!-- 'dailyOnlineAIPsAccessed', -->
        <!-- 'dailyOfflineAIPsAccessed', -->
        <!-- 'dailyOnlineDataIngested', -->
        <!-- 'dailyOfflineDataIngested', -->
        <!-- 'dailyOnlineDataAccessed', -->
        <!-- 'dailyOfflineDataAccessed', -->
        <!-- 'fileFormatsIngested', -->
        <!-- 'monthylyAIPsStored', -->

        <!-- $infoArray['onlineDataIngested'] -->
        <!-- $infoArray['offlineDataIngested'] -->
        <!-- $infoArray['onlineAIPsIngested'] -->
        <!-- $infoArray['oflineAIPsIngested'] -->
        <!-- $infoArray['offlineReelsCount'] -->
        <!-- $infoArray['offlinePagesCount'] -->



        <div class="row">
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $monthlyOnlineAIPsIngested->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOnlineAIPsIngested->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $monthlyOnlineDataIngested->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOnlineDataIngested->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $fileFormatsIngested->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $fileFormatsIngested->script() !!}
            </div>
            <div class="col">
                <h4>{{ $infoArray['onlineAIPsIngested'] }} AIPs stored</h4>
                <h4>#### pages stored on piqlFilm</h4>
                <h4>{{ $infoArray['onlineDataIngested'] }} Used for online storage</h4>
            </div>
        </div>
        <div class="row">
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $monthlyOfflineAIPsIngested->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOfflineAIPsIngested->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $monthlyOfflineDataIngested->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOfflineDataIngested->script() !!}
            </div>
            <div class="col" style="max-height:300px;max-width:300px">
                {!! $monthlyOnlineAIPsAccessed->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOnlineAIPsAccessed->script() !!}
            </div>
            <div class="col">
                <h4>{{ $infoArray['onlineAIPsIngested'] }} AIPs stored</h4>
                <h4>#### pages stored on piqlFilm</h4>
                <h4>{{ $infoArray['onlineDataIngested'] }} Used for online storage</h4>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {!! $monthlyOnlineDataAccessed->container() !!}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
                {!! $monthlyOnlineDataAccessed->script() !!}
            </div>
        </div>
    </div>
</div>
@endsection