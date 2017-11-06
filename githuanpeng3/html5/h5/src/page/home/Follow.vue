<template>
  <div class="home-container">
    <div class="unlogin" v-if="page_type == 1">
      <div class="container">
        <div class="unlogin-img"></div>
        <p class="message">登录后才可以浏览你“关注的直播”哦</p>
        <a href="/mobile/h5login/index.html">
          <button class="login">
            立即登录
          </button>
        </a>
      </div>
    </div>
    <div class="no-live" v-if="page_type == 2">
      <div class="container">
        <div class="no-live-img"></div>
        <p class="message">你关注的主播还没有开播哦</p>
      </div>
    </div>
    <load-more :pullup="addFollowList" :touchEvent="touchEvent" v-if="page_type == 3">
      <div class="follow-list" slot="list">
        <video-list :video-list="liveItems"></video-list>
        <!-- <video-list :video-list="liveItems"></video-list>
        <video-list :video-list="liveItems"></video-list> -->
      </div>
      <transition enter-active-class="animated fadeInUp" leave-active-class="animated fadeOutDown" slot="bottom">
        <p class="hint" v-show="isAllLoaded">没有更多了</p>
      </transition>
    </load-more>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import videoList from 'components/videoList/videoList';
import loadmore from 'components/loadmore/loadmore';
import qs from 'qs';

export default {
  name: 'home_Hot',
  data() {
    return {
      size: 10,
      page: 1,
      liveItems: [],
      liveTotal: 0,
      total: 0,
      maxPage: 0,
      touchEvent: true,
      isAllLoaded: false
    }
  },
  computed: {
    numberFormat() {
      return this.$common
    }
  },
  created() {
  },
  mounted() {
    this.initFollowList();
  },
  methods: {
    initFollowList() {
      if (this.uid) {
        this.page = 1;
        this.touchEvent = false;
        this.$ajax.post('/api/user/info/followList.php', qs.stringify({
          uid: this.uid,
          encpass: this.encpass,
          client: '0',
          page: this.page.toString(),
          size: this.size.toString()
        })).then((res) => {
          const response = res.data.content;
          this.liveItems = response.list;
          this.liveTotal = parseInt(response.liveTotal, 10) || 0;
          this.total = parseInt(response.total, 10) || 0;
          console.log('liveItems', this.liveItems, 'liveTotal', this.liveTotal, 'total', this.total);
          this.maxPage = Math.ceil(parseInt(this.liveTotal, 10) / this.size);
          this.touchEvent = true;
        })
      }
    },
    addFollowList() {
      this.isAllLoaded = true;
      if (this.isAllLoaded === true) {
        setTimeout(() => {
          this.isAllLoaded = false;
        }, 2000)
      }
      // if (this.page < this.maxPage) {
      //   this.touchEvent = false;
      //   this.page += 1;
      //   console.log(this.page, this.maxPage);
      //   this.$ajax.post('/api/user/info/followList.php', {
      //     uid: this.uid,
      //     encpass: this.encpass,
      //     client: '0',
      //     page: this.page.toString(),
      //     size: this.size.toString()
      //   }).then((res) => {
      //     const response = res.data.content;
      //     console.log('response', response);
      //     let newLiveTotal = parseInt(response.liveTotal);
      //     if (newLiveTotal !== this.liveTotal) {
      //       // 当直播数量变化时，重置maxPage
      //       this.liveTotal = newLiveTotal;
      //       this.maxPage = Math.ceil(newLiveTotal / size);
      //       if (page > maxPage) {
      //         // 当关注的直播数减少时
      //         this.page = this.maxPage;
      //         // ...
      //       }
      //     }
      //     for (let i = 0; i < response.list.length; i++) {
      //       this.liveItems.push(response.list[i]);
      //     }
      //     this.total = response.total;
      //     // console.log('liveItems', this.liveItems, 'liveTotal', this.liveTotal, 'total', this.total);
      //     this.touchEvent = true;
      //   })
      // }
    }

  },
  computed: {
    ...mapGetters([
      'uid',
      'encpass'
    ]),
    page_type() {
      if (this.uid) {
        if (this.liveTotal !== 0) {
          return 3;// follow page
        }
        else {
          return 2;// no live page
        }
      }
      else {
        return 1;// unlogin page
      }
    }
  },
  components: { 'video-list': videoList, 'load-more': loadmore }
}
</script>

<style lang="less" scoped>
@import '~assets/css/animate';
@myrem :75rem;

.unlogin {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: center;
  -webkit-align-items: center;
  background-color: rgb(245, 245, 245);
}

.no-live {
  .unlogin
}

.follow-list {
  padding-top: 20/@myrem;
}

.container {
  width: 450/@myrem;
  margin: 18vh 0;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: center;
  -webkit-align-items: center;
  -webkit-flex-wrap: wrap;
  .unlogin-img {
    width: 240/@myrem;
    height: 240/@myrem;
    background: url("../../assets/image/follow_page_unlogin.png") no-repeat center;
    background-size: contain;
  }
  .no-live-img {
    .unlogin-img;
    background: url("../../assets/image/follow_page_noLive.png") no-repeat center;
    background-size: contain;
  }
  .message {
    margin: 20/@myrem 0;
    width: 450/@myrem;
    height: auto;
    font-size: 35/@myrem;
    color: rgb(146, 146, 146);
    line-height: 60/@myrem;
    text-align: center;
  }
  .login {
    width: 280/@myrem;
    height: 80/@myrem;
    padding: 0;
    border: 1/@myrem solid rgb(146, 146, 146);
    border-radius: 40/@myrem;
    background-color: rgb(245, 245, 245);
    font-size: 30/@myrem;
    color: rgb(255, 120, 0);
  }
}

.hint {
  width: 100%;
  margin-top: -5/@myrem;
  height: 100%;
  line-height: 40/@myrem;
  text-align: center;
  font-size: 28/@myrem;
  color: rgb(144, 144, 144);
}
</style>
