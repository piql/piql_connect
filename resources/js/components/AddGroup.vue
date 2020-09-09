<template>
    <div>
        <b-form @submit="submitForm">
            
            <b-form-group id="input-group-2" :label="$t('settings.groups.group')" label-for="input-1">
                <b-form-input
                id="input-1"
                v-model="form.group"
                required
                :placeholder="$t('settings.groups.group')"
                
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
            <b-button type="submit" variant="primary">{{$t('settings.groups.addGroup')}}</b-button>
        </b-form>
        

    </div> 
</template>

<script>
export default {
    data(){
        return{
            form: {
                group: '',
                description: ''
            }
        }
    },
    methods:{
        submitForm(e){
            e.preventDefault();
            this.$emit('addGroup', this.form)

        }
    },
    computed:{

    },
    watch: {

    }

}
</script>
