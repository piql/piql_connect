<template>
  <div class="table-responsive">

       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>{{$t('settings.groups.group')}}</th>
                            <th width="20%">{{$t('settings.settings.actions')}} </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in userGroups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>
                                
                                <a class="btn btn-xs btn-primary" @click="showAssignUsersModal(group.id)" data-toggle="tooltip" :title="$t('settings.groups.assignUsers')" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showListingModal(group.id)" data-toggle="tooltip" :title="$t('settings.groups.listRolesAndUsers')" style="color:white">
                                    <i class="fa fa-list"></i>
                                    </a>
                              
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(group.id)" data-toggle="tooltip" :title="$t('settings.groups.assignRoles')" style="color:white">
                                    <i class="fa fa-user-shield"></i>
                                    </a>
                                
                            </td>

                        </tr>


                    </tbody>
                </table>

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
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
    },
    data() {
        return {
            group: null,
            selectedRoles: [],
            selectedUsers: [],
            ulist:[],
            list:[]
        };
    },

    props: {
        height: {
            type: Number,
            default: 0
        },
        formattedUsers: Array
    },

    computed:{
        ...mapGetters('groups',
            ['userGroups','userGroupUsers','userGroupRoles','userRoles']
        ),
        ...mapGetters('roles',
            ['userRoles']
        ),
    },
    watch: {
        formattedUsers(val){
            if(val){
                this.ulist = val.map( u => {
                    let fullName = '';
                    if (u.firstName && u.lastName) {
                        fullName = u.firstName + ' ' + u.lastName;
                    } else if (u.firstName) {
                        fullName = u.firstName;
                    } else if (u.lastName) {
                        fullName = u.lastName;
                    } else {
                        fullName = u.username;
                    }
                    return { value: u.id, label: fullName }
                } );
            }
        },
        userGroupUsers(val){
            if (val){
                this.selectedUsers = val.map( u => {
                    return u.id;
                } );
            }
        },
        userRoles(val){
            if(val){
                this.list = val.map( u => { return { value: u.id, label: u.name } } );
            }
        }
    },
    async mounted() {
        this.fetchSelectUsers()
        this.fetchSelectedRoles();
    },
    methods:{
        ...mapActions('groups',
            ['fetchGroupUsers','fetchGroupRoles','fetchSelectedRoles']
        ),
        ...mapActions('users',
            ['fetchSelectUsers']
        ),
        ...mapActions('roles',
            ['fetchSelectedRoles']
        ),
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
                group_id: groupId
            }

            this.$emit('assignGroupToUsers', data);

        },
        showAssignModal(groupId){
            this.group = this.userGroups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-group')

        },
        showAssignUsersModal(groupId){
            this.group = this.userGroups.filter(group => group.id === groupId);
            this.fetchGroupUsers(groupId).then(() => {
                this.$bvModal.show('assign-users');
            }).catch(e => {
                console.log(e);
            });
        },
    }

}
</script>



