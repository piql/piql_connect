<template>
    <div>
        <div class="row plist">
            <div class="col-1"><input type="checkbox" id="{item.uuid}" class="checkbox"></div>
            <div class="col-2">
                <a href="">
                    <span class="linked">
                {{item.name}}
                    </span>
                </a>
            </div>
            <div class="col-3">
                {{fileName}}
            </div>
            <div class="col">
                {{item.created_at}}
            </div>
            <div class="col listActionItems">
                <i class="fas fa-cog"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>&nbsp;
            </div>
            <div class="col piqlIt" v-on:click="piqlIt">&nbsp;</div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
        console.log('Task component mounted.')
        let bagId = this.item.id;
        this.fileName = (await axios.get("/api/v1/ingest/bags/"+bagId+"/files")).data[0].filename;
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
            bag: {},
            fileName: "",
        };
    },

}
</script>
