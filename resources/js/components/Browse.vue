<template>
    <div>
        <form class="form mb-3" v-on:submit.prevent>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xs-1">
                        <holding-picker :holdings='holdings' :initialSelectedHolding='selectedHolding' @holdingSelectionChanged='holdingSelectionChanged'></holding-picker>
                    </div>

                    <div class="col-sm-2">
                        <label for="fromDate" class="col-form-label-sm">{{$t('access.browse.archivedFrom')}}</label>
                        <input v-model="fromDateFilter" id="fromDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-2">
                        <label for="toDate" class="col-form-label-sm">{{$t('access.browse.archivedTo')}}</label>
                        <input v-model="toDateFilter" id="toDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-3 form-group">
                        <label for="searchContents" class="col-form-label-sm">{{$t('access.browse.withContents')}}</label>
                        <div class="input-group">
                            <div class="input-group addon">
                                <input v-model="searchField" id="searchContents" type="text" class="form-control">
                                <span class="input-group-addon">
                                    <i class="fas fa-search search-icon-inline"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 ml-5">
                        <location-picker :initialSelectedLocation="selectedLocation" :locations="locations" @locationSelectionChanged="locationSelectionChanged"></location-picker>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col">
                current filters: {{ completeFilter }}
            </div>
        </div>

        <hr class="row m-0">
        <div class="row">
            <div class="col-sm-3 col-lg-2 col-xs-1 mt-5">
                <fond-select @fondSelectionChanged="fondSelectionChanged"></fond-select>
            </div>
            <div class="col-sm-8">
                <browser-list v-if="fondSelected" :selectedFond="lastSelectedFond"></browser-list>
                <identity v-else></identity>
            </div>
            <div class="col-sm-2 mt-5">
                <online-actions v-if="fondSelected"></online-actions>
                <primary-contact v-else></primary-contact>
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
            fromDateFilter: "",
            toDateFilter: "",
            searchField: "",
            selectedLocation: "online",
            locations: [
                { name: 'Online', value: 'online' },
                { name: 'Offline', value: 'offline'}
            ],
            selectedHolding: "H-002",
            holdings: [
                { name: 'Forsvarsmuseet', value: 'H-001' },
                { name: 'Bergenhus', value: 'H-002' },
                { name: 'Hjemmefrontmuseet', value: 'H-003' },
                { name: 'Luftforsvarsmuseet', value: 'H-004' },
                { name: 'Marinemuseet', value: 'H-005' },
                { name: 'Oscarsborg', value: 'H-006' },
                { name: 'Rustkammeret', value: 'H-007' }
            ]
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
    mounted() {
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
