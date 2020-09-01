<template>
    <div class="w-100">
        <page-heading icon="fa-user-shield" 
        :title="$t('settings.settings.rolePermissions') + ' [ '+ role.name + ' ]'" 
        :ingress="$t('settings.settings.rolePermDesc')" />
        <div class="card">
            <div class="card-header">
                CONFIGURE PERMISSIONS
        
                <!-- <button type="button" class="btn btn-primary" @click="$bvModal.show('add-permission')">
                    <i class="fa fa-key"></i>  Add Role Permission
                </button>
                <b-modal id="add-permission" hide-footer>
                    <template v-slot:modal-title>
                   <h4> Add Role Permission </h4>
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>Permission</label>
                        <input type="text" class="form-control" v-model="permission" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="description" class="form-control" required="required"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addPermission"><i class="fa fa-key"></i> Add Permission</b-button>
                </b-modal> -->
            </div>
            <div class="card-body">
                <!-- <permissions-listing 
                :key="permkey" 
                @deletePermission="deletePermission" 
                @editPermission="editPermission" 
                @assignRoleToPermission="assignRoleToPermission"></permissions-listing> -->
               
            </div>
        </div>

        
    </div>
</template>

<script>

    export default {
       
        data() {
            return {
                permission:null,
                description:null,
                response:null,
                permkey: 0,
                msg:null,
                role: null
            };
        },
        async mounted(){
            let roleId = this.$route.params.roleId;

            await axios.get("/api/v1/admin/access-control/permission-groups/"+ roleId).then(res => {
                this.role = res.data.data;
            });



        },

        methods: {
            forceRerender(){
                this.permkey += 1;

            },
            async addPermission(){
                if((this.permission != null) && (this.description != null)){
                    let roleId = this.$route.params.roleId;

                    this.infoToast('Add Group Permission','Adding '+ this.permission);
                    this.response = (await axios.post('/api/v1/admin/access-control/permission-groups/'+ roleId +'/permission', {
                        name: this.permission,
                        description: this.description
                    },{
                        headers:{
                            'content-type': 'application/json'
                        }
                    })).data;
                    
                    this.forceRerender();
                    this.$bvModal.hide('add-permission');
                }else{
                    this.errorToast("Error","Fill in both fields");
                    this.forceRerender();
                    this.$bvModal.hide('add-permission');
                }
                
            },
            async assignRoleToPermission(data){
                this.infoToast("Assigning Role", "assigning role to a permission");
                await axios.put("/api/v1/admin/access-control/permissions/"+ data.permissionId +"/set-group/"+ data.roleId).then(res => {
                    this.response = res;
                }).catch(error => {
                    this.response = error;
                    this.errorToast('Assign Failed',"Unable to assign permission, check endpoint");
                });

                this.forceRerender();
                this.$bvModal.hide('assign-perm');

            },
            async editPermission(data){
                this.infoToast('Edit Permission','Editing '+ data.name);
                 await axios.put("/api/v1/admin/access-control/permissions/"+ data.permId, {
                    name: data.name,
                    description: data.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                }).then(res => {
                    this.response = res;
                }).catch(err => {
                    this.response = err;
                });
                
                this.forceRerender();
                this.$bvModal.hide('edit-perm');
                
            },
            async deletePermission(permId){
                this.infoToast('Delete Permission','Deleting an permission ');
                await axios.delete("/api/v1/admin/access-control/permissions/"+ permId).then(res => {
                    this.response = res;
                }).catch(err => {
                    this.response = err;
                    this.errorToast('Permission Delete Failed',"Unable to delete permission, check endpoint");
                });
                
                this.forceRerender();
                this.$bvModal.hide('delete-perm')
            }
        }
    }
</script>

