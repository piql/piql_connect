<template>
    <div>
        <div class="row plist">
            <div class="col">
                {{item.name}}
            </div>
            <div class="col">
                {{filename}} 
            </div>
            <div class="col">
                {{item.created_at}}
            </div>
            <div class="col">
                Ingesting
            </div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
        console.log('FileInProcess component mounted.')
        let bagId = this.item.id;
        this.filename = (await axios.get("/api/v1/ingest/bags/"+bagId+"/files")).data[0].filename;
    },
    data() {
        return {
            bag: {},
            filename: "",
        };
    },
    props: {
        item: Object
    }
}
</script>
