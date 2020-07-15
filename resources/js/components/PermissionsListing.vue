<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Description</th>
                            <th width="18%">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="perm in permissions" :key="perm.id">
                            <td>{{perm.name}}</td>
                            <td>{{perm.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Configure Permissions" style="color:white">
                                    <i class="fa fa-user-cog"></i>
                                    </a>
                                
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(perm.id)" title="Assign Access Group" style="color:white">
                                    <i class="fa fa-user-shield"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit Permission" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Delete Role" style="color:white">
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


                <b-modal id="assign-perm" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ASSIGN ACCESS GROUP TO PERMISSION [ {{ permission[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="form-group">
                        <label for="groups">Select Access Group</label>
                        <select v-model="selgroup" id="groups" class="form-control">
                            <option v-for="group in list" :key="group.id" :value="group.id">
                                {{ group.name }}
                            </option>

                        </select>

                        

                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked" block><i class="fa fa-user-shield"></i> Assign Access Group</b-button>
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
            selgroup:null
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
         let groupId = this.$route.params.groupId;

         if( parseInt( groupId ) ) {
                return '/api/v1/admin/access-control/permission-groups/'+ groupId +'/permissions';
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

       let groups = (await axios.get("/api/v1/admin/access-control/permission-groups",{ params: { limit: 100 } })).data.data;
        groups.forEach(single => {
            this.list.push({
                name: single.name,
                id: single.id
                })
        });

    },
    methods:{
        assignButtonClicked(){
            let data = {
                groupId: this.selgroup,
                permissionId: this.permission[0].id
            }

            this.$emit('assignGroupToPermission', data);


        },
        showAssignModal(permId){
            this.permission = this.permissions.filter(permission => permission.id === permId);
            this.$bvModal.show('assign-perm')

        },
        
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString, this.apiQueryEndPoint);
        },

        refreshObjects( apiQueryString, apiQueryEndPoint){
            axios.get(apiQueryEndPoint + apiQueryString).then( (response ) => {
               this.response = response
                this.permissions = this.response.data.data;
                
                this.pageMeta = this.response.data.meta
            });
        }
        
    }

}
</script>



