<template>
    <div class="piqlTop fixed-top">
        <nav class="navbar-expand navbar-light">
            <div class="row pt-3">
                <div class="col-md-3 ">
                    <router-link class="navbar-brand" :to="{ name: 'stats.dashboard' }">
                        <img class="" style="position: fixed; left: 3rem; width: 15rem;" src="/images/piql-connect.png">
                    </router-link>
                </div>
                <div class="col-md-1 ml-0 mr-0 pr-0">
                    <img id="customLogiImg" style="position: fixed; top: 1.5rem; left: 22rem; max-width: 250px; max-height: 80px;" src="/api/v1/admin/logo/show">
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
                        <li class="navbar" ref='nav-home' :class="[ routeBelongsTo('stats') ? 'top-active' : 'top-inactive' ]">
                            <router-link :to="{ name: 'stats.dashboard'}"><b>{{$t('Home')}}</b></router-link>
                        </li>
                        <li id="ingest" class="navbar" ref='nav-ingest' :class="[ routeBelongsTo('ingest') ? 'top-active' : 'top-inactive' ]">
                            <router-link :to="{ name: 'ingest.uploader' }" id="navbarIngest"><b>{{$t('Ingest')}}</b></router-link>
                        </li>
                        <li class="navbar" ref='nav-access' :class="[ routeBelongsTo('access') ? 'top-active' : 'top-inactive' ]">
                            <router-link :to="{ name: 'access.browse' }" id="navbarAccess"><b>{{$t('Access')}}</b></router-link>
                        </li>

                        <li class="navbar" ref='nav-settings' :class="[ routeBelongsTo('settings') ? 'top-active' : '']" data-toggle="tooltip" :title="$t('sidebar.settings')" >
                            <router-link :to="{ name: 'settings.user' }">
                                <i class="fas fa-cogs plistIcon navbar" ></i>
                            </router-link>
                        </li>

                        <li class="pr-2 navbar">
                            <notifications/>
                        </li>
                        <li class="pr-2 navbar">
                            <a href="#logout" id="navbarLogout" @click="logoutUser"><i class="fas plistIcon fa-sign-out-alt signal"></i></a>
                        </li>
                        <li class="pr-2 navbar">
                            <div class="userImgCont"><img :src="userImg" class="userImg"/></div>
                            <div class="userName">{{ userName }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
import axios from 'axios';
import serviceCall from '../mixins/serviceCall';
import { mapActions, mapGetters } from "vuex";
export default {
    mixins: [
        serviceCall
    ],
    async mounted() {
        this.get("/api/v1/system/sessionLifetime").then( (data) => this.sessionLifetimeMs = data*60*1000 );
    },
    data() {
        return {
            sessionLifetimeMs: null,
            user: null,
            modalTimeMs: 300000,     /* 5 minutes */
            updateIntervalMs: 1000   /* 1 second */
        };
    },
    computed: {
        userName() {
            return this.user != null ? this.user.given_name : "";
        },
        currentActiveRoute() {
            return this.$route.name;
        },
        userImg() {
            this.loadUser();
            if (this.user != null) {
                return 'data:image/jpeg;base64, ' + this.user.img;
            }
            return "";
        }
    },
    methods: {
        ...mapActions(["logoutUser"]),
        routeBelongsTo( checkRoute ) {
            if( !this.$route.name ) return false;
            return this.$route.name.startsWith( checkRoute );

        },
        loadUser() {
            if (this.user == null) {
                axios.get("/api/v1/system/users/me").then( async ( resp ) =>  {
                    this.user = resp.data;
                });
            }
        }
    }

}
</script>
