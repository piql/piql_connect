<template>
    <div class="w-100">
        <page-heading icon="fa-key" :title="$t('settings.settings.permissions')" :ingress="$t('settings.settings.permissionsdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-permission')">
                    <i class="fa fa-key"></i>  Add Permission
                </button>
                <b-modal id="add-permission" hide-footer>
                    <template v-slot:modal-title>
                   <h4> Add Permission </h4>
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
                </b-modal>
            </div>
            <div class="card-body">
                <permissions-listing :key="permkey" @assignGroupToPermission="assignGroupToPermission"></permissions-listing>
               
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
                msg:null
            };
        },

        methods: {
            forceRerender(){
                this.permkey += 1;

            },
            async addPermission(){
                if((this.permission != null) && (this.description != null)){
                    this.infoToast('Add Permission','Adding '+ this.permission);
                    this.response = (await axios.post('/api/v1/admin/access-control/permissions', {
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
            async assignGroupToPermission(data){
                this.infoToast("Assigning group", "assigning group to a permission of selected users");
                this.response = (await axios.put("/api/v1/admin/permissions/"+ data.permissionId +"/set-group/"+ data.groupId)).data;

                this.forceRerender();
                this.$bvModal.hide('assign-perm');

            }
        }
    }
</script>

