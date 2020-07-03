<template>
  <div>
    <!-- The overlay -->
    <div id="myNav" :class="myNavClass">

      <!-- Button to close the overlay navigation -->
      <a href="javascript:void(0)" class="closebtn" v-on:click="closeNav()">&times;</a>

      <!-- Overlay content -->
      <div class="overlayContent">
        <div class="overlayContentImg">
          <img :src="currentImgSrc" :style="'transform: translateY(-50%) scale(' + zoomRate + ') rotate(' + rotateRate + 'deg)'"/>
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
            <div v-for="img, i in imgs" class="toolBoxImg" @click="thumbClick(i)" v-if=""><img :src="img" :style="index == i ? 'border: 3px #ff6633 solid' : 'border: 3px #000000 solid'"/></div>
          </div>
          <div :class="overlayContentNavButtonNext">
            <button @click="nav(+1)" class="btn btn-sm btn-info"><i class="fas fa-angle-right"></i></button>
          </div>
          <div :class="overlayContentPageButtonNext">
            <button @click="pageNav(+1)" class="btn btn-sm btn-info"><i class="fas fa-angle-double-right"></i></button>
          </div>
        </div>
        <div :class="toolBoxImgPanel">
          <button class="btn btn-sm btn-info" @click="zoom(-1)"><i class="fas fa-search-minus"></i></button>
          <button class="btn btn-sm btn-info" @click="zoom(+1)"><i class="fas fa-search-plus"></i></button>
          <button class="btn btn-sm btn-info" @click="rotate(+1)"><i class="fas fa-sync-alt"></i></button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
  export default {
    created: function() {
      this.currentImgSrc = "";
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
        default: "",
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
        this.index += adj;
        this.setImgSrc();
      },
      setImgSrc() {
        this.currentImgSrc = this.imgs != null && this.imgs.length > 0 ? this.imgs[this.index] : "";
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
    },
    computed: {
      myNavClass: function () {
        this.setImgSrc();
        return "overlay overlay" + (this.visible ? "On" : "Off")
      },
      zoomRate: function () {
        return this.zoomIndex * 0.1;
      },
      rotateRate: function () {
        return this.rotateIndex * 90;
      }
    },
    data() {
      return {
        currentImgSrc: this.currentImgSrc,
        zoomIndex: this.zoomIndex,
        rotateIndex: this.rotateIndex,
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
</style>
