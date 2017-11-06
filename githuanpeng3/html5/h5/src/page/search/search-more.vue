<template>
<!-- <transition name="slideright"> -->
  <div class="searchmore">
    <v-header>
      <a slot="main-nav" class="go-backbtn" @click="goBack"></a>
      <p slot="title" class="title">{{title}}</p>
    </v-header>
    <div class="container" ref="scroller">
      <!-- <swiper-column> -->
        <ul @touchmove="handleScroll" ref="scrollerUl">
          <li v-for="item in contentList" class="clearfix" @click="toLiveRoom(item.uid)">
            <div class="poster left">
              <img v-lazy="item.poster" alt="" data-type="live">
            </div>
            <div class="video-item">
              <h3 class="title" v-html="filterColor(item.nick)"></h3>
              <p class="nick">{{item.nick}}</p>
              <p class="info clearfix">
                <span class="left">
                  <i class="fa fa-gamepad"></i>
                  {{item.gameName}}
                </span>
                <span class="right">
                  <i class="fa fa-user" v-if="type=='1'"></i>
                  <i class="fa fa-eye" v-if="type=='3'"></i>
                      {{item.fansCount || item.viewCount}}
                </span>
              </p>
            </div>
          </li>
        </ul>
      <!-- </swiper-column> -->
        <div class="loading" v-show="showLoading||loadingComplete">
          <img src="../../assets/image/icon_loading.gif" alt="" v-show="!loadingComplete">
          <p v-show="loadingComplete">没有更多了</p>
        </div>
    </div>
  </div>
<!-- </transition> -->
  
</template>

<script>
import vHeader from 'components/header/header';
import swiperColumn from 'components/swiper/swiper-column';
import { mapGetters } from 'vuex';
import { homeSearch } from 'api/search';
import { filterColor } from 'assets/js/common';


export default {
  components: {vHeader,swiperColumn},
  data() {
    return {
      keyword: this.$route.query.keyword,
      type: this.$route.query.type==='live'?'1':'3',
      page: 1,
      size: '10',
      client: '1',
      total: 0,
      contentList: [],

      loadingCon: true,
      showLoading: false
    }
  },
  computed: {
    ...mapGetters([
        'uid',
        'encpass'
      ]),
    title() {
      return this.type === '1'?'直播':'视频';
    },
    allPage() {
      return Math.ceil(this.total/parseInt(this.size,10));
    },
    loadingComplete() {
      if(this.page === this.allPage) {
        return true;
      }else {
        return false;
      }
    }
  },
  created() {
    this.init();
    document.body.style.height = '100%';
  },
  destroyed() {
    document.body.style.height = 'auto';
  },
  methods: {
    init() {
      const self = this;
      

      const option = {
        uid: self.uid,
        encpass: self.encpass,
        keyword: self.keyword,
        type: self.type,
        client: self.client,
        page: self.page.toString(),
        size: self.size
      };
      homeSearch(option).then((data)=> {
        // console.log(data);
        if(self.type === '1') {
          self.contentList = data.content.liveList;
        }else {
          self.contentList = data.content.videoList;
        }
        self.total = data.content.total.toString();
      })
    },
    nextPage() {
      const self = this;
      this.loadingCon = false;
      this.showLoading = true;
      this.page++;
      const option = {
        uid: self.uid,
        encpass: self.encpass,
        keyword: self.keyword,
        type: self.type,
        client: self.client,
        page: self.page.toString(),
        size: self.size
      };
      // console.log(option);
      setTimeout(()=> {
        homeSearch(option).then((data)=> {
          if(self.type === '1') {
            self.contentList = self.contentList.concat(data.content.liveList);
          }else {
            self.contentList = self.contentList.concat(data.content.videoList);
          }
          self.loadingCon = true;
          self.showLoading = false;
        })
      },300)
      
    },
    handleScroll() {
      const scrollAllHeight = document.body.scrollHeight;
      const scrollTop = document.body.scrollTop;
      const scrollHeight = document.body.clientHeight;
      // console.log('scrollAllHeight',scrollAllHeight);
      // console.log('scrollTop',scrollTop);
      // console.log('scrollHeight',scrollHeight);
      if(scrollHeight+Math.ceil(scrollTop)>=scrollAllHeight && this.loadingCon && !this.loadingComplete) {
        this.nextPage();
      }
    },
    toLiveRoom(uid) {
      const url = '/mobile/room/room.html?luid=' + uid;
      location.href = url;
    },
  	goBack() {
  	  this.$router.go(-1);
  	},
    filterColor
  }
}
</script>

<style lang="less">
@import "~assets/css/common";

.slideright-enter-active,
.slideright-leave-active {
  transition: all .3s;
}

.slideright-enter,
.slideright-leave-to {
  transform: translate3d(100%,0,0);
}
.searchmore {
  li {
    border-bottom: 1px solid #ddd;
    margin-bottom: 16/@myrem;
    &:last-child {
      border-bottom: 0 none;
    }
  }
  .poster {
    margin-right: 16/@myrem;
    img {
      width: 256/@myrem;
      height: 192/@myrem;
    }
  }
  .video-item {
    position: relative;
    height: 192/@myrem;
    overflow: hidden;
    .title {
      font-size: 26/@myrem;
      padding-top: 6/@myrem;
      color: #282828;
    }
    .nick {
      font-size: 24/@myrem;
      color: #787878;
      margin: 20/@myrem 0;
    }
    .info {
      position: absolute;
      width: 100%;
      left: 0;
      bottom: 0;
      font-size: 20/@myrem;
      color: #929292;
      i {
        font-size: 32/@myrem;
      }
    }
  }
  .container {
    padding: 110/@myrem 15/@myrem 0;
  }
  .loading {
    text-align: center;
    font-size: 26/@myrem;
    color: #333;
    padding: 26/@myrem 0;
    img {
      width: 50/@myrem;
    }
  }
  .search-key {
    color: @hpColor;
  }
}
</style>