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
                    <div class="col-sm-12">
                        <pie-chart class="stats" title="{{__("File formats Ingested")}}"
                                   url="/api/v1/stats/fileFormatsIngested" height=150 />
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
            <div class="row pt-sm-3 pb-sm mt-4">
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">{{ $infoArray['onlineAIPsIngested'] }}</div>
                    <div class="legend">{{__("Archival Packages stored online")}}</div>
                </div>
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">{{ $infoArray['onlineDataIngested'] }}</div>
                    <div class="legend">{{__("Stored online")}}</div>
                </div>
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">{{ $infoArray['AIPsRetrievedCount'] }}</div>
                    <div class="legend">{{__("Archival Packages retrieved")}}</div>
                </div>
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">{{ $infoArray['DataRetrieved'] }}</div>
                    <div class="legend">{{__("Retrieved")}}</div>
                </div>
            </div>
            <div class="row pt-1 mt-1 halfStatsRow dashboard-halfStatsRow">
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">
                        {{ $infoArray['offlineAIPsIngested'] }}
                    </div>
                    <div class="legend">
                        {{__("Archival Packages stored on film")}}
                    </div>
                </div>
                <div class="card dashboard-card col-sm halfStats">
                    <div class="value">
                        {{ $infoArray['offlineDataIngested'] }}
                    </div>
                    <div class="legend">
                        {{__("Stored on film")}}
                    </div>
                </div>
                <div class="card dashboard-card halfStats col-sm">
                    <div class="value">
                        {{ $infoArray['offlineReelsCount'] }}
                    </div>
                    <div class="legend">
                        {{__("Reels")}}
                    </div>
                </div>
                <div class="dashboard-card halfStats col-sm">
                    <div class="value">{{ $infoArray['offlinePagesCount'] }}</div>
                    <div class="legend">{{__("Pages stored on film")}}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
