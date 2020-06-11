<template>
    <div class="w-100">
        <page-heading icon="fa-users" :title="$t('settings.settings.users')" :ingress="$t('settings.settings.listing')" />
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
                        
                        <form role="form" method="post">
                            <fieldset>
                                <div class="modal-body">
                                    <legend>Add User </legend>
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
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i>Add User</button>
                                </div>
                            </fieldset>

                        </form>
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
                fullname:null,
                email:null,
                username:null
            };
        },

        methods: {
        }
    }
</script>

