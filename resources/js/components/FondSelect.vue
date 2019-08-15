<template>
    <div>
        <ul class="tree-list-group">
            <li class="tree-list-group-item d-flex justify-content-between align-items-center active">
                {{ $t('access.browse.selectFond') }}
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
                        { code: 'DELETE_NODE', label: '1' }, 
                        { code: 'RENAME_NODE', label: '2' } 
                    ]
                }
            },
 
    },
    data() {
        return {
           list: [],
            group: {},
            item: {},

            treeData: [
                {
                    "id": 1, "name": "Documents" , 
                    "children": [
                        {"id": 11, "name": "Neptune"}, 
                        {"id": 12, "name": "Stratus"} ] 
                },
                {
                    "id": 2, "name": "Audio" , 
                    "children": [
                        {"id": 21, "name": "Moonlanding Recording"}, 
                        {"id": 22, "name": "Ziggy Stardust by David Bowie"} ] 
                },
                {
                    "id": 3, "name": "Video" , 
                    "children": [
                        {"id": 31, "name": "Moonlanding Footage"}, 
                        {"id": 32, "name": "SpaceX - Of course I still love you"} ] 
                }

            ]
        }
    },

    methods: {
        fondSelected: function (fond, state) {
            this.$emit('fondSelectionChanged', fond, state);
        }
    }
}
</script>
