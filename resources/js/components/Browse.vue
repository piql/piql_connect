<template>
    <div>
        <form class="form mb-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xs-1">
                        <holding-picker></holding-picker>
                    </div>

                    <div class="col-sm-2">
                        <label for="fromDate" class="col-form-label-sm">{{$t('access.browse.archivedFrom')}}</label>
                        <input id="fromDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-2">
                        <label for="toDate" class="col-form-label-sm">{{$t('access.browse.archivedTo')}}</label>
                        <input id="toDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-3 form-group">
                        <label for="searchContents" class="col-form-label-sm">{{$t('access.browse.withContents')}}</label>
                        <div class="input-group">
                            <div class="input-group addon">
                                <input id="searchContents" type="text" class="form-control">
                                <span class="input-group-addon">
                                    <i class="fas fa-search search-icon-inline"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 ml-5">
                        <location-picker></location-picker>
                    </div>
                </div>
            </div>
        </form>

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
        }
    },
    computed: {
        fondSelected: function() {
            return this.fondSelectCounter > 0;
        }
    },
    mounted() {
        console.log('Processing component mounted.')
    },
    methods: {
        fondSelectionChanged: function(fond, state) {
            if(state){
                this.lastelectedFond = fond.data.name;
                this.fondSelectCounter++;
                console.log("fond selected name: "+ fond.data.name + " id: "+fond.data.id);
            }
            else{
                this.fondSelectCounter--;
                console.log("fond deselected:"+ fond.data.name +  " id: "+fond.data.id);
            }

        }

    },
}
</script>
