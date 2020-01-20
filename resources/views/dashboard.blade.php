@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')

<div>
    <div class="row mb-5">
        <div class="col-sm-1 text-center">
            <i class="fas fa-tachometer-alt color-main-brand titleIcon"></i>
        </div>
        <div class="text-left mt-3">
            <h1 class="ml-1">Dashboard</h1>
            <div class="ml-1" style="font-size: 0.75rem">The dashboard gives you a breakdown of relevant information about on your data.</div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h1 style="text-align: center;margin-bottom: 5px;">Ingest</h1>
            <div>
                <div class="row">
                    <div class="col-sm-6">
                        <line-chart class="stats" title="AIPs Ingested (monthly)" url="/api/v1/stats/monthlyOnlineAIPsIngested"/>
                    </div>
                    <div class="col-sm-6">
                        <line-chart class="stats" title="AIPs Accessed (monthly)" url="/api/v1/stats/monthlyOnlineAIPsAccessed"/>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12" class="stats">
                        <pie-chart class="stats" title="File formats Ingested" url="/api/v1/stats/fileFormatsIngested"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <h1 style="text-align: center;margin-bottom: 5px;">Access</h1>
            <div class="row">
                <div class="col-sm-6">
                    <line-chart class="stats" title="Data Ingested (monthly)" url="/api/v1/stats/monthlyOnlineDataIngested"/>
                </div>
                <div class="col-sm-6">
                    <line-chart class="stats" title="Data Accessed (monthly)" url="/api/v1/stats/monthlyOnlineDataAccessed"/>
                </div>
            </div>
            <div class="row pt-sm-3 pb-sm-2 halfStatsRow dashboard-halfStatsRow mt-2">
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['onlineAIPsIngested'] }}</strong>
                    </h2>
                    <em style="color: white;">AIPs stored online</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['onlineDataIngested'] }}</strong>
                    </h2>
                    <em >Stored online</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['AIPsRetrievedCount'] }}</strong>
                    </h2>
                    <em>AIPs retrieved</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['DataRetrieved'] }}</strong>
                    </h2>
                    <em>Retrieved</em>
                </div>
            </div>
            <div class="row pt-sm-0 halfStatsRow dashboard-halfStatsRow">
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['offlineAIPsIngested'] }}</strong>
                    </h2>
                    <em>AIPs stored on film</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['offlineDataIngested'] }}</strong>
                    </h2>
                    <em>Stored on film</em>
                </div>
                <div class="card dashboard-card halfStats col-sm-3">
                    <h2>
                        <strong>{{ $infoArray['offlineReelsCount'] }}</strong>
                    </h2>
                    <em >Reels</em>
                </div>
                <div class="card dashboard-card halfStats col-sm-3">
                    <h2>
                        <strong>{{ $infoArray['offlinePagesCount'] }}</strong>
                    </h2>
                    <em>Pages stored on film</em>
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
