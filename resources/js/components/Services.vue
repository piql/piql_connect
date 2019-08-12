<template>
    <div>
       <div class="row plistHeader">
            <div class="col-1">Ping</div>
            <div class="col">Url</div>
            <div class="col">Token</div>
            <div class="col">Id</div>
            <div class="col-1">Status</div>
        </div>

        <div class="plist" style="height: 500px; overflow-y: scroll !important;">
            <Service v-for="item in items" v-bind:item="item" v-bind:key="item.id" />
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            items : {},
        };
    },
    async mounted() {
        console.log('Services component mounted.')
        axios.get("/api/v1/ingest/am/instances").then( (response) => {
            this.items = response.data;
        });
    },
    methods: {
       
    }
}
</script>
