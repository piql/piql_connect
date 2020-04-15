<template>
    <div class="mt-0 pt-0 screensize">
        <div class="row row-no-gutters mb-5">
            <div class="col-sm-12">
                <top-bar></top-bar>
            </div>
        </div>
        <div class="d-flex flex-row row-no-gutters">
            <div class="d-inline-block pl-0 pr-0" :class="responsiveSidebarColumns">
                <side-bar :width="currentWidth" :fullname="fullname" ></side-bar>
            </div>
            <div class="col d-inline-flex mr-4 ml-4">
                <router-view></router-view>
            </div>
        </div>
        <resize-observer @notify="handleResize"></resize-observer>
    </div>
</template>
<script>
export default {
    data() {
        return {
            width: 0,
            height: 0
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
    computed: {
        currentWidth() {
            return this.width;
        },
        responsiveSidebarColumns() {
            return this.width > 1100 ? "col-sm-2" : "col-sm-1 pr-2";
        }
    },
}
</script>

<style scoped>
.screensize {
    position: relative;
}
</style>
