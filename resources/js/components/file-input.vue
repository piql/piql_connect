<template>
  <div class="vue-fine-uploader-file-input">
    <label>
      <slot>Upload File</slot>
      <input type="file"
             @change="_onFilesSelected"
             :multiple="multiple"
             :accept="accept"
             :required="required"
             :disabled="disabled"/>
    </label>
  </div>
</template>

<style>
  .vue-fine-uploader-file-input {
    display: inline-block;
  }

  .vue-fine-uploader-file-input label {
    cursor: pointer;
  }

  .vue-fine-uploader-file-input input[type="file"] {
    width: 1px;
    height: 1px;
    opacity: 0;
  }
</style>

<script>
  export default {
    props: {
      multiple: {
        type: Boolean,
        default: false
      },
      required: {
        type: Boolean,
        default: false
      },
      accept: {
        type: String,
        default: ''
      },
      uploader: {
        type: Object,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },

    data () {
      return {
        _unmounted: false
      }
    },

    beforeDestroy () {
      this._unmounted = true
    },

    methods: {
      _onFilesSelected (e) {
        this.uploader.methods.addFiles(e.target)
      }
    }
  }
</script>
