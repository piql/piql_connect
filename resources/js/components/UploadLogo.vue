<template>
  <div>
      <form>
            <div class="col-10">
                <FileInputComponent
                    id="uploadbutton"
                    :multiple="false"
                    :uploader="uploader"
                    :disabled="false">
                    <div>
                        <i class="fas fa-upload"></i> {{logolabel}}
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
Vue.use(VuejsDialog)

export default {
    components: {
        FineUploader
    },
    data() {
        const uploader = new FineUploaderTraditional({
                options: {
                    request: {
                        endpoint: '', //some endpoint
                    },
                    validation: {
                        allowedExtensions: ['png','jpg']
                    },
                    callbacks: {
                        onError: (id, name, errorReason, xhrOrXdr) => {
                            let options = {
                                okText: this.$t('OK')
                            };
                            this.$dialog.alert(errorReason, options);
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
    props: {
        logolabel: {
            type: String,
            default: ""
        }
    },
    computed: {
        showLabel: function() {
            return this.logolabel.length > 0;
        }
    }

}
</script>

<style>

</style>