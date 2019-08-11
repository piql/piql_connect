<template>
    <div>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center active">
                {{ $t('access.browse.selectFond') }}
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
            <b-tree-view :data="treeData" :class="list-group-item"></b-tree-view>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
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
    data() {
        return {
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
        doSomething: () => {
            console.log("Did something");
        }
    }
}
</script>
