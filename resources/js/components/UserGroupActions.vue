<template>
  <div>
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="action in actions" :key="action.id">
                            <td>{{action.name}}</td>
                            <td>{{action.description}}</td>
                            
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
                actions:null,
                pageMeta: null
            };
    },

    props: {
        height: {
            type: Number,
            default: 0
        },
        groupid: Number
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
            axios.get("/api/v1/admin/permissions/groups/"+ this.groupid +"/roles" + apiQueryString).then( (response ) => {
               this.response = response
                this.actions = this.response.data.data;
                this.pageMeta = this.response.data.meta
            });
        },

        
        
    }


}
</script>

<style>

</style>