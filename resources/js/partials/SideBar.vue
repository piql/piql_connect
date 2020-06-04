<template>
    <div :class="responsiveSidebarColumns">
        <div :class="adaptToScreenWidth">
            <div class="welcomeText" :class="invisibleIfNarrow">
                <p class="mb-1">{{$t("Welcome")}}</p>
                <p><i class="fas fa-user"></i> {{ fullname }}</p>
            </div>

            <div class="clearboth">
            </div>

            <ul class="list-inline">
                <span :class="{ collapse: !routeBelongsTo('stats') }">

                    <router-link :to="{ name: 'stats.dashboard' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('stats.dashboard') }" >
                            <i class="fas fa-tachometer-alt"></i>
                            <div class="leftMenuItem">
                                {{$t('sidebar.dashboard')}}
                            </div>
                        </li>
                    </router-link>

                </span>

                <span :class="{ collapse: !routeBelongsTo('ingest') }">

                    <router-link :to="{ name: 'ingest.uploader' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('ingest.uploader') }" >
                            <i class="fas fa-upload"></i>
                            <div class="leftMenuItem">{{$t('sidebar.upload')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.processing' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('ingest.processing') }" >
                            <i class="fas fa-hourglass-half"></i><div class="leftMenuItem">{{$t('sidebar.processing')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.offline'}">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('ingest.offline') }" >
                            <i class="fas fa-clock"></i><div class="leftMenuItem">{{$t('sidebar.taskList')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.status' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('ingest.status') }" >
                            <i class="fas fa-clipboard-check"></i><div class="leftMenuItem">{{$t('sidebar.status')}}</div>
                        </li>
                    </router-link>

                </span>

                <span :class="{ collapse: !routeBelongsTo('access') }">

                    <router-link :to="{ name: 'access.browse' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('access.browse') }" >
                            <i class="fas fa-hdd"></i><div class="leftMenuItem">{{$t('sidebar.browse')}}</div>
                        </li>
                    </router-link>

                    <span :class="{ collapse: !routeBelongsTo('access.browse') }">
                        <router-link :to="{ name: 'access.retrieve.ready' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo ('access.retrieve.ready') }" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve')}}</div>
                            </li>
                        </router-link>
                    </span>


                    <span :class="{ collapse: !routeBelongsTo('access.retrieve') }">

                        <router-link :to="{ name: 'access.retrieve.ready' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo('access.retrieve.ready') }" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.readyToRetrieve')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.now' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo('access.retrieve.now') }" >
                                <i class="fas fa-spinner"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.retrieving')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.download' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo('access.retrieve.download') }" >
                                <i class="fas fa-file-download"></i><div class="leftMenuItem">{{$t('sidebar.retrieve.downloadable')}}</div>
                            </li>
                        </router-link>

                        <router-link :to="{ name: 'access.retrieve.history' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo('access.retrieve.history') }" >
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
        routeBelongsTo( checkRoute ) {
            return this.$route.name.startsWith( checkRoute );
        },
    }
}
</script>


