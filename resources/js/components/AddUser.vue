<template>
    <div>
        <b-form @submit="submitForm">
            
            <b-form-group id="input-1" :label="$t('settings.listing.fullname')" label-for="input-1">
                <b-form-input
                id="input-1"
                class="mb-4"
                v-model="form.fullname"
                required
                :placeholder="$t('settings.listing.fullname')"
                
                ></b-form-input>
            </b-form-group>
            <b-form-group id="input-2" :label="$t('settings.listing.username')" label-for="input-2">
                <b-form-input
                id="input-2"
                class="mb-4"
                v-model="form.username"
                required
                :placeholder="$t('settings.listing.username')"
                
                ></b-form-input>
            </b-form-group>
            <b-form-group id="input-3" :label="$t('settings.listing.email')" label-for="input-3">
                <b-form-input
                id="input-3"
                class="mb-4"
                v-model="form.email"
                required
                :placeholder="$t('settings.listing.email')"
                
                ></b-form-input>
            </b-form-group>

            
            <b-button type="submit" variant="primary">{{$t('settings.settings.addUser')}}</b-button>
        </b-form>

    </div>
  
</template>

<script>
export default {
    data(){
        return {
            form: {
                fulllname: '',
                email: '',
                username: ''
            }
        }
    },
    methods:{
        submitForm(e){
            e.preventDefault();
            this.$emit('addUser',this.form)
        }
    }

}
</script>
