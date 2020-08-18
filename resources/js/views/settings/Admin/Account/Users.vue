<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary btncheck" @click="$bvModal.show('add-user')">
                    <i class="fa fa-user-plus"></i>  {{$t('settings.settings.addUser')}}
                </button>
                <b-modal id="add-user" hide-footer>
                    <template v-slot:modal-title>
                   <h4>{{$t('settings.settings.addUser')}}</h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>{{$t('settings.listing.fullname')}}</label>
                            <input type="text" class="form-control" v-model="fullname" required>
                        </div>
                        <div class="form-group">
                            <label>{{$t('settings.listing.username')}}</label>
                            <input type="text" class="form-control" v-model="username" required>
                        </div>
                        <div class="form-group">
                            <label>{{$t('settings.listing.email')}}</label>
                            <input type="email" class="form-control" v-model="email" required>
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addUser" @keydown="addUser"><i class="fa fa-user-plus"></i> {{$t('settings.settings.addUser')}}</b-button>
                </b-modal>
            </div>
            <div class="card-body">
               <user-listing :key="listingKey" @deleteUser='deleteUser'  @disableUser="disableUser" :users="formattedUsers" @editUser="editUser" @enableUser="enableUser"></user-listing>
               <div class="row text-center pagerRow">
                    <div class="col">
                        <Pager :meta='usersPageMeta' :height='height' />
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>

<script>
import Pager from "@components/Pager"
import { mapGetters, mapActions } from "vuex";

    export default {
        components:{
            Pager
        },
        data() {
            return {
                fullname:null,
                email:null,
                username:null,
                listingKey: 0,
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
            userApiResponse(newValue,prevValue){
                //will run on success or failure of any post operation
                if(newValue && (newValue.status >= 200 && newValue.status <= 299)){
                    this.successToast('Success: ' + newValue.status ,newValue.message);
                }else if(newValue && newValue.status){
                    this.errorToast('Error: ' + newValue.status,newValue.message);
                }
            }
        },

        async mounted() {
            let page = this.$route.query.page;
            if( isNaN( page ) || parseInt( page ) < 2 ) {
                this.$route.query.page = 1;
            }
            this.fetchUsers(this.apiQueryString)

        },

        computed:  {
            ...mapGetters(['formattedUsers','usersPageMeta','userApiResponse']),
            apiQueryString: function() {
                let query = this.$route.query;
                let filter = '';

                if( parseInt( query.page ) ) {
                    filter += "?page=" + query.page;
                }
                return filter;
            }
            

        },

        methods: {
            ...mapActions(['fetchUsers','postNewUser','disableUserRequest','enableUserRequest']), 
            dispatchRouting() {
                this.fetchUsers(this.apiQueryString);
            },
            forceRerender(){
                this.listingKey += 1;
            },
        
            addUser(){
                this.infoToast("Adding User", "creating new user in the system");

                //invoke vuex axction postNewUser defined above
                this.postNewUser({
                    'name': this.fullname,
                    'username': this.username,
                    'email': this.email
                });

                this.$bvModal.hide('add-user');
                this.forceRerender();    

            },
            editUser(){
                this.infoToast("Editing User", "editing user in the system");
                //logic to send data to endpoint goes here

                this.forceRerender();
                this.$bvModal.hide('edit-user');
                

            },
           disableUser(data){
                this.infoToast("Disable User", "disabling a user from listing");

                //vuex action call
                this.disableUserRequest(data);

                if(!this.forceRerender()){
                    location.reload();
                }

                this.$bvModal.hide('disable-user');
               
            },
            
            deleteUser(data){
                this.infoToast("Delete User", "deleting a user from listing");

                //delete request goes here
                this.forceRerender();
                
                this.$bvModal.hide('delete-user');
            },
            enableUser(data){
                this.infoToast("Enable User", "enabling a user in listing");

                //vuex request
                this.enableUserRequest(data);

                if(!this.forceRerender()){
                    location.reload();
                }

                this.$bvModal.hide('enable-user');

            },
        }
    }
</script>

