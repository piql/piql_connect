<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-user-plus"></i>  Add User
                </button>
            </div>
            <div class="card-body">
               <users />
               <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Add User</h4>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                        <label>Fullname</label>
                                        <input type="text" class="form-control" v-model="fullnamei" >
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control" v-model="usernamei" >
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" v-model="emaili" >
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i>Add User</button>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>

<script>
import users from './components/users';

    export default {
        components:{
            users

        },
        data() {
            return {
                fullnamei:null,
                emaili:null,
                usernamei:null
            };
        },

        methods: {
        }
    }
</script>

