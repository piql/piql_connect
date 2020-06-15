<template>
  <div>
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Descriptionn</th>
                          
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Assign Action" style="color:white">
                                    <i class="fa fa-cog"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit" style="color:white" data-toggle="modal" data-target="#editModal">
                                    <i class="fa fa-edit"></i>
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
                
              
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <form role="form" method="post">
                            <fieldset>
                                <div class="modal-body">
                                    <legend>Update Group </legend>
                                    <div class="form-group">
                                        <label>Group</label>
                                        <input type="text" class="form-control" v-model="group" >
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea v-model="description" class="form-control"></textarea>
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
                data:null,
                group:null,
                description:null,
                groups:null
            };
    },
    async mounted() {
        this.data = (await axios.get("/api/v1/admin/permissions/groups")).data;
        this.groups = this.data.data;
        
        
    },
    methods:{
        
    }


}
</script>

<style>

</style>