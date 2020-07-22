<template>
  <div>
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="staff in users" :key="staff.id">
                            <td>{{staff.full_name}}</td>
                            <td>{{staff.username}}</td>
                            <td>{{staff.email}}</td>
                            <td v-if="staff.disabled === true"> <b-badge variant="danger"> Disabled</b-badge></td>
                            <td v-else > <b-badge variant="success"> Active</b-badge></td>
                            <td>{{formatDate(staff.created_at)}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Assign Role" style="color:white">
                                    <i class="fa fa-user-secret"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit" style="color:white" @keydown="showEditModal(staff.id)" @click="showEditModal(staff.id)">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a v-if="staff.disabled === true" class="btn btn-xs btn-primary" title="Enable" style="color:white" @keydown="showEnableModal(staff.id)" @click="showEnableModal(staff.id)">
                                    <i class="fa fa-plug"></i>
                                    </a>
                                <a v-else class="btn btn-xs btn-primary" title="Disable" style="color:white" @keydown="showDisableModal(staff.id)" @click="showDisableModal(staff.id)">
                                    <i class="fa fa-ban"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Delete User" style="color:white" @keydown="showDeleteModal(staff.id)" @click="showDeleteModal(staff.id)">
                                    <i class="fa fa-trash"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>

                <b-modal id="delete-user" hide-footer>
                    <template v-slot:modal-title>
                    <h3><b> DELETE USER : {{ user[0].full_name }} </b></h3>
                    </template>
                    <div class="d-block">
                        <div class="alert alert-warning">
                            Are you sure you want to delete this user? If so, proceed to delete
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="deleteButtonClicked(user[0].id)" @keydown="deleteButtonClicked(user[0].id)"><i class="fa fa-trash"></i> Disable </b-button>
                </b-modal>
                

                <b-modal id="disable-user" hide-footer>
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

                <b-modal id="enable-user" hide-footer>
                    <template v-slot:modal-title>
                    <h3><b> ENABLE USER : {{ user[0].full_name }} </b></h3>
                    </template>
                    <div class="d-block">
                        <div class="alert alert-info">
                            This action will enable this user, do you want to proceed? If so, click to proceed
                        </div>
                    </div>
                    <b-button class="mt-3" block @click="enableButtonClicked(user[0].id)" @keydown="enableButtonClicked(user[0].id)"><i class="fa fa-plug"></i> Enable </b-button>
                </b-modal>
                
                
                
                
                <b-modal id="edit-user" hide-footer>
                    <template v-slot:modal-title>
                   <h4>Edit User</h4>
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
                    <b-button class="mt-3" block @click="editButtonClicked" @keydown="editButtonClicked"><i class="fa fa-edit"></i> Edit User</b-button>
                </b-modal>
  
  
  
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
        users: Array
    },
    
    methods:{
        showDisableModal(id){
            this.user = this.users.filter(user => user.id === id);
            this.$bvModal.show('disable-user');
        },
        showDeleteModal(id){
            this.user = this.users.filter(user => user.id === id);
            this.$bvModal.show('delete-user');
        },
        showEnableModal(id){
            this.user = this.users.filter(user => user.id === id);
            this.$bvModal.show('enable-user');
        },
        showEditModal(id){
            this.user = this.users.filter(user => user.id === id);
            this.fullname = this.user[0].full_name;
            this.email = this.user[0].email;
            this.username = this.user[0].username;

            this.$bvModal.show('edit-user');

        },
        disableButtonClicked(id){
            let data = {
                users: [id]
            };
            this.$emit('disableUser', data);
        },
        enableButtonClicked(id){
            let data = {
                users: [id]
            };
            this.$emit('enableUser', data);
        },
        deleteButtonClicked(id){
            let data = {
                users: [id]
            };
            this.$emit('deleteUser', data);
        },
        editButtonClicked(){
            //some data will be passed here before emitting
            this.$emit('editUser');
        },
        formatDate(ISOdate){
                let date = new Date(ISOdate);
                let year = date.getFullYear();
                let month = date.getMonth()+1;
                let dt = date.getDate();
                let time = date.getHours() + ':'+ date.getMinutes();

                if (dt < 10) {
                dt = '0' + dt;
                }
                if (month < 10) {
                month = '0' + month;
                }

                return year+'-' + month + '-'+dt + ' '+time;
                
            },

    
        
    }


}
</script>

<style>

</style>