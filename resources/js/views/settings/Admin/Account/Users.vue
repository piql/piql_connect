<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
        <div class="card">
            <div class="card-header">
                <span v-if="showAddUser"><i class="fa fa-user-plus"></i>  {{$t('settings.settings.addUser').toUpperCase()}} |
                <a href="#" class="btn btn-sm" @click="displayUsers">{{$t('settings.listing.backToUsers')}}</a>
                </span>

                <button v-else type="button" class="btn btn-primary btncheck" @click="displayAddUser">
                    <i class="fa fa-user-plus"></i>  {{$t('settings.settings.addUser')}}
                </button>
            </div>
            <div class="card-body">
                <add-user v-if="showAddUser" @addUser='addUser'></add-user>
                <div v-else>
                    <user-listing @disableUser="disableUser" :users="formattedUsers" @editUser="editUser" @enableUser="enableUser"></user-listing>
                    <div class="row text-center pagerRow">
                        <div class="col">
                            <Pager :meta='usersPageMeta' :height='height' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Pager from "../../../../components/Pager"
import { mapGetters, mapActions } from "vuex";

export default {
    components:{
        Pager
    },
    data() {
        return {
            showAddUser: false
        };
    },
    props: {
        height: {
            type: Number,
            default: 0
        }
    },
    watch: {
        '$route': 'dispatchRouting',
        userApiResponse(newValue){
            //will run on success or failure of any post operation
            if(newValue && (newValue.status >= 200 && newValue.status <= 299)){
                this.successToast('Success: ' + newValue.status ,newValue.message);
            } else if(newValue && newValue.status){
                this.errorToast('Error: ' + newValue.status,newValue.message);
            }
        }
    },
    async mounted() {
        let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.fetchUserSettings().then(() => {
            this.fetchUsers(this.queryParams)
        })
    },
    computed:  {
        ...mapGetters(['formattedUsers','usersPageMeta','userApiResponse', 'userTableRowCount']),
        queryParams(){
            let query = this.$route.query;
            let page = query.page || 1;
            let limit = this.userTableRowCount;
            return {
                limit: limit,
                offset: (page - 1) * limit
            }
        },
    },
    methods: {
        ...mapActions(['fetchUsers','postNewUser','disableUserRequest','enableUserRequest', 'fetchUserSettings']),
        displayAddUser(){
            this.showAddUser = true;
        },
        displayUsers(){
            this.showAddUser = false;
        },
        dispatchRouting() {
            this.fetchUsers(this.queryParams);
        },
        addUser(form){
            this.postNewUser({
                'name': form.fullname,
                'username': form.username,
                'email': form.email
            });
            this.displayUsers();
        },
        editUser(){
            //logic to send data to endpoint goes here
            this.$bvModal.hide('edit-user');
        },
        disableUser(data){
            //vuex action call
            this.disableUserRequest(data);
            this.$bvModal.hide('disable-user');
        },
        enableUser(data){
            //vuex request
            this.enableUserRequest(data);
            this.$bvModal.hide('enable-user');
        }
    }
}
</script>
