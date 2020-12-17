<template>
    <div class="screensize fullHeight">
        <div v-if="!$auth.loading && $auth.isAuthenticated" class="container-fluid p-0 fullHeight">
            <top-bar />
            <div class="d-flex flex-row overflow-auto fullHeight">
                <div class="d-flex fullHeight">
                    <side-bar :width="currentWidth" :fullname="fullname" />
                </div>
                <div class="d-flex flex-nowrap mr-auto ml-auto pr-4 pl-4 w-100 justify-content-center" style="margin-top: 140px">
                    <div class="w-100" style="margin-left: 280px;">
                        <router-view :height="currentHeight" :width="currentWidth"></router-view>
                    </div>
                </div>
            </div>
            <resize-observer @notify="handleResize"></resize-observer>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            width: 0,
            height: 0,
        };
    },
    mounted() {
        Vue.nextTick( () => {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
        });
    },
    props: {
        fullname: String,
    },
    methods: {
        handleResize ({ width, height }) {
            this.width = width;
            this.height = height;
        },
    },
    watch: {
        authLoaded(shouldLogIn) {
            if (shouldLogIn) {
                this.$auth.loginWithRedirect();
            }
        }
    },
    computed: {
        currentWidth() {
            return this.width;
        },
        currentHeight() {
            return this.height;
        },
        authLoaded() {
            return this.$auth.auth0Client !== null && !this.$auth.loading && !this.$auth.isAuthenticated;
        },
    },
}
</script>

<style scoped>
.screensize {
    position: relative;
}
</style>
