<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id">
                            <td>{{role.name}}</td>
                            <td>{{role.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Edit Role" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Delete Role" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Assign Users" style="color:white">
                                    <i class="fa fa-users"></i>
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

  
  </div>
</template>

<script>
export default {
    data() {
        return {
            response:null,
            roles: null,
            pageMeta: null,
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
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString );
        },

        refreshObjects( apiQueryString ){
            axios.get("/api/v1/admin/permissions/roles" + apiQueryString).then( (response ) => {
               this.response = response
                this.roles = this.response.data.data;
                
                this.pageMeta = this.response.data.meta
            });
        }
        
    }

}
</script>

<style>

</style>