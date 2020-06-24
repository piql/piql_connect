<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" @click="$bvModal.show('add-user')">
                    <i class="fa fa-user-plus"></i>  Add User
                </button>
                <b-modal id="add-user" hide-footer>
                    <template v-slot:modal-title>
                   <h4>Add User</h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Fullname</label>
                            <input type="text" class="form-control" v-model="fullname" >
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" v-model="username" >
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" v-model="email" >
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="addUser" @keydown="addUser"><i class="fa fa-user-plllus"></i> Add User</b-button>
                </b-modal>
            </div>
            <div class="card-body">
               <user-listing :key="listingKey" @disableUser="disableUser" :users="users" :pageMeta="pageMeta"></user-listing>
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
                response: null
            };
        },

        async mounted() {
            this.response = (await axios.get("/api/v1/admin/users")).data;
            let staffs = this.response.data;
            let i = 1;

            staffs.forEach(function(single) {
                    
                    //single.created = getFormatDate(single.created_at);
                    single.uid = i;
                    i++;
                });
            
            this.users = staffs;
            this.pageMeta = this.response.meta;
        },

        methods: {
            forceRerender(){
                this.listingKey += 1;

            },
            async disableUser(data){
                 this.infoToast("Disable User", "disabling a user from listing");
                this.response = (await axios.post("/api/v1/admin/users/disable",data,{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;

                this.forceRerender();
                this.$bvModal.hide('disable-user');
            }
        }
    }
</script>

