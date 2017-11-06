<template>
  <div ref="slider" class="anchor-slider">
    <ul class="clearfix" ref="sliderUl" :style="{width:ulWidth}">
      <li class="anchor-item" v-for="item in data" :style="{width:liWidth}" @click="toLiveRoom(item.uid)">
          <div class="avatar">
            <img v-lazy="item.head" data-type="userface">
            <span class="icon">
              <!-- <i class="fa fa-video-camera" v-if="item.isliving=='1'"></i> -->
              <img src="../../assets/image/Search_icon_onAir@2x.png" v-if="item.isliving=='1'">
            </span>
            
          </div>
        <p class="name" v-html="filterColor(item.nick)"></p>
        <p class="fans">{{item.fansCount}}人关注</p>
      </li>
    </ul>
  </div>
</template>

<script>
import IScroll from 'iscroll';
import { filterColor } from 'assets/js/common';

export default {
  props: {
  	data: {
      type: Array
    },
    keyword: {}
  },
  mounted() {
  	this.init();
  },
  updated() {
    this.init();
  },
  data() {
    return {
      liWidth: document.body.clientWidth/5+ 'px'
    }
  },
  computed: {
    ulWidth() {
      return document.body.clientWidth/5*this.data.length + 'px';
    }
  },
  methods: {
  	init() {
      this.$nextTick(()=> {
        this.sldier = new IScroll(this.$refs.slider,{
          scrollX: true,
          click: true
        });
      })
    },
    filterColor,
    toLiveRoom(uid) {
      const url = '/mobile/room/room.html?luid=' + uid;
      location.href = url;
    }
  }
}
</script>

<style lang="less">
@import "~assets/css/common";

.anchor-slider {
  overflow: hidden;
}
	.anchor-item {
    float: left;
  
    text-align: center;
    .avatar {
      position:relative;
      display: inline-block;
      width: 100/@myrem;
      height: 100/@myrem;
      border-radius: 50%;
      img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
      }
      .icon {
        color: @hpColor;
        position: absolute;
        bottom: 14/@myrem;
        right: -7/@myrem;
        img {
          width: 42/@myrem;
          height: 32/@myrem;
        }
      }
    }
    .name {
      font-size: 26/@myrem;
      margin: 9/@myrem 0 18/@myrem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #282828;
    }
    .fans {
      font-size: 22/@myrem;
      color: #929292;
    }
  }
</style>