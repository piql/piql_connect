<template>
    <div :class="responsiveSidebarColumns" style="height:100%">
        <div :class="adaptToScreenWidth" style="height:100%" >
            <div class="welcomeText" :class="invisibleIfNarrow">
                <p class="mb-1">{{$t("Welcome")}}</p>
                <p><i class="fas fa-user"></i> {{ fullname }}</p>
            </div>

            <div class="clearboth">
            </div>

            <ul class="list-inline">
                <span :class="collapseInactiveComponent('home')">

                    <router-link :to="{ name: 'home.dashboard' }">
                        <li class="list-group-item" :class="currentRouteActiveClass('home.dashboard')" >
                            <i class="fas fa-tachometer-alt"></i>
                            <div class="leftMenuItem">
                                {{$t('sidebar.dashboard')}}
                            </div>
                        </li>
                    </router-link>

                </span>

                <span :class="collapseInactiveComponent('ingest')">

                    <router-link :to="{ name: 'ingest.upload' }">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.upload')" >
                            <i class="fas fa-upload"></i>
                            <div class="leftMenuItem">{{$t('sidebar.upload')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.process' }">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.process')" >
                            <i class="fas fa-hourglass-half"></i><div class="leftMenuItem">{{$t('sidebar.processing')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.offline_storage'}">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.offline_storage')" >
                            <i class="fas fa-clock"></i><div class="leftMenuItem">{{$t('sidebar.taskList')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.status' }">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.status')" >
                            <i class="fas fa-clipboard-check"></i><div class="leftMenuItem">{{$t('sidebar.status')}}</div>
                        </li>
                    </router-link>

                </span>

                <span :class="collapseInactiveComponent('access')">

                    <router-link :to="{ name: 'access.browse' }">
                        <li class="list-group-item" :class="currentRouteActiveClass('access.browse')" >
                            <i class="fas fa-hdd"></i><div class="leftMenuItem">{{$t('sidebar.browse')}}</div>
                        </li>
                    </router-link>

                    <span :class="collapseInactiveSubComponent('access.browse')">
                        <router-link :to="{ name: 'access.retrieve.ready' }">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.ready')" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve')}}</div>
                            </li>
                        </router-link>
                    </span>


                    <span :class="collapseInactiveSubComponent('access.retrieve')">

                        <router-link :to="{ name: 'access.retrieve.ready' }">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.ready')" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.readyToRetrieve')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.retrieving' }">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.retrieving')" >
                                <i class="fas fa-spinner"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.retrieving')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.download' }">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.download')" >
                                <i class="fas fa-file-download"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.downloadable')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.history' }">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.history')" >
                                <i class="fas fa-history"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.history')}}</div>
                            </li>
                        </router-link>

                    </span>

                </span>

            </ul>

            <div class="poweredBy" :class="invisibleIfNarrow">
                <div class="poweredByText">Powered by </div>
                    <span class="poweredByImg"><img src="/images/piql_logo_white.png">
                    </span>
            </div>
        </div>
    </div>
</template>
<script>

import axios from 'axios';

export default {
    data() {
        return {
        };
    },
    mounted() {
    },
    props: {
        fullname: String,
        width: {
            type: Number,
            default: 0
        }
    },
    computed: {
        responsiveSidebarColumns() {
            return this.width > 1100 ? "sideMenuOuter" : "sideMenuOuterCollapsed";
        },
        currentActiveRoute() {
            return this.$route.name;
        },
        adaptToScreenWidth() {
            return this.width > 1100 ? "sideMenu" : "sideMenu-collapsed";
        },
        adaptOuterToScreenWidth() {
            return this.width > 1000 ? "sideMenuOuter" : "";
        },
        invisibleIfNarrow() {
            return this.width > 1000 ? "" : "invisible";
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
        isCurrentSubComponentRoute( checkRoute ) {
            let base = this.$route.name.split(".").slice(0,2).join(".");
            return base === checkRoute;
        },
        currentRouteActiveClass( route ) {
            if( this.isCurrentRoute( route ) ) return "active";
            return "";
        },
        collapseInactiveComponent( route ) {
            if( ! this.isCurrentComponentRoute( route ) ) {
                return "collapse";
            }
            return "";
        },
        collapseInactiveSubComponent( route ) {
            if( ! this.isCurrentSubComponentRoute( route ) ) {
                return "collapse";
            }
            return "";
        },
    }
}
</script>


