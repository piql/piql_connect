<template>
    <div class="w-100">
        <page-heading icon="fa-cog" :title="$t('settings.settings.header')" :ingress="$t('settings.settings.ingress')" />
        <div class="row">
            <div class="col-md-12">
                <div>
                    <b-card no-body>
                        <b-tabs nav-class="toggle-nav" card>
                            <b-tab :title="$t('settings.settings.userInterfaceHeader')" active>
                                <b-card-text>
                                    <language-picker v-bind:label="$t('settings.settings.languagelabel')" 
                                     ></language-picker>
                                    <row-selector :rowlabel="$t('settings.settings.rowslabel')"></row-selector>
                                    
                                </b-card-text>
                            </b-tab>
                            <b-tab :title="$t('settings.settings.setPasswordHeader')">
                                <b-card-text>
                                    <password-picker v-bind:oldPasswordLabel="$t('settings.settings.oldPassword.label')" 
                                    v-bind:newPasswordLabel1="$t('settings.settings.newPassword1.label')" 
                                    v-bind:newPasswordLabel2="$t('settings.settings.newPassword2.label')" 
                                    v-bind:saveButtonText="$t('settings.settings.passwordSaveButtonText')" 
                                    @saveButtonClicked="changePassword" ref="passwordPicker">
                                    </password-picker>
                                </b-card-text>
                            </b-tab>
                            <b-tab :title="$t('settings.settings.profileImagelabel')">
                                <b-card-text>
                                    <upload-profile-image v-bind:logolabel="$t('settings.settings.profileImagelabel')"></upload-profile-image>
                                    
                                </b-card-text>
                            </b-tab>

                        

                        </b-tabs>
                    </b-card>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
    export default {

        created(){
             this.fetchUserSettings();
             this.fetchLanguages(); 

        },
        
        
    
        watch: {
            setPasswordData(NewPassword,oldPassword){
                if (NewPassword && NewPassword.status == 0) {
                    this.$refs.passwordPicker.clearData();
                    this.infoToast(
                        this.$t('settings.settings.toasts.passwordUpdated.title'),
                        this.$t('settings.settings.toasts.passwordUpdated.message')
                    );
                    alert(this.$t('settings.settings.alert.changedPassword'));
                    Vue.nextTick( () => { window.location = "/logout"; } );
                }
                else if (NewPassword && NewPassword.status == 1) {
                    this.errorToast(
                        this.$t('settings.settings.toasts.newPasswordTooShort.title'),
                        this.$t('settings.settings.toasts.newPasswordTooShort.message')
                    );
                }
                else if (NewPassword && NewPassword.status == 2) {
                    this.errorToast(
                        this.$t('settings.settings.toasts.oldPasswordWrong.title'),
                        this.$t('settings.settings.toasts.oldPasswordWrong.message')
                    );
                }
                else {
                    this.errorToast(
                        this.$t('settings.settings.toasts.failedSetPassword.title'),
                        this.$t('settings.settings.toasts.failedSetPassword.message')
                    );
                }

            },

        },

        methods: {
            ...mapActions('user',
                ['setNewPassword','fetchLanguages','fetchUserSettings']
            ),
            async changePassword(oldPassword, newPassword1, newPassword2) {
                if (newPassword1 != newPassword2) {
                    this.errorToast(
                        this.$t('settings.settings.toasts.newPasswordMismatch.title'),
                        this.$t('settings.settings.toasts.newPasswordMismatch.message')
                    );
                    return;
                }

                let data = {
                    oldPassword: oldPassword,
                    newPassword: newPassword1
                }

                this.setNewPassword(data);     
            }
        },

        computed: {
            ...mapGetters('user',
                ['setPasswordData']
            ),
            
        }
    }
</script>
