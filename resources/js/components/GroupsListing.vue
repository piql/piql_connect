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
                        <tr v-for="group in groups" :key="group.id">
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
                        <Pager :meta='pageMeta' :height='height' />
                    </div>
                </div>

                <b-modal id="group-users" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.groups.group')}} " {{ group[0].name.toUpperCase() }} " </b></h4>
                    </template>
                    <div class="row">
                        <div class="col-md-6">
                            <b>{{$t('settings.settings.users')}}</b>
                            <p v-if="users.length <= 0">{{$t('settings.groups.noUsersAssignedMessage')}}</p>
                            <table class="table table-condensed table-bordered table-sm table-striped" role="table">
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{user.full_name}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <b>{{$t('settings.settings.roles')}}</b>
                            <p v-if="roles.length <= 0">{{$t('settings.groups.noRolesAssignedMessage')}}.</p>
                             <table class="table table-condensed table-bordered table-sm table-striped" role="table">
                                 <tr v-for="role in roles" :key="role.id">
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
import axios from "axios"
export default {
    components: {
        Pager
    },
    data() {
        return {
            response:null,
            groups: null,
            pageMeta: null,
            group: null,
            list: [],
            ulist: [],
            selectedRoles: [],
            selectedUsers: [],
            users: [],
            roles:[],
        };
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },

    computed:{
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
        '$route': 'dispatchRouting'
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.refreshObjects( this.apiQueryString);

        this.fetchRoles(100);
        this.fetchUsers(100);


    },
    methods:{
        async fetchRoles(limit){
            await axios.get("/api/v1/admin/access-control/permission-groups",{ params: { limit: limit } }).then(response => {
                let roles = response.data.data;
                roles.forEach(single => {
                    this.list.push({
                        label: single.name,
                        value: single.id
                        })
                });
            })

        },
        async fetchUsers(limit){
            await axios.get("/api/v1/admin/users",{ params: { limit: limit } }).then(response => {
                let users = response.data.data
                users.forEach(single => {
                    this.ulist.push({
                        label: single.full_name,
                        value: single.id
                        })
                });
            })

        },
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
            this.group = this.groups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-group')

        },
        showAssignUsersModal(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-users');

        },
        
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString);
        },

        async refreshObjects( apiQueryString){
            await axios.get('/api/v1/admin/access-control/roles' + apiQueryString).then( (response ) => {
               this.response = response
                this.groups = this.response.data.data;
                
                this.pageMeta = this.response.data.meta
            });
        },
        async fetchGroupUsers(groupId){
            
            await axios.get('/api/v1/admin/access-control/roles/' + groupId +'/users',{ params: { limit: 100 } }).then( response => {
              
                this.users = response.data.data;
            }).catch(error => {
                console.log(error);
            });

            
        },

        async fetchGroupRoles(groupId){
            await axios.get('/api/v1/admin/access-control/roles/'+ groupId +'/permissions',{ params: { limit: 100 } }).then( response => {
                //this.response = response;
                this.roles = response.data.data;
            }).catch(error => {
                console.log(error)
            });

        },
        showListingModal(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            //fetch users and roles
            this.fetchGroupUsers(groupId);
            this.fetchGroupRoles(groupId);

            this.$bvModal.show('group-users');

        }
        
    }

}
</script>



