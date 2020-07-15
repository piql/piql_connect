<template>
    <div class="w-100">
        <page-heading icon="fa-user-shield" :title="$t('settings.settings.groups')" :ingress="$t('settings.settings.groupsdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-group')">
                    <i class="fa fa-plus"></i>  Add Group
                </button>
                <b-modal id="add-group" hide-footer>
                    <template v-slot:modal-title>
                    Add Group
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Group</label>
                            <input type="text" class="form-control" v-model="group" >
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addGroup" @keydown="addGroup"><i class="fa fa-group"></i> Add Group</b-button>
                </b-modal>
            </div>
            <div class="card-body">
                <user-group-items :key="groupkey" @editGroup="editGroup" @deleteGroup='deleteGroup' />
            </div>
        </div>

        
    </div>
</template> 

<script>

    export default {
     
       
        data() {
            return {
                group:null,
                description:null,
                response: null,
                groupkey: 0,
            };
        },

        methods: {
            forceRerender(){
                this.groupkey += 1;

            },
            async addGroup(){
                this.infoToast('Add Group','Adding '+ this.group + ' accesss group');
                 this.response = (await axios.post("/api/v1/admin/access-control/permission-groups", {
                    name: this.group,
                    description: this.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
                
                this.forceRerender();
                this.$bvModal.hide('add-group')
            },

            async editGroup(data){
                this.infoToast('Edit Group','Editing '+ data.name);
                 this.response = (await axios.put("/api/v1/admin/access-control/permission-groups/"+ data.groupId, {
                    name: data.name,
                    description: data.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
                
                this.forceRerender();
                this.$bvModal.hide('edit-group');
                
            },
            async deleteGroup(groupId){
                this.infoToast('Delete Group','Deleting an access group ');
                await axios.delete("/api/v1/admin/access-control/permission-groups/"+ groupId).then(res => {
                    this.response = res;
                }).catch(err => {
                    this.response = err;
                    this.errorToast('Group Delete Failed',"Unable to delete group, check endpoint");
                });
                
                this.forceRerender();
                this.$bvModal.hide('delete-group')
            }
        }
    }
</script>

