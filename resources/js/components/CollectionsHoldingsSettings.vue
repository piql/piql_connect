<template>
    <div>
        <form class="form mb-3" v-on:submit.prevent>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4 col-lg-4 col-xs-4">
                        <collection-picker :collections='collections' :initialSelectedCollection='selectedCollection' @collectionSelectionChanged='collectionSelectionChanged'></collection-picker>
                    </div>
                </div>
            </div>
        </form>
       <hr class="row m-0">
        <div class="row">
            <div class="col-sm-4 col-lg-4 col-xs-4 mt-5">
                <holding-select @holdingSelectionChanged="holdingSelectionChanged"></holding-select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            holdingSelectCounter: 0,
            lastSelectedHolding: "",
            selectedHolding: "",
            selectedCollection: "H-002",
            collections: [],
        }
    },
    computed: {
        holdingSelected: function() {
            return this.holdingSelectCounter > 0;
        },

        completeFilter: function() {
            let filter = "?collection=" + encodeURI(this.selectedCollection);
            filter += "&loc=" + encodeURI(this.selectedLocation);
            if(this.selectedHolding){
                filter += "&holding=" + encodeURI(this.selectedHolding);
            }
            if(this.fromDateFilter){
                filter += "&from=" + encodeURI(this.fromDateFilter);
            }
            if(this.toDateFilter){
                filter += "&to=" + encodeURI(this.toDateFilter);
            }
            if(this.searchField){
                filter += "&search=" + encodeURI(this.searchField);
            }
            return filter;
        },
    },
    async mounted() {
        this.collections = (await axios.get("/api/v1/planning/collections")).data.data;
    },
    methods: {
        holdingSelectionChanged: function(holding, state) {
            if(state){
                this.lastelectedHolding = holding.data.name;
                this.holdingSelectCounter++;
                this.selectedHolding = holding.data.name;
            }
            else{
                this.holdingSelectCounter--;
                if(this.holdingSelectCounter === 0)
                {
                    this.selectedHolding = "";
                }
            }

        },
        collectionSelectionChanged: function(collection) {
            this.selectedCollection = collection;
        },
        locationSelectionChanged: function(location) {
            this.selectedLocation = location;
        }
    },
}
</script>
