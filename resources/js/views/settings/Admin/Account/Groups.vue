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
import { mapGetters, mapActions } from "vuex";

    export default {
       
        data() {
            return {
                group:null,
                description:null,
                groupkey: 0,
            };
        },
        computed: {
            ...mapGetters(['groupsApiResponse']),
            
        },
        watch:{
            groupsApiResponse(newValue,prevValue){
                //will run on success or failure of any post operation
                if(newValue && (newValue.status >= 200 && newValue.status <= 299)){
                    this.successToast('Success: ' + newValue.status ,newValue.message);
                }else if(newValue && newValue.status){
                    this.errorToast('Error: ' + newValue.status,newValue.message);
                }
            }

        },
        methods: {
            ...mapActions(['postNewGroup','postRolesToGroup','postUsersToGroup']),
            forceRerender(){
                this.groupkey += 1;

            },
            addGroup(){
                this.infoToast('Add Group','Adding '+ this.group);

                //bundle the data
                let data = {
                    name: this.group,
                    description: this.description
                }
                
                //access vuex action
                this.postNewGroup(data)

                //refresh and hide group modal
                this.forceRerender();
                this.$bvModal.hide('add-group');
                
            },
            async assignGroupToRoles(data){
                this.infoToast("Assigning group", "assigning group to roles selected");

                //push to vuex action
                this.postRolesToGroup(data);

                this.forceRerender();
                this.$bvModal.hide('assign-group');
            },

            async assignGroupToUsers(data){
                this.infoToast("Assigning Users", "assigning group to users selected");
                //push to vuex action
                this.postUsersToGroup(data);

                this.forceRerender();
                this.$bvModal.hide('assign-users');

            }
        }
    }
</script>

