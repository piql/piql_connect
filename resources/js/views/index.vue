<template>
    <div class="screensize" style="height:100%">
        <div class="container-fluid p-0" style="height:inherit;">
            <top-bar />
            <div class="d-flex flex-row overflow-auto" style="height:inherit;">
                <div class="d-flex" style="min-height:inherit; max-height:5000px; height:inherit; margin-top:130px;">
                    <side-bar :width="currentWidth" :fullname="fullname" />
                </div>
                <div class="d-flex flex-nowrap mr-auto ml-auto pr-4 pl-4 w-100 justify-content-center">
                    <router-view :height="currentHeight" :width="currentWidth"></router-view> 
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
        currentHeight() {
            return this.height;
        }
    },
}
</script>

<style scoped>
.screensize {
    position: relative;
}
</style>
