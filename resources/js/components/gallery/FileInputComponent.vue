<template>
  <FileInput 
    :uploader="uploader"
    :multiple="multiple"
    :disabled="disabled"
    >

    <span class='btn vue-fine-uploader-gallery-file-input-content form-control-btn' v-bind:class='[{ disabled: disabled }]'>
      <slot>
        <UploadIcon class='vue-fine-uploader-gallery-file-input-upload-icon' />
        Select Files
      </slot>
    </span>
  </FileInput>
</template>


<script>
  import FileInput from '../file-input'
  import UploadIcon from './upload-icon'
  export default {
    components: {
      FileInput,
      UploadIcon
    },
    props: {
      uploader: {
        type: Object,
        required: true
      },
      multiple: {
        type: Boolean,
        default: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
    }
  }
</script>
