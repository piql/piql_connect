<template>
    <div class="ml-3">
        <div class="form-group row">
            <label for="oldPassword" class="col-form-label-sm">
                {{oldPasswordLabel}}
            </label>
            <input v-model="oldPassword" type="password" class="col-3 form-control" name="oldPasswordInput" placeholder="" required>
        </div>
        <div class="form-group row">
            <label for="newPassword1" class="col-form-label-sm">
                {{newPasswordLabel1}}
            </label>
            <input v-model="newPassword1" type="password" class="col-3 form-control" name="newPassword1Input" placeholder="" required pattern='^(.){8,}$'>
        </div>
        <div class="form-group row">
            <label for="newPassword2" class="col-form-label-sm">
                {{newPasswordLabel2}}
            </label>
            <input v-model="newPassword2" type="password" class="col-3 form-control" name="newPassword2Input" placeholder="" required pattern='^(.){8,}$'>
        </div>
        <div class="form-group row">
            <button type="button" class="btn form-control-btn" @click="saveClicked">{{saveButtonText}}</button>
        </div>
    </div>
</template>

<script>
import JQuery from 'jquery';
let $ = JQuery;

export default {
    methods: {
        saveClicked() {
            this.$emit('saveButtonClicked', this.oldPassword, this.newPassword1, this.newPassword2);
        },
        clearData() {
            this.oldPassword = '';
            this.newPassword1 = '';
            this.newPassword2 = '';
        }
    },
    data() {
        return {
            oldPassword: '',
            newPassword1: '',
            newPassword2: ''
        };
    },
    props: {
        oldPasswordLabel: {
            type: String,
            default: ''
        },
        newPasswordLabel1: {
            type: String,
            default: ''
        },
        newPasswordLabel2: {
            type: String,
            default: ''
        },
        saveButtonText: {
            type: String,
            default: ''
        },
    },
    watch: {
    },
    computed: {
    }
}

</script>
