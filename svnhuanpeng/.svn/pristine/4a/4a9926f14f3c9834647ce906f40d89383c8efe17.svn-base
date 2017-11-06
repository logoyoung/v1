<template>
  <div class="home-container">
    <v-swiper class="swiper-container" v-if="swiperList.length">
      <div class="swiper-wrapper" ref="swiperItem">
        <div class="swiper-slide" v-for="(item,index) in swiperList">
          <!-- <router-link :to="'/webview/' + item.url.replace(/\//g,'*hp*')" class="web-link">
                                  <img :src="item.poster" alt=""/>
                                </router-link> -->
          <a class="web-link" @click.stop="goWebview(item)">
            <img :src="item.poster" alt="">
          </a>
        </div>
      </div>
    </v-swiper>
    <div class="live-container" v-for="(item,index) in hotList" :key=index>
      <section class="live-info">
        <div class="game-info">
          <img v-lazy="item.icon" alt="" />
          <span>{{item.gameName}}</span>
        </div>
        <a href="javascript:void(0)" v-if="item.liveList.length>=4&&index>0" @click="goMore(item.gameId,item.gameName)">
          <p class="more">更多</p>
        </a>
      </section>
      <video-list :video-list="item.liveList" :hasGameTitle="item.gameName === '热门推荐'"></video-list>
    </div>
  </div>
</template>

<script>
import vSwiper from 'components/swiper/swiper';
import videoList from 'components/videoList/videoList'
import qs from 'qs';

const ERR_OK = '1';
export default {
  name: 'home_Hot',
  data() {
    return {
      seen: true,
      swiperList: [],
      hotList: [],
    }
  },
  computed: {
    numberFormat() {
      return this.$common.numberFormat;
    }
  },
  created() {
    this.getHotData();
    this.getHotListData();
    // this.getTest();
  },
  mounted() {

  },
  methods: {
    getHotData() {
      const self = this;
      this.$ajax.post('/api/other/getCarousel.php', qs.stringify({
        client: '3'
      })).then((res) => {
        const response = res.data;
        if (response.status == ERR_OK) {
          self.swiperList = response.content.list;
          //            console.log('sp', self.swiperList);
        }
      });
    },
    getHotListData() {
      this.$ajax.post('/api/app/indexForApp.php', qs.stringify({
        uid: '1860',
        encpass: '9db06bcff9248837f86d1a6bcf41c9e7'
      })).then((res) => {
        this.hotList = res.data.content.list;
        console.log('res hotListJson: ', this.hotList);
      });
    },
    goMore(id, gameName) {
      this.$router.push({
        path: '/zone',
        query: { id, gameName }
      })
    },
    goWebview(item) {
      console.log(item);
      let showType = item.showType;
      let url = item.url;
      let title = item.title;
      this.$router.push({
        path: '/webview',
        query: { showType, url, title }
      })
    },
  },

  components: { vSwiper, 'video-list': videoList }
}

</script>

<style lang="less" scoped>
body {
  height: auto;
}

@myrem: 75rem;

.swiper-container {
  // margin-top: 88/@myrem;
}

.live-container {
  margin-top: -20/@myrem;
}

.live-info {
  width: 100%;
  height: 80/@myrem;
  padding: 0 32/@myrem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: space-between;
  -webkit-align-items: center;
  .game-info {
    width: auto;
    height: 100%;
    display: flex;
    align-items: center;
    /* fix bug on ios 8.x */
    display: -webkit-flex;
    -webkit-align-items: center;
    img {
      display: block;
      height: 56/@myrem;
    }
    span {
      display: block;
      color: rgb(40, 40, 40);
      font-size: 32/@myrem;
      padding-left: 16/@myrem;
    }
  }
  a {
    display: block;
    .more {
      font-size: 24/@myrem;
      color: rgb(146, 146, 146);
      &::after {
        content: '';
        width: 10/@myrem;
        height: 10/@myrem;
        border-top: 3/@myrem solid rgb(204, 204, 204);
        border-right: 3/@myrem solid rgb(204, 204, 204);
        display: inline-block;
        margin-left: 3/@myrem;
        margin-bottom: 3/@myrem;
        transform: rotate(45deg);
      }
    }
  }
}
</style>
