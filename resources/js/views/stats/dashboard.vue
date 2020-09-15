<template>
    <div class="w-100">
        <page-heading icon="fa-tachometer-alt" :title="$t('dashboard.title')" :ingress="$t('dashboard.ingress')" />
        <div class="row">
            <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                               
                                <div class="col-md-9">
                                    <div class="value">{{offlineAIPsIngested}}</div>
                                    <div class="legend">{{$t('dashboard.cards.AIPsStoredOnFilm')}}</div>

                                </div>
                                 <div class="icon col-md-3"><i class="fa fa-archive"></i></div>
                                
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                                <div class="col-md-9">
                                    <div class="value">{{offlineDataIngested}}</div>
                                    <div class="legend">{{$t('dashboard.cards.dataStoredOnFilm')}}</div>
                                </div>
                                <div class="icon col-md-3"><i class="fa fa-download"></i></div>
                                
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card halfStats col-md row">
                                <div class="col-md-9">
                                    <div class="value">{{offlineReelsCount}}</div>
                                    <div class="legend">{{$t('dashboard.cards.reels')}}</div>
                                </div>
                                <div class="col-md-3 icon"><img src="/images/piqlfilm-icon-white.svg" class="dashboardIconImg"/></div>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card halfStats col-md row">
                            <div class="col-md-9">
                                <div class="value">{{offlinePagesCount}}</div>
                                <div class="legend">{{$t('dashboard.cards.offlinePagesCount')}}</div>
                            </div>
                            <div class="icon col-md-3"><i class="fa fa-file-image"></i></div>
                                
                        </div>
                    </div>
            
        </div>
        <div class="row">
            <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                            <div class="col-md-9">
                                <div class="value">{{onlineAIPsIngested}}</div>
                                <div class="legend">{{$t('dashboard.cards.onlineAIPsIngested')}}</div>
                            </div>
                            <div class="icon col-md-3"><i class="fa fa-cloud"></i></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                            <div class="col-md-9">
                                <div class="value">{{onlineDataIngested}}</div>
                                <div class="legend">{{$t('dashboard.cards.onlineDataIngested')}}</div>
                            </div>
                            <div class="icon col-md-3"><i class="fa fa-database"></i></div>
                                
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                            <div class="col-md-9">
                                <div class="value">{{AIPsRetrievedCount}}</div>
                                <div class="legend">{{$t('dashboard.cards.AIPsRetrieved')}}</div>
                            </div>
                            <div class="icon col-md-3"><i class="fa fa-upload"></i></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card col-md halfStats row">
                            <div class="col-md-9">
                                <div class="value">{{DataRetrieved}}</div>
                                <div class="legend">{{$t('dashboard.cards.filesRetrieved')}}</div>
                            </div>
                            <div class="icon col-md-3"><i class="fa fa-folder-open"></i></div>
                        </div>
                    </div>

        </div>
        <br />
        
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header card-background">
                        <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link l1" @click.prevent="displayGraph(1)" href="#">Archival Packages Ingested</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link l2" @click.prevent="displayGraph(2)" href="#">Data Ingested</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link l3" @click.prevent="displayGraph(3)" href="#">Archival Packages Accessed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link l4" @click.prevent="displayGraph(4)" href="#">Data Accessed</a>
                        </li>
                        </ul>
                    </div>
                    <div class="card-body g1">
                        <h5 class="card-title">Monthly</h5>
                    
                    <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineAIPsIngested')"
                    url="/api/v1/stats/charts/aips/online/ingested/monthly"
                    :labels="monthNames" />
                    </div>
                    <div class="card-body g2">
                        <h5 class="card-title">Monthly</h5>
                        <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineDataIngested')"
                    url="/api/v1/stats/charts/data/online/ingested/monthly"
                    :labels="monthNames" />
                    </div>
                    <div class="card-body g3">
                        <h5 class="card-title">Monthly</h5>
                         <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineAIPsAccessed')"
                    url="/api/v1/stats/charts/aips/online/accessed/monthly"
                    :labels="monthNames" />
                    </div>
                    <div class="card-body g4">
                        <h5 class="card-title">Monthly</h5>
                        <line-chart class="graphs" :title="$t('dashboard.graphs.monthlyOnlineDataAccessed')"
                    url="/api/v1/stats/charts/data/online/accessed/monthly"
                    :labels="monthNames"/>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <pie-chart class="pieChart" :title="$t('dashboard.fileTypes')"
                    url="api/v1/stats/charts/file-formats/ingested" />
                
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
        for (let i = 1; i <= 4; i++) {
            let g = document.querySelector(".g"+ i);
            let l = document.querySelector(".l"+ i);
            if(i != 1){
            
                g.style.display = 'none';
                l.classList.remove("active");
            }else{
                l.classList.add("active");
                g.style.display = 'block';
            }
            
            
        }
        
        
    },
    data() {
        return {
            userStats: [],
        };
    },
    methods:{
        displayGraph(graph){
            for (let i = 1; i <= 4; i++) {
                let g = document.querySelector(".g"+ i);
                let l = document.querySelector(".l"+ i);
                
                if(i != graph){
                
                    g.style.display = 'none';
                    l.classList.remove("active");
                }else{
                    l.classList.add("active");
                    g.style.display = 'block';
                    
                }
             
            }
        },
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