<template>
    <div class="w-100">
        <page-heading icon="fa-user-secret" :title="$t('settings.settings.roles')" :ingress="$t('settings.settings.rolesdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-role')">
                    <i class="fa fa-plus"></i>  Add Role
                </button>
                <b-modal id="add-role" hide-footer>
                    <template v-slot:modal-title>
                   <h4> Add Role </h4>
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" v-model="role" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="description" class="form-control" required="required"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addRole"><i class="fa fa-user-secret"></i> Add Role</b-button>
                </b-modal>
            </div>
            <div class="card-body">
                <roles-listing :key="rolekey" @assignRoleToUsers="assignRoleToUsers" />
               
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
                response:null,
                rolekey: 0,
                msg:null
            };
        },

        methods: {
            forceRerender(){
                this.rolekey += 1;

            },
            async addRole(){
                if((this.role != null) && (this.description != null)){
                    this.infoToast('Add Role','Adding '+ this.role);
                    this.response = (await axios.post("/api/v1/admin/permissions/roles", {
                        name: this.role,
                        description: this.description
                    },{
                        headers:{
                            'content-type': 'application/json'
                        }
                    })).data;
                    
                    this.forceRerender();
                    this.$bvModal.hide('add-role');
                }else{
                    this.errorToast("Error","Fill in both fields");
                    this.forceRerender();
                    this.$bvModal.hide('add-role');
                }
                
            },
            async assignRoleToUsers(data){
                this.infoToast("Assigning role", "assigning role to a group of selected users");
                this.response = (await axios.post("/api/v1/admin/permissions/users/assign",data,{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                this.forceRerender();
                this.$bvModal.hide('assign-role');
            }
        }
    }
</script>

