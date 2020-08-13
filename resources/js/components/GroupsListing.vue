<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>{{$t('settings.groups.group')}}</th>
                            <th>{{$t('settings.groups.description')}}</th>
                            <th width="20%">{{$t('settings.settings.actions')}} </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in userGroups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" :title="$t('settings.groups.editGroup')" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showAssignUsersModal(group.id)" :title="$t('settings.groups.assignUsers')" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showListingModal(group.id)" :title="$t('settings.groups.listRolesAndUsers')" style="color:white">
                                    <i class="fa fa-list"></i>
                                    </a>
                                
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(group.id)" :title="$t('settings.groups.assignRoles')" style="color:white">
                                    <i class="fa fa-user-shield"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" :title="$t('settings.groups.deleteGroup')" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                            </td>
                        
                        </tr>
                    
                       
                    </tbody>
                </table>
                <div class="row text-center pagerRow">
                    <div class="col">
                        <Pager :meta='groupPageMeta' :height='height' />
                    </div>
                </div>

                <b-modal id="group-users" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.groups.group')}} " {{ group[0].name.toUpperCase() }} " </b></h4>
                    </template>
                    <div class="row">
                        <div class="col-md-6">
                            <b>{{$t('settings.settings.users')}}</b>
                            <p v-if="userGroupUsers.length <= 0">{{$t('settings.groups.noUsersAssignedMessage')}}</p>
                            <table class="table table-condensed table-bordered table-sm table-striped" role="table">
                                <tr v-for="user in userGroupUsers" :key="user.id">
                                    <td>{{user.full_name}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <b>{{$t('settings.settings.roles')}}</b>
                            <p v-if="userGroupRoles.length <= 0">{{$t('settings.groups.noRolesAssignedMessage')}}.</p>
                             <table class="table table-condensed table-bordered table-sm table-striped" role="table">
                                 <tr v-for="role in userGroupRoles" :key="role.id">
                                    <td>{{role.name}}</td>
                                </tr>
                             </table>
                            
                        </div>
                    
                    </div>
                </b-modal>

                <b-modal id="assign-users" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.groups.assignUsersToGroup').toUpperCase()}} [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div>
                        <vue-select-sides
                        type="mirror"
                        v-model="selectedUsers"
                        :list="ulist"
                        ></vue-select-sides>
                    
                    </div>
                    <b-button class="mt-3" @click="assignUserButtonClicked(group[0].id)" block>
                        <i class="fa fa-users"></i> {{$t('settings.groups.assignUsers').toUpperCase()}}
                    </b-button>
                </b-modal>

                <b-modal id="assign-group" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.groups.assignRolesToGroup').toUpperCase()}} [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div>
                        <vue-select-sides
                        type="mirror"
                        v-model="selectedRoles"
                        :list="list"
                        ></vue-select-sides>
                    
                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked(group[0].id)" block><i class="fa fa-user-shield"></i> 
                    {{$t('settings.groups.assignRoles').toUpperCase()}}</b-button>
                </b-modal>


  
  </div>
</template>

<script>
import Pager from "./Pager"
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        Pager
    },
    data() {
        return {
            group: null,
            list: [],
            ulist: [],
            selectedRoles: [],
            selectedUsers: [],
        };
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },

    computed:{
        ...mapGetters(['groupsApiResponse','userGroups','groupPageMeta','userGroupUsers','userGroupRoles','formattedUsers','userRoles']),
      apiQueryString: function() {
            let query = this.$route.query;
            let filter = '';

            if( parseInt( query.page ) ) {
                filter += "?page=" + query.page;
            }
            return filter;
        },


    },
     watch: {
        '$route': 'dispatchRouting',
        //watching vuex state change and tying them to actions
        formattedUsers(newValue,oldValue){
            if(newValue){
                newValue.forEach(single => {
                    this.ulist.push({
                        label: single.full_name,
                        value: single.id
                        })
                });
            }
        },
        userRoles(newValue,oldValue){
            if(newValue){
                newValue.forEach(single => {
                    this.list.push({
                        label: single.name,
                        value: single.id
                        })
                });
            }
        }
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.fetchGroups( this.apiQueryString);  
        this.fetchSelectUsers()
        this.fetchSelectedRoles();


    },
    methods:{
        ...mapActions(['fetchGroups','fetchGroupUsers','fetchGroupRoles','fetchSelectUsers','fetchSelectedRoles']),
  
       dispatchRouting() {
            this.fetchGroups( this.apiQueryString);
        },
        showListingModal(groupId){
            this.group = this.userGroups.filter(group => group.id === groupId);
            //fetch users and roles
            this.fetchGroupUsers(groupId);
            this.fetchGroupRoles(groupId);

            this.$bvModal.show('group-users');

        },
        //emitted actions
        assignButtonClicked(groupId){
            let data = {
                roles: this.selectedRoles,
                groupId: groupId
            }

            this.$emit('assignGroupToRoles', data);


        },
        assignUserButtonClicked(groupId){
            let data = {
                users: this.selectedUsers,
                access_controls: [groupId]
            }

            this.$emit('assignGroupToUsers', data);

        },
        showAssignModal(groupId){
            this.group = this.userGroups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-group')

        },
        showAssignUsersModal(groupId){
            this.group = this.userGroups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-users');

        },
        
    }

}
</script>



