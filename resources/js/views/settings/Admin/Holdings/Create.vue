<template>
    <div class="w-100">
        <page-heading icon="fa-folder" :title="$t('settings.holdings.title')" :ingress="$t('settings.holdings.description')" />
        <div class="card">
            <div class="card-header">
               <i class="fa fa-plus"></i> {{$t('settings.holdings.add').toUpperCase()}}
            </div>
            <div class="card-body">
                <b-form @submit="submitForm">
            
                    <b-form-group id="input-group-2" :label="$t('settings.holdings.holding')" label-for="input-1">
                        <b-form-input
                        id="input-1"
                        v-model="form.name"
                        required
                        :placeholder="$t('settings.holdings.holding')"
                        
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group id="input-group-2" :label="$t('settings.archives.archive')" >
                        <b-form-select v-model="form.archiveId" :options="options"></b-form-select>
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
                    <b-button type="submit" variant="primary">{{$t('settings.holdings.add')}}</b-button>
                </b-form>
            </div>
        </div>
        
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex"
export default {
    data(){
        return {
            form:{
                name:'',
                description: '',
                archiveId: '',
            },
            options: []

        }
    },
    async mounted(){
        this.options = this.retrievedArchives.map(archive => {
            return {
                text: archive.title,
                value: archive.id
            }
        })

    },
    methods: {
        ...mapActions(['addHolding']),
        async submitForm(e){
            e.preventDefault();
            
            this.addHolding({
                title: this.form.name,
                description: this.form.description,
                archiveId: this.form.archiveId,
                created: new Date(),
                id: this.uniqueID      
            })

            this.successToast(
                this.$t('settings.holdings.toast.addingHolding'), 
                this.$t('settings.holdings.toast.addingHolding') + ' ' + this.form.name
            );

            this.$router.push({ name:'settings.admin.holdings'});
            

        }
    },
    computed: {
        ...mapGetters(['retrievedArchives']),
        uniqueID(){
            return Date.now();
        }
    }

}
</script>
