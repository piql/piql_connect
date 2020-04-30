<template>
    <div class="w-100">
        <page-heading icon="fa-tachometer-alt" :title="$t('dashboard.title')" :ingress="$t('dashboard.ingress')" />

        <div class="row">
            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('Archival packages ingested (monthly)')"
                    url="/api/v1/stats/charts/monthlyOnlineAIPsIngested"
                    :labels="monthNames" />
            </div>

            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('Data ingested (monthly)')"
                    url="/api/v1/stats/charts/monthlyOnlineDataIngested"
                    :labels="monthNames" />
            </div>

            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('Archival packages accessed (monthly)')"
                    url="/api/v1/stats/charts/monthlyOnlineAIPsAccessed"
                    :labels="monthNames" />
            </div>
            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('Data accessed (monthly)')"
                    url="/api/v1/stats/charts/monthlyOnlineDataAccessed"
                    :labels="monthNames"/>
            </div>
        </div>

        <div class="row p-3 m-3">

            <div class="d-inline-flex">
                <pie-chart class="pieChart" :title="$t('File formats ingested')"
                    url="/api/v1/stats/charts/fileFormatsIngested" />
            </div>

            <div class="col d-inline-flex flex-wrap">
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineAIPsIngested}}</div>
                        <div class="legend">{{$t('Archival packages online')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineDataIngested}}</div>
                        <div class="legend">{{$t('Stored online')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{AIPsRetrievedCount}}</div>
                        <div class="legend">{{$t('Archival packages retrieved')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{DataRetrieved}}</div>
                        <div class="legend">{{$t('Files retrieved')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">
                            {{offlineAIPsIngested}}
                        </div>
                        <div class="legend">
                            {{$t('Archival packages on film')}}
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
                        <div class="legend">{{$t('Pages on film')}}</div>
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
