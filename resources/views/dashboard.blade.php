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
            <h1 class="ml-1">{{__("Dashboard")}}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div>
                <div class="row">
                    <div class="col-sm-6">
                        <line-chart class="stats" title="{{__("Archival Packages Ingested (monthly)")}}"
                                    url="/api/v1/stats/monthlyOnlineAIPsIngested"
                                    :labels="{{__("dashboard.month.abbrev.array")}}"/>
                    </div>
                    <div class="col-sm-6">
                        <line-chart class="stats" title="{{__("Data Ingested (monthly)")}}"
                                    url="/api/v1/stats/monthlyOnlineDataIngested"
                                    :labels="{{__("dashboard.month.abbrev.array")}}"/>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12" class="stats">
                        <pie-chart class="pieChart" height="200" title="{{__("File formats Ingested")}}"
                                   url="/api/v1/stats/fileFormatsIngested"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <line-chart class="stats" title="{{__("Archival Packages Accessed (monthly)")}}"
                                url="/api/v1/stats/monthlyOnlineAIPsAccessed"
                                :labels="{{__("dashboard.month.abbrev.array")}}"/>
                </div>
                <div class="col-sm-6">
                    <line-chart class="stats" title="{{__("Data Accessed (monthly)")}}"
                                url="/api/v1/stats/monthlyOnlineDataAccessed"
                                :labels="{{__("dashboard.month.abbrev.array")}}"/>
                </div>
            </div>
            <div class="row pt-sm-3 pb-sm-2 halfStatsRow dashboard-halfStatsRow mt-2">
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['onlineAIPsIngested'] }}</strong>
                    </h2>
                    <em style="color: white;">{{__("Archival Packages stored online")}}</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['onlineDataIngested'] }}</strong>
                    </h2>
                    <em>{{__("Stored online")}}</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['AIPsRetrievedCount'] }}</strong>
                    </h2>
                    <em>{{__("Archival Packages retrieved")}}</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['DataRetrieved'] }}</strong>
                    </h2>
                    <em>{{__("Retrieved")}}</em>
                </div>
            </div>
            <div class="row pt-sm-0 halfStatsRow dashboard-halfStatsRow">
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['offlineAIPsIngested'] }}</strong>
                    </h2>
                    <em>{{__("Archival Packages stored on film")}}</em>
                </div>
                <div class="card dashboard-card col-sm-3 halfStats">
                    <h2>
                        <strong>{{ $infoArray['offlineDataIngested'] }}</strong>
                    </h2>
                    <em>{{__("Stored on film")}}</em>
                </div>
                <div class="card dashboard-card halfStats col-sm-3">
                    <h2>
                        <strong>{{ $infoArray['offlineReelsCount'] }}</strong>
                    </h2>
                    <em>{{__("Reels")}}</em>
                </div>
                <div class="card dashboard-card halfStats col-sm-3">
                    <h2>
                        <strong>{{ $infoArray['offlinePagesCount'] }}</strong>
                    </h2>
                    <em>{{__("Pages stored on film")}}</em>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
