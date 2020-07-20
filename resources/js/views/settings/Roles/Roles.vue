<template>
    <div class="w-100">
        <page-heading icon="fa-user-shield" :title="$t('settings.settings.roles')" :ingress="$t('settings.settings.rolesDesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-role')">
                    <i class="fa fa-plus"></i>  Add Role
                </button>
                <b-modal id="add-role" hide-footer>
                    <template v-slot:modal-title>
                    Add Role
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" v-model="role" >
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addRole" @keydown="addRole"><i class="fa fa-user-shield"></i> Add Role</b-button>
                </b-modal>
            </div>
            <div class="card-body">
                <role-items :key="rolekey" @editRole="editRole" @deleteRole='deleteRole' />
            </div>
        </div>

        
    </div>
</template> 

<script>

    export default {
     
       
        data() {
            return {
                role:null,
                description:null,
                response: null,
                rolekey: 0,
            };
        },

        methods: {
            forceRerender(){
                this.rolekey += 1;

            },
            async addRole(){
                this.infoToast('Add Role','Adding '+ this.role + ' role');
                 this.response = (await axios.post("/api/v1/admin/access-control/permission-groups", {
                    name: this.role,
                    description: this.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
                
                this.forceRerender();
                this.$bvModal.hide('add-role')
            },

            async editRole(data){
                this.infoToast('Edit Role','Editing '+ data.name);
                 this.response = (await axios.put("/api/v1/admin/access-control/permission-groups/"+ data.roleId, {
                    name: data.name,
                    description: data.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
                
                this.forceRerender();
                this.$bvModal.hide('edit-role');
                
            },
            async deleteRole(roleId){
                this.infoToast('Delete Role','Deleting a role ');
                await axios.delete("/api/v1/admin/access-control/permission-groups/"+ roleId).then(res => {
                    this.response = res;
                }).catch(err => {
                    this.response = err;
                    this.errorToast('Role Delete Failed',"Unable to delete role, check endpoint");
                });
                
                this.forceRerender();
                this.$bvModal.hide('delete-role')
            }
        }
    }
</script>

