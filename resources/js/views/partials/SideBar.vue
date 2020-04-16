<template>
    <div>
        <div :class="adaptToScreenWidth">
            <div class="welcomeText" :class="invisibleIfNarrow">
                <p class="mb-1">{{$t("Welcome")}}</p>
                <p><i class="fas fa-user"></i> {{ fullname }}</p>
            </div>

            <div class="clearboth">
            </div>

            <ul class="list-group overflow-none d-block">
                <span :class="collapseInactiveComponent('home')">

                    <a href="/">
                        <li class="list-group-item" :class="currentRouteActiveClass('home.dashboard')" >
                            <i class="fas fa-tachometer-alt"></i><i class="leftMenuItem">{{$t('sidebar.dashboard')}}</i>
                        </li>
                    </a>

                    <a href="/" class="collapse">
                        <li class="list-group-item" :class="currentRouteActiveClass('home.reports')" >
                            <i class="fas fa-char-bar"></i><i class="leftMenuItem">{{$t('sidebar.reports')}}</i>
                        </li>
                    </a>

                </span>

                <span :class="collapseInactiveComponent('ingest')">

                    <a href="/ingest/upload">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.upload')" >
                            <i class="fas fa-upload"></i><i class="leftMenuItem">{{$t('sidebar.upload')}}</i>
                        </li>
                    </a>

                    <a href="/ingest/process">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.process')" >
                            <i class="fas fa-hourglass-half"></i><i class="leftMenuItem">{{$t('sidebar.processing')}}</i>
                        </li>
                    </a>

                    <a href="/ingest/offline_storage">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.offline_storage')" >
                            <i class="fas fa-clock"></i><i class="leftMenuItem">{{$t('sidebar.taskList')}}</i>
                        </li>
                    </a>

                    <a href="/ingest/status">
                        <li class="list-group-item" :class="currentRouteActiveClass('ingest.status')" >
                            <i class="fas fa-clipboard-check"></i><i class="leftMenuItem">{{$t('sidebar.status')}}</i>
                        </li>
                    </a>

                </span>

                <span :class="collapseInactiveComponent('access')">

                    <a href="/access/browse">
                        <li class="list-group-item" :class="currentRouteActiveClass('access.browse')" >
                            <i class="fas fa-hdd"></i><i class="leftMenuItem">{{$t('sidebar.browse')}}</i>
                        </li>
                    </a>

                    <span :class="collapseInactiveSubComponent('access.browse')">
                        <a href="/access/retrieve/ready">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.ready')" >
                                <i class="fas fa-file-export"></i><i class="leftMenuItem">{{$t('sidebar.retrieve')}}</i>
                            </li>
                        </a>
                    </span>


                    <span :class="collapseInactiveSubComponent('access.retrieve')">

                        <a href="/access/retrieve/ready">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.ready')" >
                                <i class="fas fa-file-export"></i><i class="leftMenuItem">{{$t('sidebar.retrieve.readyToRetrieve')}}</i>
                            </li>
                        </a>

                        <a href="/access/retrieve/retrieving">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.retrieving')" >
                                <i class="fas fa-spinner"></i><i class="leftMenuItem">{{$t('sidebar.retrieve.retrieving')}}</i>
                            </li>
                        </a>

                        <a href="/access/retrieve/download">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.download')" >
                                <i class="fas fa-file-download"></i><i class="leftMenuItem">{{$t('sidebar.retrieve.downloadable')}}</i>
                            </li>
                        </a>

                        <a href="/access/retrieve/history">
                            <li class="list-group-item" :class="currentRouteActiveClass('access.retrieve.history')" >
                                <i class="fas fa-history"></i><i class="leftMenuItem">{{$t('sidebar.retrieve.history')}}</i>
                            </li>
                        </a>

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
        currentActiveRoute() {
            return this.$route.name;
        },
        adaptToScreenWidth() {
            return this.width > 1100 ? "sideMenu" : "sideMenuCollapsed";
        },
        invisibleIfNarrow() {
            return this.width > 1100 ? "" : "invisible";
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


