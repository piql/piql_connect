<template>
    <div class="piqlTop">
        <nav class="mb-2 mt-0 navbar-expand-xl navbar-expand-sm navbar-light">
            <div class="row pt-3">
                <div class="col-md-3 ">
                    <a class="navbar-brand" href="/">
                        <img class="" style="position: fixed; left: 3rem; width: 15rem;" src="/images/piql-connect.png">
                    </a>
                </div>

                <div class="col-md-1">
                    <session-timeout-monitor
                        :navigation-activity-time="7000000"
                        :session-lifetime-ms="7200000"
                        :interval="10000"
                        :no-refresh.boolean="true"
                    />
                </div>

                <div class="col-md-7 navLinks w-100">
                    <ul class="navbar-nav m-auto signal">
                        <li class="navbar" :class="currentRouteActiveClass('home')">
                            <a href="/">{{$t('Home')}}</a>
                        </li>
                        <li class="navbar" :class="currentRouteActiveClass('ingest')">
                            <a href="/ingest/upload">{{$t('Ingest')}}</a>
                        </li>
                        <li class="navbar" :class="currentRouteActiveClass('access')">
                            <a href="/access/browse">{{$t('Access')}}</a>
                        </li>
                        <li class="pl-3 pr-3 plistIcon navbar">
                            <settings-dropdown/>
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
    data() {
        return {
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
            let base = this.$route.name.split(".")[0];
            return base === checkRoute;
        },
        currentRouteActiveClass( route ) {
            if( this.isCurrentComponentRoute( route ) ) return  "top-active";
            return "top-inactive";
        }

    }

}
</script>
