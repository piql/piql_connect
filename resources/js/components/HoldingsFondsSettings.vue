<template>
    <div>
        <form class="form mb-3" v-on:submit.prevent>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4 col-lg-4 col-xs-4">
                        <holding-picker :holdings='holdings' :initialSelectedHolding='selectedHolding' @holdingSelectionChanged='holdingSelectionChanged'></holding-picker>
                    </div>
                </div>
            </div>
        </form>
       <hr class="row m-0">
        <div class="row">
            <div class="col-sm-4 col-lg-4 col-xs-4 mt-5">
                <fond-select @fondSelectionChanged="fondSelectionChanged"></fond-select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            fondSelectCounter: 0,
            lastSelectedFond: "",
            selectedFond: "",
            selectedHolding: "H-002",
            holdings: [],
        }
    },
    computed: {
        fondSelected: function() {
            return this.fondSelectCounter > 0;
        },

        completeFilter: function() {
            let filter = "?holding=" + encodeURI(this.selectedHolding);
            filter += "&loc=" + encodeURI(this.selectedLocation);
            if(this.selectedFond){
                filter += "&fond=" + encodeURI(this.selectedFond);
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
        this.holdings = (await axios.get("/api/v1/planning/holdings")).data.data;
    },
    methods: {
        fondSelectionChanged: function(fond, state) {
            if(state){
                this.lastelectedFond = fond.data.name;
                this.fondSelectCounter++;
                this.selectedFond = fond.data.name;
            }
            else{
                this.fondSelectCounter--;
                if(this.fondSelectCounter === 0)
                {
                    this.selectedFond = "";
                }
            }

        },
        holdingSelectionChanged: function(holding) {
            this.selectedHolding = holding;
        },
        locationSelectionChanged: function(location) {
            this.selectedLocation = location;
        }
    },
}
</script>
