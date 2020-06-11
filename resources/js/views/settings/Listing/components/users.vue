<template>
  <div>
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Steven Sewalu</td>
                            <td>smstoroc</td>
                            <td>steven.sewalu@contango.no</td>
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
                        <tr>
                            <td>Steven Sewalu</td>
                            <td>smstoroc</td>
                            <td>steven.sewalu@contango.no</td>
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
                        <tr>
                            <td>Steven Sewalu</td>
                            <td>smstoroc</td>
                            <td>steven.sewalu@contango.no</td>
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
                username:null
            };
        },


}
</script>

<style>

</style>