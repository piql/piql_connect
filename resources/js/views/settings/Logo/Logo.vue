<template>
    <div class="w-100">
        <page-heading icon="fa-shapes" :title="$t('settings.logo.header')" :ingress="$t('settings.logo.ingress')" />

        <form>
            <div class="col-10">
                <FileInputComponent
                    id="uploadbutton"
                    :multiple="false"
                    :uploader="uploader"
                    :disabled="false">
                    <div>
                        <i class="fas fa-upload"></i> {{$t("settings.logo.button")}}
                    </div>
                </FileInputComponent>
            </div>
        </form>
    </div>
</template>

<script>
    import FineUploader from 'vue-fineuploader';
    import FineUploaderTraditional from 'fine-uploader-wrappers'
    import VuejsDialog from 'vuejs-dialog';
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    Vue.use(VuejsDialog);
    export default {
        components: {
                FineUploader
        },
        data() {
            const uploader = new FineUploaderTraditional({
                options: {
                    request: {
                        endpoint: '/api/v1/admin/logo/upload',
                    },
                    validation: {
                        allowedExtensions: ['png']
                    },
                    callbacks: {
                        onError: showError => {
                            let options = {
                                okText: this.$t('OK')
                            };
                            this.$dialog.alert(this.$t('settings.log.alert.notAllowedExtensions'), options);
                        },
                        onComplete: complete => {
                            this.infoToast(
                                this.$t('settings.logo.header'),
                                this.$t('settings.logo.successful')
                            );
                            setTimeout(
                                reload => {
                                    this.$router.go();
                                }, 2000
                            )
                        }
                    }
                },
            });
            return {
                uploader: uploader,
            };
        },
    }
</script>
