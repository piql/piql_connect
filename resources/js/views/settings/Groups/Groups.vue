<template>
    <div class="w-100">
        <page-heading icon="fa-users" :title="$t('settings.settings.userGroups')" :ingress="$t('settings.settings.userGroupDesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-group')">
                    <i class="fa fa-plus"></i>  {{$t('settings.groups.addGroup')}}
                </button>
                <b-modal id="add-group" hide-footer>
                    <template v-slot:modal-title>
                   <h4> {{$t('settings.groups.addGroup')}} </h4>
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>{{$t('settings.groups.group')}}</label>
                        <input type="text" class="form-control" v-model="group" required>
                    </div>
                    <div class="form-group">
                        <label>{{$t('settings.groups.description')}}</label>
                        <textarea v-model="description" class="form-control" required="required"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addGroup"><i class="fa fa-users"></i> {{$t('settings.groups.addGroup')}}</b-button>
                </b-modal>
            </div>
            <div class="card-body">
                <groups-listing :key="groupkey" @assignGroupToRoles="assignGroupToRoles" @assignGroupToUsers="assignGroupToUsers" />
               
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
                response:null,
                groupkey: 0,
                msg:null
            };
        },

        methods: {
            forceRerender(){
                this.groupkey += 1;

            },
            async addGroup(){
                if((this.group != null) && (this.description != null)){
                    this.infoToast('Add Group','Adding '+ this.role);
                    this.response = (await axios.post('/api/v1/admin/access-control/roles', {
                        name: this.group,
                        description: this.description
                    },{
                        headers:{
                            'content-type': 'application/json'
                        }
                    })).data;
                    
                    this.forceRerender();
                    this.$bvModal.hide('add-group');
                }else{
                    this.errorToast("Error","Fill in both fields");
                    this.forceRerender();
                    this.$bvModal.hide('add-group');
                }
                
            },
            async assignGroupToRoles(data){
                this.infoToast("Assigning group", "assigning group to roles selected");
                this.response = (await axios.post("/api/v1/admin/access-control/roles/"+ data.groupId +"/permissions",{
                    permissions: data.roles
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                this.forceRerender();
                this.$bvModal.hide('assign-group');
            },

            async assignGroupToUsers(data){
                this.infoToast("Assigning Users", "assigning group to users selected");
                this.response = (await axios.post("/api/v1/admin/access-control/users/assign",data,{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                this.forceRerender();
                this.$bvModal.hide('assign-users');

            }
        }
    }
</script>

