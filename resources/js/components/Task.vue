<template>
    <div>
        <div class="row plist">
            <div class="col"><input type="checkbox" id="{item.uuid}" class="checkbox"></div>
            <div class="col">
                <a href="">
                    <span class="linked">
                {{bag.name}}
                    </span>
                </a>
            </div>
            <div class="col">
                {{item.fileName}}
            </div>
            <div class="col">
                {{item.created_at}}
            </div>
            <div class="col listActionItems">
                <i class="fas fa-cog"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>&nbsp;
            </div>
            <div class="col piqlIt">&nbsp;</div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
        console.log('Task component mounted.')
        let bagId = this.item.bag_id;
        this.bag = (await axios.get("/api/v1/ingest/holdings/"+bagId)).data; 
    },
    props: {
        item: Object
    },
    data() {
        return {
            bag: {}
        };
    },

}
</script>
