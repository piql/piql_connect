<template>
    <div>
        <div class="row plist">
            <div class="col-1 pt-sm-3"><input type="checkbox" :id=item.uuid class="checkbox"></div>
            <div class="col-sm pt-sm-3">
                {{item.name}}
            </div>
            <div class="col-sm-3 pt-sm-3">
                {{item.created_at}}
            </div>
            <div class="col-sm-2 pt-sm-3">
                10 MB
            </div>
            <div class="col-sm-2 listActionItems d-flex flex-row justify-content-end pt-sm-1">
                <div class="mx-sm-1">
                    <i class="fas fa-list-ul cursor-pointer hover-hand"  style="font-size: 21px;" @click="onListClick()"></i>
                </div>
                <div class="mx-sm-1">
                    <i class="fas fa-trash-alt hover-hand" style="font-size: 21px;"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    async mounted() {
        console.log('Task component mounted.')
        let bagId = this.item.id;
        this.url = "tasks/"+bagId;
        axios.get("/api/v1/ingest/bags/"+bagId+"/files").then( bag => {
            if(bag && bag.data && bag.data[0]){
                this.fileName = bag.data[0].filename;
            }
        });
    },
    methods: {
        onListClick()
        {
            window.location = '/ingest/tasks/'+this.item.id;
        },
        async piqlIt(e) {
            e.preventDefault();
            let bagId = this.item.id;
            this.$emit('piqlIt', bagId);
        },

    },
    props: {
        item: Object
    },
    data() {
        return {
            fileName: "",
            url: "",
        };
    },

}
</script>
