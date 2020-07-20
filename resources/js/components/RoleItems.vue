<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                          
                            <th width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id">
                            <td>{{role.name}}</td>
                            <td>{{role.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" @click="viewPermissions(role.id)" title="Configure Permissions" style="color:white">
                                    <i class="fa fa-user-cog"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Assign User Group" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit Role" style="color:white" @click="showEditModal(role.id)">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary"  title="Delete Role" @click="showDeleteModal(role.id)" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>
                <div class="row text-center pagerRow">
                    <div class="col">

                      
                        <Pager :meta='pageMeta' :height='height' />
                    </div>
                </div>

                <b-modal id="delete-role" hide-footer>
                    <template v-slot:modal-title>
                        <h4> <b>DELETE ROLE [ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <b-alert show variant="warning">Do you want to delete this role? if so, click below to proceed</b-alert>
                    </div>
                    <b-button class="mt-3" block @click="deleteRoleClicked(role[0].id)" @keydown="deleteRoleClicked(role[0].id)"><i class="fa fa-trash"></i> DELETE ROLE</b-button>
                </b-modal>

                <b-modal id="edit-role" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>EDIT ROLE [ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" v-model="roleName" >
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" @click="editButtonClicked(role[0].id)" block><i class="fa fa-edit"></i> EDIT ROLE</b-button>
                </b-modal>
                
              
  
  
  
  </div>

</template>

<script>
export default {
    data() {
            return {
                response:null,
                description: null,
                roles:null,
                pageMeta: null,
                roleId: null,
                role: null,
                roleName: null,
                description: null,
               
            };
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },
    computed:{
      apiQueryString: function() {
            let query = this.$route.query;
            let filter = '';
            if( parseInt( query.page ) ) {
                filter += "?page=" + query.page;
            }
            return filter;
        },

    },
     watch: {
        '$route': 'dispatchRouting'
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.refreshObjects( this.apiQueryString );

        
    },
    methods:{
        editButtonClicked(roleId){
            let data = {
                name: this.roleName,
                description: this.description,
                roleId: roleId
            }

            this.$emit('editRole', data);


        },
        showEditModal(roleId){
            this.role = this.roles.filter(role => role.id === roleId);
            this.roleName = this.role[0].name;
            this.description = this.role[0].description;
            this.$bvModal.show('edit-role')

        },
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString );
        },
       showDeleteModal(roleId){
           this.role = this.roles.filter(role => role.id === roleId);
           this.$bvModal.show('delete-role');

        },
        deleteRoleClicked(roleId){
            this.$emit('deleteRole', roleId);

        },

        refreshObjects( apiQueryString ){
            axios.get("/api/v1/admin/access-control/permission-groups" + apiQueryString).then( (response ) => {
               this.response = response
                this.roles = this.response.data.data;
                this.pageMeta = this.response.data.meta
            }).catch(error => {
                this.response = error;
            });
        },

        viewPermissions(roleId){
          this.$router.push({ name:'settings.roles.permissions', params: { roleId: roleId } });
        },
        
    }


}
</script>

<style>

</style>