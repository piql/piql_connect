<template>
  <div>
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Description</th>
                          
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" @click="viewActions(group.id)" title="Actions" style="color:white">
                                    <i class="fa fa-eye"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Assign Action" style="color:white" @click="showAssignActionModal(group.id)">
                                    <i class="fa fa-tag"></i>
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

                <b-modal id="add-action" hide-footer>
                    <template v-slot:modal-title>
                    Add Action
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>Action</label>
                        <input type="text" class="form-control" v-model="action" >
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="description" class="form-control"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addActionClicked" @keydown="addActionClicked"><i class="fa fa-tag"></i> Add Action</b-button>
                </b-modal>
                
              
  
  
  
  </div>

</template>

<script>
export default {
    data() {
            return {
                response:null,
                action:null,
                description: null,
                groups:null,
                pageMeta: null,
                groupId: null
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
            axios.get("/api/v1/admin/permissions/groups" + apiQueryString).then( (response ) => {
               this.response = response
                this.groups = this.response.data.data;
                this.pageMeta = this.response.data.meta
            });
        },

        showAssignActionModal(groupId){
          this.groupId = groupId;
          this.$bvModal.show('add-action');
        },

        addActionClicked(){
          let action = {
            name: this.action,
            description: this.description,
            groupId: this.groupId
          }
          this.$emit('addAction', action);
        },
        viewActions(groupId){
          this.$router.push({ name:'settings.actions', params:{groupId} });
        }
        
    }


}
</script>

<style>

</style>