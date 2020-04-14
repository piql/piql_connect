<template>
    <div>
        <div class="row mb-5">
            <div class="col-sm-1 text-center">
                <i class="fas fa-tachometer-alt color-main-brand titleIcon"></i>
            </div>
            <div class="text-left mt-3">
                <h1 class="ml-1">{{$t('Dashboard')}}</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div>
                    <div class="row">
                        <div class="col-sm-6">
                            <line-chart class="stats" :title="$t('Archival Packages Ingested (monthly)')"
                                url="/api/v1/stats/monthlyOnlineAIPsIngested"
                                :labels="$t('dashboard.month.abbrev.array')"/>
                        </div>
                        <div class="col-sm-6">
                            <line-chart class="stats" :title="$t('Data Ingested (monthly)')"
                                url="/api/v1/stats/monthlyOnlineDataIngested"
                                :labels="$t('dashboard.month.abbrev.array')"/>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <pie-chart class="stats" :title="$t('File formats Ingested')"
                                url="/api/v1/stats/fileFormatsIngested" height=150 />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-6">
                        <line-chart class="stats" :title="$t('Archival Packages Accessed (monthly)')"
                            url="/api/v1/stats/monthlyOnlineAIPsAccessed"
                            :labels="$t('dashboard.month.abbrev.array')"/>
                    </div>
                    <div class="col-sm-6">
                        <line-chart class="stats" :title="$t('Data Accessed (monthly)')"
                            url="/api/v1/stats/monthlyOnlineDataAccessed"
                            :labels="$t('dashboard.month.abbrev.array')"/>
                    </div>
                </div>
                <div class="row pt-sm-3 pb-sm mt-4">
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineAIPsIngested}}</div>
                        <div class="legend">{{$t('Archival Packages stored online')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineDataIngested}}</div>
                        <div class="legend">{{$t('Stored online')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{AIPsRetrievedCount}}</div>
                        <div class="legend">{{$t('Archival Packages retrieved')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{DataRetrieved}}</div>
                        <div class="legend">{{$t('Retrieved')}}</div>
                    </div>
                </div>
                <div class="row pt-1 mt-1 halfStatsRow dashboard-halfStatsRow">
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">
                            {{offlineAIPsIngested}}
                        </div>
                        <div class="legend">
                            {{$t('Archival Packages stored on film')}}
                        </div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">
                            {{offlineDataIngested}}
                        </div>
                        <div class="legend">
                            {{$t('Stored on film')}}
                        </div>
                    </div>
                    <div class="card dashboard-card halfStats col-sm">
                        <div class="value">
                            {{offlineReelsCount}}
                        </div>
                        <div class="legend">
                            {{$t('Reels')}}
                        </div>
                    </div>
                    <div class="dashboard-card halfStats col-sm">
                        <div class="value">{{offlinePagesCount}}</div>
                        <div class="legend">{{$t('Pages stored on film')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    mounted() {
        console.log('Dashboard view mounted.')
    }
}
</script>
