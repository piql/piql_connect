<template>
    <div class="w-100">
        <page-heading icon="fa-archive" :title="$t('settings.collections.title')" :ingress="$t('settings.collections.description')" />
        <div class="card">
            <div class="card-header">
               <i class="fa fa-plus"></i> {{$t('settings.collections.add').toUpperCase()}}
            </div>
            <div class="card-body">
                <b-form @submit="submitForm">
            
                    <b-form-group id="input-group-2" :label="$t('settings.collections.collection')" label-for="input-1">
                        <b-form-input
                        id="input-1"
                        v-model="form.name"
                        required
                        :placeholder="$t('settings.collections.collection')"

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
                    <b-button type="submit" variant="primary">{{$t('settings.collections.add')}}</b-button>
                </b-form>
            </div>
        </div>
        
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex"
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
        ...mapActions(['addCollection', 'fetchAccounts']),
        async submitForm(e){
            e.preventDefault();
            //add collection
            this.addCollection({
                title: this.form.name,
                description: this.form.description,
                account: this.firstAccountId
            });

            this.successToast(
                this.$t('settings.collections.toast.addingCollection'),
                this.$t('settings.collections.toast.addingCollection') + ' ' + this.form.name
            );

            this.$router.push({ name:'settings.admin.collections'});


        }
    },
    async mounted() {
        await this.fetchAccounts();
    },
    computed: {
        ...mapGetters(['firstAccount']),
        firstAccountId() {
            return this.firstAccount.id;
        }
    }

}
</script>
