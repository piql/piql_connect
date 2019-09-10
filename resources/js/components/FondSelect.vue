<template>
    <div>
        <ul class="tree-list-group">
            <li class="tree-list-group-item d-flex justify-content-between align-items-center active">
                {{ $t('access.browse.selectHolding') }}
            </li>
            <li class="tree-list-group-item d-flex justify-content-between align-items-center">
                <b-tree-view :data="treeData" v-on:nodeSelect="fondSelected" :class="list-group-item" :contextMenuItems="contextMenuItems"></b-tree-view>
            </li>
            <li class="tree-list-group-item d-flex justify-content-between align-items-center">
            </li>
        </ul>
    </div>
</template>
<script>
import axios from 'axios';
import { bTreeView } from 'bootstrap-vue-treeview';
export default {
    components: {
        bTreeView
    },
    props: {
        fondSelectionChanged: {
            type: Function
        },
        contextMenuItems: {
            type: Array,
            default: () => {
                return [
                ]
            }
        },

    },
    data() {
        return {
            list: [],
            group: {},
            item: {},
            contextMenuItems: [],
            demoTree: [
                {
                    "id": 1, "name": "Dokumenter" , 
                    "children": [
                        {"id": 11, "name": "Kjøretøy"}, 
                        {"id": 12, "name": "Artilleri"} ] 
                },
                {
                    "id": 2, "name": "Fotogafi" , 
                    "children": [
                        {"id": 21, "name": "Samband"}, 
                        {"id": 22, "name": "Våpen"} ] 
                },
                {
                    "id": 3, "name": "Lyd" , 
                    "children": [
                        {"id": 31, "name": "Samband"}, 
                        {"id": 32, "name": "Opptak"} ] 
                }

            ]
        }
    },

    methods: {
        fondSelected: function (fond, state) {
            this.$emit('fondSelectionChanged', fond, state);
        }
    },
    mounted() {
    },
    computed: {
        treeData: function() {
            console.log(this.holdings);
            return this.holdings.map( (holding) => {
                return {'id' : holding.id, 'name' : holding.title };
            });
        }
    },
    props: ['holdings']
}
</script>
