<template>
    <div>
        <ul class="tree-list-group">
            <li class="tree-list-group-item d-flex justify-content-between align-items-center active">
                {{ $t('access.browse.selectHolding') }}
            </li>
            <li class="tree-list-group-item d-flex justify-content-between align-items-center">
                <b-tree-view :data="treeData" :select="select" v-on:nodeSelect="fondSelected" :class="list-group-item" :contextMenuItems="contextMenuItems" :contextMenu="useContextMenu"></b-tree-view>
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
                return [];
            }
        },
        useContextMenu: {
            type: Boolean,
            default: () => {
                return false;
            }
        },
        select: {
            type: String,
            default: () => {
                return "";
            }
        },
        holdings: {
            type: Array,
            default: () => {
                return [];
            }
        }
    },
    data() {
        return {
            list: [],
            group: {},
            item: {},
            placeholderTree: [
                {"id" : 1, "name" : "" /*, 
                    "children": [
                        {"id": 11, "name": ""}, 
                    ]*/
                }
            ],
       }
    },

    methods: {
        fondSelected: function (fond, state) {
            this.$emit('fondSelectionChanged', fond, state);
        },
    },
    mounted() {
    },
    computed: {
        treeData: function() {
            if(this.holdings.length == 0){
                return this.placeholderTree;
            }
            return [{'id' : 0, 'name' : 'All' }].concat(this.holdings.map( (holding) => {
                return {'id' : holding.id, 'name' : holding.title };
            }));
        }
    },
}
</script>
