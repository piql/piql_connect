<template>
  <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('settings.settings.groups')" :ingress="$t('settings.settings.groupsdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-action')">
                    <i class="fa fa-plus"></i>  Add Action
                </button>

                <b-modal id="add-action" hide-footer>
                    <template v-slot:modal-title>
                    Add Action
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>Action</label>
                        <input type="text" class="form-control" v-model="action" >
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="description" class="form-control"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addAction"><i class="fa fa-tag"></i> Add Action</b-button>
                </b-modal>
                
            </div>
            <div class="card-body">
                <user-group-actions :key="actionkey" :groupid="groupId" />
            </div>
        </div>

        
    </div>
</template>

<script>
export default {
     
       
        data() {
            return {
                action:null,
                description:null,
                response: null,
                actionkey: 0,
                groupId: this.$route.params.groupId
            };
        },

        methods: {
            forceRerender(){
                this.actionkey += 1;

            },

            async addAction(){
                this.infoToast('Add Group Action','Adding '+ this.action);
                 this.response = (await axios.post("/api/v1/admin/permissions/groups/"+ this.groupId +"/action", {
                    name: this.action,
                    description: this.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
                
                this.forceRerender();
                this.$bvModal.hide('add-action')
                
            }
        }
    }
</script>

<style>

</style>