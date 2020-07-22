<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary btncheck" @click="$bvModal.show('add-user')">
                    <i class="fa fa-user-plus"></i>  Add User
                </button>
                <b-modal id="add-user" hide-footer>
                    <template v-slot:modal-title>
                   <h4>Add User</h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Fullname</label>
                            <input type="text" class="form-control" v-model="fullname" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" v-model="username" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" v-model="email" required>
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addUser" @keydown="addUser"><i class="fa fa-user-plus"></i> Add User</b-button>
                </b-modal>
            </div>
            <div class="card-body">
               <user-listing :key="listingKey" @deleteUser='deleteUser'  @disableUser="disableUser" :users="users" @editUser="editUser" @enableUser="enableUser"></user-listing>
               <div class="row text-center pagerRow">
                    <div class="col">
                        <Pager :meta='pageMeta' :height='height' />
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>

<script>

    export default {
        data() {
            return {
                fullname:null,
                email:null,
                username:null,
                users: null,
                pageMeta:null,
                listingKey: 0,
                response: null,
            };
        },
        props: {
            height: {
                type: Number,
                default: 0
            }
        },

     watch: {
        '$route': 'dispatchRouting'
    },

        async mounted() {
            let page = this.$route.query.page;
            if( isNaN( page ) || parseInt( page ) < 2 ) {
                this.$route.query.page = 1;
            }
            this.refreshObjects( this.apiQueryString, this.apiEndPoint );


        },

        computed:  {
            apiQueryString: function() {
            let query = this.$route.query;
            let filter = '';

            if( parseInt( query.page ) ) {
                filter += "?page=" + query.page;
            }
            return filter;
        },

        apiEndPoint: function () {
            let query = this.$route.query;

            if( parseInt( query.groupId ) ) {
                return '/api/v1/admin/permissions/'+ query.groupId + '/users';
            }else if( parseInt( query.roleId ) ) {
                return '/api/v1/admin/permissions/'+ query.roleId + '/users';
            }  else{
                return '/api/v1/admin/users';
            }
            
        }

        },

        methods: {
            dispatchRouting() {
                this.refreshObjects( this.apiQueryString, this.apiEndPoint );
            },

            async refreshObjects( apiQueryString, apiEndPoint){
                let data = {
                    limit: 10
                }; 
                await axios.get(apiEndPoint + apiQueryString, {
                   params: data
                }).then( (response ) => {
                this.response = response
                    this.users = this.response.data.data;
                    
                    this.pageMeta = this.response.data.meta
                });
            },
            forceRerender(){
                this.listingKey += 1;

            },
            
            async addUser(){
                this.infoToast("Adding User", "creating new user in the system");

                await axios.post("/api/v1/registration/register",{
                    'name': this.fullname,
                    'username': this.username,
                    'email': this.email
                }).then(response => {
                    console.log(response);
                    this.response = response;

                }).catch(error => {
                    console.log(error);
                    this.errorToast(error.message, error.message);
                });

                this.forceRerender();
                this.$bvModal.hide('add-user');

            },
            async editUser(){
                this.infoToast("Editing User", "editing user in the system");
                //logic to send data to endpoint goes here

                this.forceRerender();
                this.$bvModal.hide('edit-user');

            },
           async disableUser(data){
                this.infoToast("Disable User", "disabling a user from listing");
                this.response = (await axios.post("/api/v1/admin/users/disable",data,{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                if(!this.forceRerender()){
                    location.reload();
                }
                //this.forceRerender();

                this.$bvModal.hide('disable-user');
            },
            deleteUser(data){
                this.infoToast("Delete User", "deleting a user from listing");
                // this.response = (await axios.post("/api/v1/admin/users/disable",data,{
                //     headers:{
                //         'content-type': 'application/json'
                //     }
                // })).data;

                if(!this.forceRerender()){
                    location.reload();
                }
                //this.forceRerender();

                this.$bvModal.hide('delete-user');
            },
            async enableUser(data){
                this.infoToast("Enable User", "enabling a user in listing");
                this.response = (await axios.post("/api/v1/admin/users/enable",data,{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                if(!this.forceRerender()){
                    location.reload();
                }
                //this.forceRerender()
                this.$bvModal.hide('enable-user');

            },
        }
    }
</script>

