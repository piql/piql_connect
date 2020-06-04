<template>
    <div class="w-100">
        <page-heading icon="fa-cog" :title="$t('settings.settings.header')" :ingress="$t('settings.settings.ingress')" />

        <form>
            <h2 class="mb-3">{{$t('settings.settings.userInterfaceHeader')}}</h2>
            <div class="row">
                <div class="col-10">
                    <language-picker v-bind:label="$t('settings.settings.languagelabel')" :initialSelection="selectedLanguage" @selectionChanged="changedLanguage"></language-picker>
                </div>
            </div>
            <h2 class="mt-4 mb-3">{{$t('settings.settings.setPasswordHeader')}}</h2>
            <div class="row">
                <div class="col-10">
                    <password-picker v-bind:oldPasswordLabel="$t('settings.settings.oldPassword.label')" v-bind:newPasswordLabel1="$t('settings.settings.newPassword1.label')" v-bind:newPasswordLabel2="$t('settings.settings.newPassword2.label')" v-bind:saveButtonText="$t('settings.settings.passwordSaveButtonText')" @saveButtonClicked="changePassword" ref="passwordPicker"></password-picker>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                userSettings: null
            };
        },

        async mounted() {
            this.userSettings = (await axios.get("/api/v1/system/currentUserSettings")).data;
        },

        methods: {
            async changedLanguage(language) {
                if (language && language != this.selectedLanguage) {
                    await axios.post("/api/v1/system/settings", {"interface": { "language": language }});
                    this.$router.go(0);
                }
            },
            async changePassword(oldPassword, newPassword1, newPassword2) {
                if (newPassword1 != newPassword2) {
                    this.errorToast(
                        this.$t('settings.settings.toasts.newPasswordMismatch.title'),
                        this.$t('settings.settings.toasts.newPasswordMismatch.message')
                    );
                    return;
                }

                let setPasswordData = (await axios.post("/api/v1/system/currentUserPassword/", {
                    oldPassword: oldPassword,
                    newPassword: newPassword1
                })).data;

                if (setPasswordData.status == 0) {
                    this.$refs.passwordPicker.clearData();
                    this.infoToast(
                        this.$t('settings.settings.toasts.passwordUpdated.title'),
                        this.$t('settings.settings.toasts.passwordUpdated.message')
                    );
                    alert(this.$t('settings.settings.alert.changedPassword'));
                    Vue.nextTick( () => { window.location = "/logout"; } );
                }
                else if (setPasswordData.status == 1) {
                    this.errorToast(
                        this.$t('settings.settings.toasts.newPasswordTooShort.title'),
                        this.$t('settings.settings.toasts.newPasswordTooShort.message')
                    );
                }
                else if (setPasswordData.status == 2) {
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
            }
        },

        computed: {
            selectedLanguage() {
                if (this.userSettings && this.userSettings.interface && this.userSettings.interface.language) {
                    return this.userSettings.interface.language;
                }
                return '';
            }
        }
    }
</script>
