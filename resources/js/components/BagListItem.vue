<template>
    <div>
        <div class="row plist">
            <div class="col-1"><input type="checkbox" :id=item.uuid class="checkbox"></div>
            <div class="col">
                {{item.name}}
            </div>
            <div class="col-3">
                {{item.created_at}}
            </div>
            <div class="col-2 listActionItems">
                <i class="fas fa-tags"></i>&nbsp;
                <i class="fas fa-list-ul"></i>
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
