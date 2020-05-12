<template>
    <div class="w-100">
        <page-heading icon="fa-tachometer-alt" :title="$t('dashboard.title')" :ingress="$t('dashboard.ingress')" />

        <div class="row">
            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineAIPsIngested')"
                    url="/api/v1/stats/charts/monthlyOnlineAIPsIngested"
                    :labels="monthNames" />
            </div>

            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineDataIngested')"
                    url="/api/v1/stats/charts/monthlyOnlineDataIngested"
                    :labels="monthNames" />
            </div>

            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineAIPsAccessed')"
                    url="/api/v1/stats/charts/monthlyOnlineAIPsAccessed"
                    :labels="monthNames" />
            </div>
            <div class="col-sm-3">
                <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineDataAccessed')"
                    url="/api/v1/stats/charts/monthlyOnlineDataAccessed"
                    :labels="monthNames"/>
            </div>
        </div>

        <div class="row p-3 m-3">

            <div class="d-inline-flex">
                <pie-chart class="pieChart" :title="$t('dashboard.fileTypes')"
                    url="/api/v1/stats/charts/fileFormatsIngested" />
            </div>

            <div class="col d-inline-flex flex-wrap">
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineAIPsIngested}}</div>
                        <div class="legend">{{$t('dashboard.cards.onlineAIPsIngested')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{onlineDataIngested}}</div>
                        <div class="legend">{{$t('dashboard.cards.onlineDataIngested')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{AIPsRetrievedCount}}</div>
                        <div class="legend">{{$t('dashboard.cards.AIPsRetrieved')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">{{DataRetrieved}}</div>
                        <div class="legend">{{$t('dashboard.cards.filesRetrieved')}}</div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">
                            {{offlineAIPsIngested}}
                        </div>
                        <div class="legend">
                            {{$t('dashboard.cards.AIPsStoredOnFilm')}}
                        </div>
                    </div>
                    <div class="card dashboard-card col-sm halfStats">
                        <div class="value">
                            {{offlineDataIngested}}
                        </div>
                        <div class="legend">
                            {{$t('dashboard.cards.dataStoredOnFilm')}}
                        </div>
                    </div>
                    <div class="card dashboard-card halfStats col-sm">
                        <div class="value">
                            {{offlineReelsCount}}
                        </div>
                        <div class="legend">
                            {{$t('dashboard.cards.reels')}}
                        </div>
                    </div>
                    <div class="dashboard-card halfStats col-sm">
                        <div class="value">{{offlinePagesCount}}</div>
                        <div class="legend">{{$t('dashboard.cards.offlinePagesCount')}}</div>
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
