<template>
    <div class="row plistHeader text-truncate text-center mt-2">
        <div v-show="!colA.hidden" :class="colA.css">{{colA.slot}}</div>
        <div v-show="!colB.hidden" :class="colB.css">{{colB.slot}}</div>
        <div v-show="!colC.hidden" :class="colC.css">{{colC.slot}}</div>
        <div v-show="!colD.hidden" :class="colD.css" v-html="colD.slot" @click="btnDClicked"></div>
    </div>
</template>

<script>
//* //HACK: Very basic component for list headers.
//  Needs refactoring to support arbitrary columns and actions,probably by using a list of slots.
//*

export default {
    props: {
        colA: {
            type: Object,
            default: () => {
                return {
                    css: "col-sm-2",
                    slot: "column A",
                    hidden: true
                };
            },
        },
        colB: {
            type: Object,
            default: () =>{
                return {
                    css: "col-sm-7 text-left",
                    slot: "column B",
                    hidden: true
                };
            }
        },
        colC: {
            type: Object,
            default: () => {
                return {
                    css: "col-sm-2 text-center",
                    slot: "column C",
                    hidden: true
                };
            }
        },
        colD: {
            type: Object,
            default: () => {
                return {
                    css: "col-sm-1",
                    slot: "column D",
                    hidden: true
                };
            }
        }

    },

    methods: {
        btnDClicked: function() { this.$emit('btnDClicked'); }
    }
};
</script>
