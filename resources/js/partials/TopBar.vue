<template>
    <div class="piqlTop">
        <nav class="navbar-expand navbar-light">
            <div class="row pt-3">
                <div class="col-md-3 ">
                    <router-link class="navbar-brand" :to="{ name: 'stats.dashboard' }">
                        <img class="" style="position: fixed; left: 3rem; width: 15rem;" src="/images/piql-connect.png">
                    </router-link>
                </div>
                <div class="col-md-1 ml-0 mr-0 pr-0">
                    <img style="position: fixed; top: 1.5rem; left: 22rem; width: 10rem;" src="/images/customer_top_logo.png">
                </div>
                <div class="col-md-1">
                    <session-timeout-monitor
                        :session-lifetime-ms=sessionLifetimeMs
                        :modal-time-ms=modalTimeMs
                        :interval-ms=updateIntervalMs
                        :no-refresh.boolean=true
                    />
                </div>

                <div class="col-md-7 navLinks w-100">
                    <ul class="navbar-nav m-auto signal">
                        <li class="navbar" :class="currentRouteActiveClass('home')">
                            <router-link :to="{ name: 'stats.dashboard'}">{{$t('Home')}}</router-link>
                        </li>
                        <li class="navbar" :class="currentRouteActiveClass('ingest')">
                            <router-link :to="{ name: 'ingest.uploader' }">{{$t('Ingest')}}</router-link>
                        </li>
                        <li class="navbar" :class="currentRouteActiveClass('access')">
                            <router-link :to="{ name: 'access.browse' }">{{$t('Access')}}</router-link>
                        </li>

                        <li class="navbar" :class="currentRouteActiveClass('settings')" data-toggle="tooltip" :title="$t('sidebar.settings')" >
                            <router-link :to="{ name: 'settings.user' }">
                                <i class="fas fa-cogs plistIcon navbar" ></i>
                            </router-link>
                        </li>

                        <li class="pr-3 plistIcon navbar"><notifications/>
                        </li>
                        <li class="pr-3 plistIcon navbar">
                            <a href="/logout"><i class="fas fa-sign-out-alt signal"></i></a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    async mounted() {
        this.sessionLifetimeMs = (await axios.get("/api/v1/system/sessionLifetime")).data*60*1000;
    },
    data() {
        return {
            sessionLifetimeMs: null,
            modalTimeMs: 300000,     /* 5 minutes */
            updateIntervalMs: 1000   /* 1 second */
        };
    },
    computed: {
        currentActiveRoute() {
            return this.$route.name;
        },
    },
    methods: {
        isCurrentRoute( checkRoute ) {
            return this.$route.name === checkRoute;
        },
        isCurrentComponentRoute( checkRoute ) {
            let base = this.$route.name.split(".");
            return base[0] && base[0] === checkRoute;
        },
        currentRouteActiveClass( route ) {
            if( this.isCurrentComponentRoute( route ) ) return  "top-active";
            return "top-inactive";
        }

    }

}
</script>
