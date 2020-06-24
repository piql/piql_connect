<template>
  <div>
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="staff in users" :key="staff.uid">
                            <td>{{staff.full_name}}</td>
                            <td>{{staff.username}}</td>
                            <td>{{staff.email}}</td>
                            <td>{{staff.created_at}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Assign Group" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit" style="color:white" data-toggle="modal" data-target="#editModal">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Disable" style="color:white" @click="showDisableModal(staff.uid)">
                                    <i class="fa fa-ban"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>

                <b-modal id="disable-user" v-if="user" hide-footer>
                    <template v-slot:modal-title>
                    <h3><b> DISABLE USER : {{ user[0].full_name }} </b></h3>
                    </template>
                    <div class="d-block">
                        <div class="alert alert-warning">
                            Are you sure you want to disable this user? If so, proceed to disable
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="disableButtonClicked(user[0].id)" @keydown="disableButtonClicked(user[0].id)"><i class="fa fa-ban"></i> Disable </b-button>
                </b-modal>
                
                
                
                
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <form role="form" method="post">
                            <fieldset>
                                <div class="modal-body">
                                    <legend>Update User </legend>
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
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i>Update User</button>
                                </div>
                            </fieldset>

                        </form>
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
                user:null,
            };
    },
    props:{
        users: Object,
        pageMeta: Object
    },
    methods:{
        showDisableModal(uid){
            this.user = this.users.filter(user => user.uid === uid);
            this.$bvModal.show('disable-user');
        },
        disableButtonClicked(id){
            let data = {
                users: [id]
            };
            this.$emit('disableUser', data);
        }
        
    }


}
</script>

<style>

</style>