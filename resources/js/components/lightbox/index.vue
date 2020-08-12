<template>
  <div ref="previewMainDiv" tabindex="0" @keydown.esc="getOut">
    <!-- The overlay -->
    <div id="myNav" :class="myNavClass">

      <!-- Button to close the overlay navigation -->
      <a href="javascript:void(0)" class="closebtn" v-on:click="closeNav()">&times;</a>

      <!-- Overlay content -->
      <div class="overlayContent">
        <div class="overlayContentImg">
          <img v-if="isPlayableFile == false" :src="currentImgSrc" :alt="currentFileName" :style="'transform: translateY(-50%) scale(' + zoomRate + ') rotate(' + rotateRate + 'deg)'"/>
          <video-player v-if="isPlayableFile == true" :options="videoOptions" class="overlayContentImgVideoPlayer" :style="isPlayableAudio ? 'top: 50%;' : '' "/>
          <div v-if="isPlayableFile == null" class="spinner-border text-warning" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>

      <div class="toolBox">
        <div :class="toolBoxNavPanel">
          <div :class="overlayContentPageButtonPrev">
            <button @click="pageNav(-1)" class="btn btn-sm btn-info"><i class="fas fa-angle-double-left"></i></button>
          </div>
          <div :class="overlayContentNavButtonPrev">
            <button @click="nav(-1)" class="btn btn-sm btn-info"><i class="fas fa-angle-left"></i></button>
          </div>
          <div class="toolBoxImgList">
            <div v-for="thumb, i in thumbs" class="toolBoxImg" @click="thumbClick(i)" :title="thumb.name"><img :src="thumb.img" :alt="thumb.name" :class="index == i ? 'selectedThumb' : 'regularThumb'"/></div>
          </div>
          <div :class="overlayContentNavButtonNext">
            <button @click="nav(+1)" class="btn btn-sm btn-info"><i class="fas fa-angle-right"></i></button>
          </div>
          <div :class="overlayContentPageButtonNext">
            <button @click="pageNav(+1)" class="btn btn-sm btn-info"><i class="fas fa-angle-double-right"></i></button>
          </div>
        </div>
        <div :class="toolBoxImgPanel" :style="isPlayableFile == false ? 'visibility:' : 'visibility:hidden'">
          <button class="btn btn-sm btn-info" @click="zoom(-1)"><i class="fas fa-search-minus"></i></button>
          <button class="btn btn-sm btn-info" @click="zoom(+1)"><i class="fas fa-search-plus"></i></button>
          <button class="btn btn-sm btn-info" @click="rotate(+1)"><i class="fas fa-sync-alt"></i></button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
  import VideoPlayer from '@components/lightbox';

  export default {
    created: function() {
      this.currentImgSrc = "";
      this.isMultimedia = false;
      this.currentFileName = "";
      this.currentFileType = "";
      this.toolBoxNavPanel = "toolBoxNavPanelOff";
      this.toolBoxImgPanel = "toolBoxImgPanelOff";
      this.overlayContentNavButtonPrev = "overlayContentNavButtonOff";
      this.overlayContentNavButtonNext = "overlayContentNavButtonOff";
      this.overlayContentPageButtonPrev = "overlayContentPageButton overlayContentPageButtonOff";
      this.overlayContentPageButtonNext = "overlayContentPageButton overlayContentPageButtonOff";
      this.zoomIndex = 0;
      this.rotateIndex = 0;
    },
    props: {
      visible: {
        type: Boolean,
        default: false
      },
      imgs: {
        type: Array,
        default: null,
      },
      fileNames: {
        type: Array,
        default: null,
      },
      fileTypes: {
        type: Array,
        default: null,
      },
      hide: {
        type: Function
      },
      pageNav: {
        type: Function
      },
      totalImgs: {
        type: Number,
        default: 0
      },
      index: {
        type: Number,
        default: 0
      },
      perPage: {
        type: Number,
        default: 0
      },
      page: {
        type: Number,
        default: 0
      },

    },
    methods: {
      closeNav: function () {
        this.hide()
      },
      nav: function(adj) {
        this.isMultimedia = null;
        this.index += adj;
        this.setImgSrc();
      },
      isPlayableTypes(fileType, extArr) {
        if (fileType != null) {
          let nameArr = fileType.split("/");
          for (let i=0;i<extArr.length;i++) {
            if (nameArr.includes(extArr[i].toLowerCase())) {
              return true;
            }
          }
        }
        return false;
      },
      isPlayable(fileType) {
          return this.isPlayableTypes(fileType, ['audio', 'video']);
      },
      setImgSrc() {
        if (this.imgs != null && this.imgs.length > this.index - 1 && this.imgs[this.index] != undefined && this.imgs[this.index] != null) {
         this.currentImgSrc = this.imgs[this.index];
        } else {
         this.currentImgSrc = null;
        }
        this.currentFileName = this.fileNames != null && this.fileNames.length > this.index - 1 ? this.fileNames[this.index] : "";
        this.currentFileType = this.fileTypes != null && this.fileTypes.length > this.index - 1 ? this.fileTypes[this.index] : "";
        this.isMultimedia = this.isPlayable(this.currentFileType) && this.currentImgSrc != undefined && this.currentImgSrc != null;
        this.videoOptionsHeight = this.isPlayableAudio ? 30 : null;
        this.toolBoxNavPanel = "toolBoxNavPanel" + (this.imgs.length >= 1 ? "On" : "Off");
        this.toolBoxImgPanel = "toolBoxImgPanel" + (this.imgs.length > 0 ? "On" : "Off");
        this.overlayContentNavButtonPrev = "overlayContentNavButtonPrev overlayContentNavButton" + (this.index > 0 ? "On" : "Off");
        this.overlayContentNavButtonNext = "overlayContentNavButtonNext overlayContentNavButton" + (this.index < this.imgs.length - 1 ? "On" : "Off");
        this.overlayContentPageButtonPrev = "overlayContentPageButton overlayContentPageButton" + (this.totalImgs > this.perPage && this.page > 1 ? "On" : "Off");
        this.overlayContentPageButtonNext = "overlayContentPageButton overlayContentPageButton" + (this.totalImgs > this.perPage && (this.page < Math.ceil(this.totalImgs / this.perPage)) ? "On" : "Off");
        this.zoomIndex = 10;
        this.rotateIndex = 0;
      },
      thumbClick(index) {
        this.isMultimedia = null;
        this.index = index;
        this.setImgSrc();
      },
      zoom: function(adj) {
        this.zoomIndex += adj;
        if ( this.zoomIndex < 1 ) {
          this.zoomIndex = 1;
        } else if ( this.zoomIndex > 20 ) {
          this.zoomIndex = 20;
        }
      },
      rotate: function(adj) {
        this.rotateIndex += adj;
      },
      getOut: function() {
        this.closeNav();
      },
      prepareFocus: function(){
        if (this.$refs.previewMainDiv != null && this.$refs.previewMainDiv != undefined) {
          this.$refs.previewMainDiv.focus();
        }
      },
    },
    computed: {
      myNavClass: function () {
        this.setImgSrc();
        this.prepareFocus();
        return "overlay overlay" + (this.visible ? "On" : "Off")
      },
      zoomRate: function () {
        return this.zoomIndex * 0.1;
      },
      rotateRate: function () {
        return this.rotateIndex * 90;
      },
      playableType: function() {
          return "video/mp4";
      },
      thumbs: function() {
        if (this.thumbList == undefined || this.thumbList == null || this.thumbList.length != this.imgs.length) {
          this.thumbList = [];
          for (let i=0; i < this.imgs.length; i++) {
            this.thumbList[this.thumbList.length] = {
              img: this.fileNames != null && this.fileNames.length > this.index - 1 && this.isPlayable(this.fileTypes[i]) ? '/api/v1/media/thumb/' + this.fileNames[i] : this.imgs[i],
              name: this.fileNames[i],
            } 
          }
        }
        return this.thumbList;
      },
      isPlayableAudio: function() {
          return this.isPlayableTypes(this.currentFileType, ['audio']);
      },
      videoOptions: function () {
        return {
          autoplay: true,
          controls: true,
          width: 900,
          height: this.videoOptionsHeight,
          sources: [
            {
              src: this.currentImgSrc,
              type: "video/mp4"
            }
          ]
  			}
      },
      isPlayableFile: function() {
        if (this.isMultimedia && !this.reloading) {
          this.isMultimedia = null;
          this.reloading = true;
          setTimeout(() => this.isMultimedia = true, 500);
        } else if (this.isMultimedia && this.reloading) {
          this.reloading = false;
        }
        return this.isMultimedia;
      }
    },
    data() {
      return {
        currentImgSrc: this.currentImgSrc,
        currentFileName: this.currentFileName,
        currentFileType: this.currentFileType,
        zoomIndex: this.zoomIndex,
        rotateIndex: this.rotateIndex,
        reloading: false,
        isMultimedia: null,
        thumbList: null
      }
    },
  }
