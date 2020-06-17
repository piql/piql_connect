<template>
    <div class="w-100">
        <page-heading icon="fa-users" :title="$t('settings.settings.groups')" :ingress="$t('settings.settings.groupsdesc')" />
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
                <user-group-items :key="groupkey" @addAction="addAction" />
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
                this.infoToast('Add Group','Adding '+ this.group + ' user group');
                 this.response = (await axios.post("/api/v1/admin/permissions/groups", {
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

            async addAction(action){
                this.infoToast('Add Group Action','Adding '+ action.name);
                 this.response = (await axios.post("/api/v1/admin/permissions/groups/"+ action.groupId +"/action", {
                    name: action.name,
                    description: action.description
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

