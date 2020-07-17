<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Description</th>
                            <th>Role</th>
                            <th width="18%">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="perm in permissions" :key="perm.id">
                            <td>{{perm.name}}</td>
                            <td>{{perm.description}}</td>
                            <td v-if="perm.group_id === null"> <b-badge variant="warning"> {{perm.roleName}}</b-badge></td>
                            <td v-else > <b-badge variant="info">  Has Role -ref #{{ perm.group_id }}</b-badge></td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Configure Permissions" style="color:white">
                                    <i class="fa fa-user-cog"></i>
                                    </a>
                                
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(perm.id)" title="Assign Access Group" style="color:white">
                                    <i class="fa fa-user-shield"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showEditModal(perm.id)" title="Edit Permission" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showDeleteModal(perm.id)" title="Delete Role" style="color:white">
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

                <b-modal id="delete-perm" hide-footer>
                    <template v-slot:modal-title>
                        <h4> <b>DELETE PERMISSION [ {{ permission[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <b-alert show variant="warning">Do you want to delete this permission? if so, click below to proceed</b-alert>
                    </div>
                    <b-button class="mt-3" block @click="deletePermClicked(permission[0].id)" @keydown="deletePermClicked(perm[0].id)"><i class="fa fa-trash"></i> DELETE PERMISSION</b-button>
                </b-modal>

                <b-modal id="edit-perm" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>EDIT PERMISSION [ {{ permission[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Permission</label>
                            <input type="text" class="form-control" v-model="permName" >
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" @click="editButtonClicked(permission[0].id)" block><i class="fa fa-edit"></i> EdIT PERMISSION</b-button>
                </b-modal>


                <b-modal id="assign-perm" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ASSIGN ROLE TO PERMISSION [ {{ permission[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="form-group">
                        <label for="roles">Select Role</label>
                        <select v-model="selrole" id="roles" class="form-control">
                            <option v-for="role in list" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>

                        </select>

                        

                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked" block><i class="fa fa-user-shield"></i> ASSIGN ROLE</b-button>
                </b-modal>


  
  </div>
</template>

<script>
export default {
    data() {
        return {
            response:null,
            permissions: null,
            pageMeta: null,
            permission: null,
            list: [],
            selrole:null,
            permName: null,
            description: null
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
     apiQueryEndPoint: function(){
         let roleId = this.$route.params.roleId;

         if( parseInt( roleId ) ) {
                return '/api/v1/admin/access-control/permission-groups/'+ roleId +'/permissions';
            } else {
                return '/api/v1/admin/access-control/permissions';
            }

     }

    },
     watch: {
        '$route': 'dispatchRouting'
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.refreshObjects( this.apiQueryString, this.apiQueryEndPoint);

        

        /**list users * i can only pull in 10 at a time, need help getting all at 
         * the same time unless allowed to tamper with the backend **/

       let roles = (await axios.get("/api/v1/admin/access-control/permission-groups",{ params: { limit: 100 } })).data.data;
        roles.forEach(single => {
            this.list.push({
                name: single.name,
                id: single.id
                })
        });

    },
    methods:{
        showEditModal(permId){
            this.permission = this.permissions.filter(permission => permission.id === permId);
            this.permName = this.permission[0].name;
            this.description = this.permission[0].description;
            this.$bvModal.show('edit-perm')

        },
        editButtonClicked(permId){
            let data = {
                name: this.permName,
                description: this.description,
                permId: permId
            }

            this.$emit('editPermission', data);

        },
        showDeleteModal(permId){
            this.permission = this.permissions.filter(permission => permission.id === permId);
           
            this.$bvModal.show('delete-perm')

        },
        deletePermClicked(permId){
            this.$emit('deletePermission', permId);
        },
        assignButtonClicked(){
            let data = {
                roleId: this.selrole,
                permissionId: this.permission[0].id
            }

            this.$emit('assignRoleToPermission', data);


        },
        showAssignModal(permId){
            this.permission = this.permissions.filter(permission => permission.id === permId);
            this.$bvModal.show('assign-perm')

        },
        
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString, this.apiQueryEndPoint);
        },

        async refreshObjects( apiQueryString, apiQueryEndPoint){
            await axios.get(apiQueryEndPoint + apiQueryString).then( (response ) => {
               this.response = response
                this.permissions = this.response.data.data;

                //get group name
                this.permissions.forEach(perm => {
                    if(perm.group_id !== null){
                        axios.get('/api/v1/admin/access-control/permission-groups/'+ perm.group_id).then(res => {
                            //console.log(res.data.data.name);
                            perm.roleName = res.data.data.name;
                        });

                    }else{
                        perm.roleName = "N/A";
                    }
                   

                });
                
                this.pageMeta = this.response.data.meta
            });
        },
        fetchGroupName(roleId){

            //return "steven";
            axios.get('/api/v1/admin/access-control/permission-groups/'+ roleId).then(res => {
                //console.log(res.data.data.name);
                return res.data.data.name;
            }).catch(err => {
                return err;
            });

        }
        
    }

}
</script>



