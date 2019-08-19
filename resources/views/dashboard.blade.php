@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')

<i class="fas fa-tachometer-alt titleIcon"></i>
<h1>Dashboard</h1>
<!-- <em>Short descriptive text of view.</em> -->
<!-- <p>Other type of text or content goes here.</p> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h2 style="text-align: center;margin-bottom: 5px;">Ingest</h2>
                    <div>
                        <div class="row">
                            <div class="col-sm-6">
                                {!! $monthlyOnlineAIPsIngested->container() !!}
                                {!! $monthlyOnlineAIPsIngested->script() !!}
                            </div>
                            <div class="col-sm-6">
                                {!! $monthlyOnlineDataIngested->container() !!}
                                {!! $monthlyOnlineDataIngested->script() !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                {!! $fileFormatsIngested->container() !!}
                                {!! $fileFormatsIngested->script() !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h2 style="text-align: center;margin-bottom: 5px;">Access</h2>
                    <div class="row">
                        <div class="col-sm-6">
                            {!! $monthlyOnlineAIPsAccessed->container() !!}
                            {!! $monthlyOnlineAIPsAccessed->script() !!}
                        </div>
                        <div class="col-sm-6">
                            {!! $monthlyOnlineDataAccessed->container() !!}
                            {!! $monthlyOnlineDataAccessed->script() !!}
                        </div>
                    </div>
                    <div>
                        <div class="row pt-3" style="justify-content: space-evenly;">
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['onlineAIPsIngested'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">AIPs stored online</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['onlineDataIngested'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Stored online</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlineReelsCount'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Something</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlineDataIngested'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Something</em>
                            </div>
                        </div>
                        <div class="row pt-3" style="justify-content: space-evenly;">
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlineAIPsIngested'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">AIPs stored on film</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlineDataIngested'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Stored on film</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlineReelsCount'] }}</strong>
                                </h1>
                                <em style="color: white;text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Reels</em>
                            </div>
                            <div class="card dashboard-card" class="col-sm-3" style="height:150px;width: 150px;">
                                <h1 style="color: white;text-align: center;padding-top: 50px;font-size: 300%">
                                    <strong>{{ $infoArray['offlinePagesCount'] }}</strong>
                                </h1>
                                <em style="text-align: center;position: absolute;left: 0;right: 0;bottom: 0;">Pages stored on film</em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row" style="height:50%">
                <div class="col-sm-3" style="max-height:100%">
                </div>
                <div class="col-sm-3" style="max-height:100%">
                </div>
                <div class="col-sm-3" style="max-height:100%">
                </div>
                <div class="col-sm-3" style="max-height:100%">
                </div>
            </div> -->
        </div>
       <!--  <div class="col-sm-3">
            <div class="col-sm-12">
                <div class="row" style="height:100px;width: 100px;border:1px solid #000;">
                    <h1 style="text-align: center;margin-left: auto;margin-right: auto;align-self: flex-end;">
                        <strong>{{ $infoArray['onlineAIPsIngested'] }}</strong>
                    </h4>
                    <em style="align-self: end;margin-left: auto;margin-right: auto;">AIPs stored online</em>
                </div>
                <div class="row">
                    <h4>{{ $infoArray['onlineDataIngested'] }} stored online  </h4>
                </div>
                <div class="row">
                    <h4>{{ $infoArray['offlineAIPsIngested'] }} AIPs stored on film  </h4>
                </div>
                <div class="row">
                    <h4>{{ $infoArray['offlineDataIngested'] }} stored on film  </h4>
                </div>
                <div class="row">
                    <h4>{{ $infoArray['offlineReelsCount'] }} reels  </h4>
                </div>
                <div class="row">
                    <h4>{{ $infoArray['offlinePagesCount']  }} stored on film  </h4>
                </div>
                <div class="row">
                </div>
            </div>
        </div> -->
    </div>
</div>
@endsection