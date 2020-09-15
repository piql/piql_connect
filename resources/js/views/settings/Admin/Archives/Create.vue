<template>
    <div class="w-100">
        <page-heading icon="fa-archive" :title="$t('settings.archives.title')" :ingress="$t('settings.archives.description')" />
        <breadcumb/>
        <div class="card">
            <div class="card-header">
               <i class="fa fa-plus"></i> {{$t('settings.archives.add').toUpperCase()}}
            </div>
            <div class="card-body">
                <b-form @submit="submitForm">
            
                    <b-form-group id="input-group-2" :label="$t('settings.archives.archive')" label-for="input-1">
                        <b-form-input
                        id="input-1"
                        v-model="form.name"
                        required
                        :placeholder="$t('settings.archives.archive')"
                        
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group id="input-group-2" :label="$t('settings.groups.description')" label-for="input-2">
                            <b-form-textarea
                        id="input-2"
                        v-model="form.description"
                        :placeholder="$t('settings.groups.description')"
                        rows="5"
                        max-rows="6"
                        required
                        ></b-form-textarea>

                    </b-form-group>
                    <b-button type="submit" variant="primary">{{$t('settings.archives.add')}}</b-button>
                </b-form>
            </div>
        </div>
        
    </div>
</template>

<script>
import { mapActions } from "vuex"
export default {
    data(){
        return {
            form:{
                name:'',
                description: ''
            },

        }
    },
    methods: {
        ...mapActions(['addArchive']),
        async submitForm(e){
            e.preventDefault();
            //add archive
            this.addArchive({
                title: this.form.name,
                description: this.form.description,
                created: new Date(),
                id: this.uniqueID      
            })

            this.successToast(
                this.$t('settings.archives.toast.addingArchive'), 
                this.$t('settings.archives.toast.addingArchive') + ' ' + this.form.name
            );

            this.$router.push({ name:'settings.admin.archives'});
            

        }
    },
    computed: {
        uniqueID(){
            return Date.now();
        }
    }

}
</script>
