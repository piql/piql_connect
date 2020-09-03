<template>
    <div class="w-100">
        <page-heading icon="fa-users" :title="$t('settings.settings.userGroups')" :ingress="$t('settings.settings.userGroupDesc')" />
        <div class="card">
            <div class="card-header">
                <span v-if="showAddGroup"><i class="fa fa-plus"></i>  {{$t('settings.groups.addGroup').toUpperCase()}} | 
                <a href="#" class="btn btn-sm" @click="displayGroups">{{$t('settings.groups.backToGroup')}}</a>
                </span>
        
                <button v-else type="button" class="btn btn-primary" @click="displayAddGroup">
                    <i class="fa fa-plus"></i>  {{$t('settings.groups.addGroup')}}
                </button>
                
            </div>
            <div class="card-body">
                 <add-group v-if="showAddGroup" @addGroup='addGroup'></add-group>
                <groups-listing v-else :key="groupkey" @assignGroupToRoles="assignGroupToRoles" @assignGroupToUsers="assignGroupToUsers" />
               
            </div>
        </div>

        
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

    export default {
       
        data() {
            return {
                groupkey: 0,
                showAddGroup: false,
            };
        },
        computed: {
            ...mapGetters({
                groups: ['groupsApiResponse']
            }),
            
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
            displayAddGroup(){
                this.showAddGroup = true;

            },
            displayGroups(){
                this.showAddGroup = false;

            },
            forceRerender(){
                this.groupkey += 1;

            },
            addGroup(form){
                this.infoToast('Add Group','Adding '+ form.group);

                //bundle the data
                let data = {
                    name: form.group,
                    description: form.description
                }
                
                //access vuex action
                this.postNewGroup(data)

                //refresh and hide group modal
                this.forceRerender();
                this.displayGroups();
                
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

