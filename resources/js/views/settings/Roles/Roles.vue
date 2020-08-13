<template>
    <div class="w-100">
        <page-heading icon="fa-user-shield" :title="$t('settings.settings.roles')" :ingress="$t('settings.settings.rolesDesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-role')">
                    <i class="fa fa-plus"></i>  {{$t('settings.roles.addRole')}}
                </button>
                <b-modal id="add-role" hide-footer>
                    <template v-slot:modal-title>
                    {{$t('settings.roles.addRole')}}
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>{{$t('settings.roles.role')}}</label>
                            <input type="text" class="form-control" v-model="role" >
                        </div>
                        <div class="form-group">
                            <label>{{$t('settings.groups.description')}}</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addRole" @keydown="addRole"><i class="fa fa-user-shield"></i> {{$t('settings.roles.addRole')}}</b-button>
                </b-modal>
            </div>
            <div class="card-body">
                <role-items :key="rolekey" @assignGroup="assignGroup" @editRole="editRole" @deleteRole='deleteRole' />
            </div>
        </div>

        
    </div>
</template> 

<script>
import { mapActions, mapGetters } from "vuex";

    export default {
     
       
        data() {
            return {
                role:null,
                description:null,
                response: null,
                rolekey: 0,
            };
        },
        computed:{
            ...mapGetters(['rolesApiResponse'])
        },
        watch:{
            rolesApiResponse(newValue,prevValue){
                //will run on success or failure of any post operation
                if(newValue && (newValue.status >= 200 && newValue.status <= 299)){
                    this.successToast('Success: ' + newValue.status ,newValue.message);
                }else if(newValue && newValue.status){
                    this.errorToast('Error: ' + newValue.status,newValue.message);
                }
            }

        },

        methods: {
            ...mapActions(['postNewRole','updateRole','removeRole','postRolesToGroup']),
            forceRerender(){
                this.rolekey += 1;

            },
            async addRole(){
                this.infoToast('Add Role','Adding '+ this.role + ' role');
                let data = {
                    name: this.role,
                    description: this.description
                }

                this.postNewRole(data);
                
                this.forceRerender();
                this.$bvModal.hide('add-role')
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

