<template>
  <div>
      <form>
            <div class="col-10">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            {{$t('settings.settings.imageProfile.message')}}: {{ allowedExt }}
                        </div>
                        <Dropzone
                            class="dropzone is-6 has-text-centered"
                            :multiple="false"
                            :uploader="uploader" style="margin-right: 0px; width:99%; height:1s0vh;">
                            <file-input multiple
                                :uploader="uploader">
                                <p class="dz-text"><i class="fas fa-cloud-upload-alt"></i> {{$t("upload.addFileButton")}}</p>
                            </file-input>
                        </Dropzone>
                    </div>
                </div>
            </div>
        </form>

    </div>
</template>

<script>
import FineUploader from 'vue-fineuploader';
import FineUploaderTraditional from 'fine-uploader-wrappers'
import VuejsDialog from 'vuejs-dialog';
import Dropzone from 'vue-fineuploader/dropzone';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
Vue.use(VuejsDialog)
const ALLOWED_EXT = ['png','jpg','jpeg'];
export default {
    components: {
        FineUploader,
        Dropzone,
    },
    data() {
        return {
            uploader: {},
        };
    },
    async mounted() {
        let token = (await this.$auth.getTokenSilently());
        const Authorization = `Bearer ${token}`;
        this.uploader = new FineUploaderTraditional({
            options: {
                request: {
                    endpoint: '/api/v1/system/profile/imgUpload',
                    params: {
                        base_directory: 'profileImg',
                        sub_directory: null,
                        optimus_uploader_allowed_extensions: [],
                        optimus_uploader_size_limit: 0,
                        optimus_uploader_thumbnail_height: 100,
                        optimus_uploader_thumbnail_width: 100,
                        qqchunksize: 1024 * 768,
                    },
                    customHeaders: {
                        Authorization
                    }
                },
                validation: {
                    allowedExtensions: ALLOWED_EXT
                },
                chunking: {
                    enabled: true,
                    partSize: 1024*768,
                    mandatory: true,
                    concurrent: {
                        enabled: false
                    },
                },
                callbacks: {
                    onError: (id, name, errorReason, xhrOrXdr) => {
                        let options = {
                            okText: this.$t('OK')
                        };
                        this.$dialog.alert(errorReason, options);
                    },
                    onComplete: async (id, name, response, xhr) => {
                        if( response.success == false ){
                            return;
                        }
                        axios.post('/api/v1/system/profile/img', {
                            'fileName' : name,
                            'result' : response
                        }).then( async ( file ) => {
                            this.infoToast(
                                this.$t('settings.logo.header'),
                                this.$t('settings.logo.successful')
                            );
                            setTimeout(
                                reload => {
                                    this.$router.go();
                                }, 1000
                            )
                        });
                    }
                }
            },
        });
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
        },
        allowedExt() {
            return ALLOWED_EXT.join(', ');
        },
    }

}
</script>

<style>

</style>