</script>

<style scoped>
  .overlayOn {
    width: 100%;
  }
  .overlayOff {
    width: 0%;
  }
  .overlay {
    height: 100%;
    position: fixed;
    z-index: 9990;
    left: 0;
    top: 0;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0, 0.5);
    overflow-x: hidden;
    overflow-y: hidden;
    transition: 0.5s;
  }
  .overlayContent {
    overflow: hidden;
    top: 100px;
    width: 100%;
    height: 100%;
    text-align: center;
    margin-top: 30px;
  }
  .overlay a {
    padding: 8px;
    text-decoration: none;
    font-size: 36px;
    color: #d0d0d0;
    display: block;
    transition: 0.3s;
  }
  .overlay a:hover, .overlay a:focus {
    color: #ff6633;
  }
  .overlay .closebtn {
    position: absolute;
    right: 45px;
    font-size: 60px;
    color: #000000;
    z-index: 9999;

  }
  .overlayContentNavButtonOn {
    display: inline-block;
  }
  .overlayContentNavButtonOff {
    display: inline-block;
    visibility: hidden;
  }
  .overlayContentNavButtonPrev {
    margin-left: 10px;
  }
  .overlayContentNavButtonNext {
    margin-right: 10px;
  }
  .overlayContentImg {
    display: inline-block;
    width: 100%;
    height: 100%;
    margin: 0 auto;
  }
  .overlayContentImg img {
    transition: transform 0.25s ease;
    box-shadow: 0 0 10px #222;
    -webkit-box-shadow: 0 0 10px #222;
    margin: 15px;
    position: relative;
    top: 50%;
  }
  .overlayContentImgVideoPlayer {
    position: relative;
    display: inline-block;
    box-shadow: 0 0 10px #222;
    -webkit-box-shadow: 0 0 10px #222;
    margin: 15px;
  }
  .toolBox {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background-color: gray;
    border-top: 1px #000000 solid;
    text-align: center;
    padding-top: 10px;
  }
  .toolBoxNavPanelOn {
    display: inline-block;
  }
  .toolBoxNavPanelOff {
    display: none;
  }
  .toolBoxImgList {
    display: inline-block;
  }
  .toolBoxImg{
    display: inline-block;
    margin-left: 5px;
    margin-right: 5px;
    cursor: pointer;
  }
  .toolBoxImg img {
    max-width: 80px;
    max-height: 80px;
    border-radius: 5px;
  }
  .overlayContentPageButton {
    display: inline-block;
  }
  .overlayContentPageButtonOff {
    visibility: hidden;
  }
  .toolBoxImgPanelOff {
    display: none;
  }
  .toolBoxImgPanelOn {
    margin-right: 30px;
    margin-bottom: 30px;
    float: right;
  }
  .selectedThumb {
    border: 3px #ff6633 solid
  }
  .regularThumb {
    border: 3px #000000 solid
  }
</style>
