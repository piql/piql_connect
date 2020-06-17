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
                                <a class="btn btn-xs btn-primary" title="Disable" style="color:white" data-toggle="modal" data-target="#disableModal">
                                    <i class="fa fa-ban"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination pagination-sm justify-content-end">
                        <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
                
                
                
                <div class="modal fade" id="disableModal" tabindex="-1" role="dialog" aria-labelledby="eModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <form role="form" method="post">
                            <div class="modal-body">
                                    <div class="alert alert-warning">
                                        Are you sure you want to disable this user? If so, proceed to disable
                                    </div>
                                
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Disable</button>
                                </div>

                        </form>
                        </div>
                    </div>
                </div>
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
                users:null,
                data:null
            };
    },
    async mounted() {
        this.data = (await axios.get("/api/v1/admin/users")).data;
        let staffs = this.data.data;
        let i = 1;

        staffs.forEach(function(single) {
                
                //single.created = getFormatDate(single.created_at);
                single.uid = i;
                i++;
            });
        
        this.users = staffs;
        
    },
    methods:{
        getFormatDate(date){
            let monthNames = [
                "January", "February", "March",
                "April", "May", "June", "July",
                "August", "September", "October",
                "November", "December"
            ];

            let day = date.getDate();
            let monthIndex = date.getMonth();
            let year = date.getFullYear();
            let time = date.getHours() + ':'+ date.getMinutes();

            return day + ' ' + monthNames[monthIndex] + ' ' + year + ' ' + time;
        }
    }


}
</script>

<style>

</style>