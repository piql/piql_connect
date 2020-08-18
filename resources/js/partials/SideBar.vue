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

                <span :class="{ collapse: !routeBelongsTo('settings') }">

                    <router-link :to="{ name: 'settings.user' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('settings.user') }" >
                            <i class="fas fa-cog"></i>
                            <div class="leftMenuItem">
                                {{$t('sidebar.userProfile')}}
                            </div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'settings.admin' }">
                        <li class="list-group-item cursorPointer" :class="{ active: routeBelongsTo('settings.admin') }">
                            <i class="fas fa-wrench"></i>
                            <div class="leftMenuItem">
                                {{$t('sidebar.settings.admin')}}
                            </div>
                        </li>
                    </router-link>

                    <span v-if=" routeBelongsTo('settings.admin') ">

                            <router-link :to="{ name: 'settings.admin.logo' }">
                                <li class="list-group-item submenu" :class="{ active: routeBelongsTo('settings.admin.logo') }" >
                                    <i class="fas fa-shapes"></i>
                                    <div class="leftMenuItem">
                                        {{$t('sidebar.settings.admin.logo')}}
                                    </div>
                                </li>
                            </router-link>

                            <router-link :to="{ name: 'settings.admin.account.metadata.template' }">
                                <li class="list-group-item submenu" :class="{ active: routeBelongsTo('settings.admin.account.metadata.template') }" >
                                    <i class="fas fa-list"></i>
                                    <div class="leftMenuItem">
                                        {{$t('sidebar.settings.admin.metadata')}}
                                    </div>
                                </li>
                            </router-link>
         
                            <router-link :to="{ name: 'settings.admin.account.users' }">
                                <li class="list-group-item submenu" :class="{ active: routeBelongsTo('settings.admin.account.users') }" >
                                    <i class="fas fa-user"></i>
                                    <div class="leftMenuItem">
                                        {{$t('sidebar.users')}}
                                    </div>
                                </li>
                            </router-link>
                            <router-link :to="{ name: 'settings.admin.account.groups' }">
                                <li class="list-group-item submenu" :class="{ active: routeBelongsTo('settings.admin.account.groups') }" >
                                    <i class="fas fa-users"></i>
                                    <div class="leftMenuItem">
                                        {{$t('sidebar.userGroups')}}
                                    </div>
                                </li>
                            </router-link>

                            <router-link :to="{ name: 'settings.admin.account.roles' }">
                                <li class="list-group-item submenu" :class="{ active: routeBelongsTo('settings.admin.account.roles') }" >
                                    <i class="fas fa-user-shield"></i>
                                    <div class="leftMenuItem">
                                        {{$t('sidebar.roles')}}
                                    </div>
                                </li>
                            </router-link>

                        </span>

                </span>

                <span :class="{ collapse: !routeBelongsTo('ingest') }">

                    <router-link :to="{ name: 'ingest.uploader' }">
                        <li class="list-group-item" :class="{ active: routeBelongsTo('ingest.uploader') }" >
                            <i class="fas fa-upload"></i>
                            <div class="leftMenuItem">{{$t('sidebar.upload')}}</div>
                        </li>
                    </router-link>

                    <router-link :to="{ name: 'ingest.processing' }" id="sideBarProcessing">
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
                        <router-link :to="{ name: 'access.retrieve.request' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo ('access.retrieve') }" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve')}}</div>
                            </li>
                        </router-link>
                    </span>


                    <span :class="{ collapse: !routeBelongsTo('access.retrieve') }">

                        <router-link :to="{ name: 'access.retrieve.request' }">
                            <li class="list-group-item" :class="{ active: routeBelongsTo('access.retrieve.request') }" >
                                <i class="fas fa-file-export"></i><div class="leftMenuItem">{{$t('sidebar.retrieve')}}</div>
                            </li>
                        </router-link>


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
            console.log("checking ",checkRoute );
            return this.$route.name.startsWith( checkRoute );
        },
    }
}
</script>


