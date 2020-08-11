export default {
    methods: {
        runCallBack(response,modal,timeOut){
            //to handle promises from vuex
            setTimeout(() => {
                if(response.status == 200){
                    this.successToast('Success: ' + response.status ,response.message);
                }else{
                    this.errorToast('Error: ' + response.status ,response.message);
                }

                 this.forceRerender();
                 this.$bvModal.hide(modal);
                
            }, timeOut)

        }
    }
}