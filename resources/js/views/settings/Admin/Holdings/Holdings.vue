<template>
<div class="w-100">
    <page-heading icon="fa-folder" :title="$t('settings.holdings.title')" :ingress="$t('settings.holdings.description')" />
    <breadcumb/>
    <div class="card">
        <div class="card-header">
            <span v-if="enableMetaForm"><i class="fa fa-tags"></i> {{ $t('settings.holdings.assignMeta').toUpperCase() }} | 
            <button class="btn btn-primary" @click="disableMetaForm">{{$t('settings.holdings.backToHoldings')}}</button>
            </span>
            <button v-else class="btn" @click="newHoldingForm"><i class="fa fa-plus"></i> {{$t('settings.holdings.add')}}</button>
        </div>
        <div class="card-body">
            <holding-metadata v-if="enableMetaForm" :holdingId="holdingId" @disableMetaForm='disableMetaForm' />
            <holdings-listing v-else @assignMeta='assignMeta' />
        </div>
    </div>

</div>
  
</template>

<script>
export default {
    data() {
        return {
            enableMetaForm: false,
            holdingId: null
        }
    },
    methods: {
        newHoldingForm(){
            this.$router.push({ name:'settings.admin.holdings.create'});
        },
        assignMeta(holdingId){
            this.enableMetaForm = true;
            this.holdingId = holdingId

        },
        disableMetaForm(){
            this.enableMetaForm = false;
        }
    }

}
</script>
