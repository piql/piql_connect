<template>
    <div class="pageHeader">
        <div class="row titleRow">
            <div class="d-inline-flex">
                <img :src="iconImg" v-if="iconImg != null"/>
                <i :class="icon" class="fas titleIcon" v-if="iconImg == null"></i>
                <span class="titleText">{{title}}</span>
            </div>
        </div>

        <div class="row">
            <div class="col ingressText">{{ingress}}</div>
        </div>
    </div>

</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            default: "as-hdd"
        },
        title: {
            type: String,
            default: "Page Title"
        },
        ingress: {
            type: String,
            default: "Page description"
        },
        iconImg: {
            type: String,
            default: null
        },

    },
};
</script>
