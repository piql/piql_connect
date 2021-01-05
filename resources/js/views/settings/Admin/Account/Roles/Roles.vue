<template>
    <div class="w-100">
        <page-heading icon="fa-user-shield" :title="$t('settings.settings.roles')" :ingress="$t('settings.settings.rolesDesc')" />
        <div class="card">
            <div class="card-header">
                <span v-if="showAddRole"><i class="fa fa-plus"></i>  {{$t('settings.roles.addRole').toUpperCase()}} | 
                <a href="#" class="btn btn-sm" @click="displayRoles">{{$t('settings.roles.backToRoles')}}</a>
                </span>
        
                <button v-else type="button" class="btn btn-primary" @click="displayAddRole">
                    <i class="fa fa-plus"></i>  {{$t('settings.roles.addRole')}}
                </button>
            </div>
            <div class="card-body">
                <new-role v-if="showAddRole" @addRole='addRole'></new-role>
                <role-items v-else :key="rolekey" @assignGroup="assignGroup" @editRole="editRole" @deleteRole='deleteRole' />
                
            </div>
        </div>

        
    </div>
</template> 

<script>
import { mapActions, mapGetters } from "vuex";

    export default {
     
       
        data() {
            return {
                rolekey: 0,
                showAddRole: false,
            };
        },
        computed:{
            ...mapGetters('roles',
                ['rolesApiResponse']
            ),
            
        },
        watch:{
            rolesApiResponse(newValue){
                //will run on success or failure of any post operation
                if(newValue && (newValue.status >= 200 && newValue.status <= 299)){
                    this.successToast('Success: ' + newValue.status ,newValue.message);
                }else if(newValue && newValue.status){
                    this.errorToast('Error: ' + newValue.status,newValue.message);
                }
            }

        },

        methods: {
            ...mapActions('roles',
                ['postNewRole','updateRole','removeRole','postRolesToGroup']
            ),
            displayAddRole(){
                this.showAddRole = true;

            },
            displayRoles(){
                this.showAddRole = false;

            },
            forceRerender(){
                this.rolekey += 1;

            },
            async addRole(form){
                this.infoToast('Add Role','Adding '+ form.role + ' role');
                let data = {
                    name: form.role,
                    description: form.description
                }

                this.postNewRole(data);
                
                this.forceRerender();
                this.displayRoles();
            },

            async editRole(data){
                this.infoToast('Edit Role','Editing '+ data.name);
                
                this.updateRole(data);
                
                this.forceRerender();
                this.$bvModal.hide('edit-role');
                
            },
            async deleteRole(roleId){
                this.infoToast('Delete Role','Deleting a role ');

                this.removeRole(roleId);
                
                this.forceRerender();
                this.$bvModal.hide('delete-role')
            },
            async assignGroup(data){
                this.infoToast("Assigning group", "assigning group to role selected");

                this.postRolesToGroup(data);

                this.forceRerender();
                this.$bvModal.hide('assign-role');
            }
        }
    }
</script>

