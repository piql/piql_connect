<template>
    <div>
        <div class="row componentHeader">
                <i class="fas fa-tachometer-alt color-main-brand titleIcon"></i>
			<div class="align-center">
				<h1 class="ml-1 titleText">{{$t('Dashboard')}}</h1>
			</div>
        </div>

        <div class="row componentBody">
            <div class="col-sm-6">
                <div>
                    <div class="row">
                        <div class="col-sm-6">
                            <line-chart class="stats" :title="$t('Archival Packages Ingested (monthly)')"
                                url="/api/v1/stats/charts/monthlyOnlineAIPsIngested"
                                :labels="monthNames" />
                        </div>
                        <div class="col-sm-6">
                            <line-chart class="stats" :title="$t('Data Ingested (monthly)')"
                                url="/api/v1/stats/charts/monthlyOnlineDataIngested"
                                :labels="monthNames" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <pie-chart class="stats" :title="$t('File formats Ingested')"
                                url="/api/v1/stats/charts/fileFormatsIngested" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-6">
                        <line-chart class="stats" :title="$t('Archival Packages Accessed (monthly)')"
                            url="/api/v1/stats/charts/monthlyOnlineAIPsAccessed"
                            :labels="monthNames" />
                    </div>
                    <div class="col-sm-6">
                        <line-chart class="stats" :title="$t('Data Accessed (monthly)')"
                            url="/api/v1/stats/charts/monthlyOnlineDataAccessed"
                            :labels="monthNames"/>
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
        this.get(
            "/api/v1/stats/user/current"
        ).then( (response) => {
            this.userStats = response.data.data;
        }).catch(error => {
            this.userStats = [];
        });
    },
    data() {
        return {
            userStats: [],
        };
    },
    computed: {
        onlineDataIngested: function() {
            return this.userStats['onlineDataIngested'];
        },
        offlineDataIngested: function() {
            return this.userStats['offlineDataIngested'];
        },
        onlineAIPsIngested: function() {
            return this.userStats['onlineAIPsIngested'];
        },

        offlineAIPsIngested: function() {
            return this.userStats['offlineAIPsIngested'];
        },

        offlinePagesCount: function() {
            return this.userStats['offlinePagesCount'];
        },

        offlineReelsCount: function() {
            return this.userStats['offlineReelsCount'];
        },

        AIPsRetrievedCount: function() {
            return this.userStats['AIPsRetrievedCount'];
        },
        DataRetrieved: function() {
            return this.userStats['DataRetrieved'];
        },
        monthNames: function() {
			// TODO: Rethink how we represent months in the translation file
            let translated = this.$t('dashboard.month.abbrev.array');
            return translated.split("'").filter( t => t != "[" && t != "]" && t != ", ");
        },
    }
}
</script>
