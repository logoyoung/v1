<template>
  <div class="home">
    <!--header-->
    <v-header class="home-title-box">
      <i slot="main-nav" class="nav-button" @click="showMainNav"></i>
      <p slot="title" class="home-title">
        <router-link to="/home/Hot" class="link">热门</router-link>
      </p>
      <p slot="title" class="home-title">
        <router-link to="/home/Follow" class="link">关注</router-link>
      </p>
      <i slot="search" class="search-button">
        <!-- <a data-v-04da74f5="" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng" class="search-button-link"></a> -->
        <a class="search-button-link" @click="toSearch"></a>
      </i>
    </v-header>
    <!--Hot Follow 组件-->
    <keep-alive>
      <router-view></router-view>
    </keep-alive>
    <!--nav组件-->
    <main-nav ref="mainNav"></main-nav>
    <!--footer组件-->
    <download-tip></download-tip>
  </div>
</template>

<script>
import vHeader from '@/components/header/header';
import mainNav from '@/components/nav/main-nav';
import downloadTip from '@/components/downloadTip/download-tip';

export default {
  name: 'home',
  components: { mainNav, vHeader, downloadTip },
  data() {
    return {
      // showNav: false
    }
  },
  methods: {
    // 展示用户导航
    showMainNav() {
      this.$refs.mainNav.show();
    },
    toSearch() {
      this.$router.push({
        path: '/search',
        query: {init: true}
      })
    }
  }
}

</script>

<style lang="less" >
@myrem: 75rem;

.home-title-box {
  width: 100%;
  padding: 0 32/@myrem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: space-between;
  -webkit-align-items: center;
  .nav-button {
    width: 56/@myrem;
    height: 56/@myrem;
    background: url("../../assets/image/icon_menu.png") no-repeat;
    background-size: contain;
    &:active {
      background: url("../../assets/image/icon_menu_h.png") no-repeat;
      background-size: contain;
    }
  }
  .search-button {
    width: 56/@myrem;
    height: 56/@myrem;
    background: url("../../assets/image/icon_search.png") no-repeat;
    background-size: contain;
    &:active {
      background: url("../../assets/image/icon_search_h.png") no-repeat;
      background-size: contain;
    }
    .search-button-link {
      display: block;
      width: 100%;
      height: 100%;
    }
  }
  .home-title {
    width: 168/@myrem;
    text-align: center;
    display: inline-block;
    .link {
      width: 100%;
      display: block;
      position: relative;
      z-index: 5;
      font-size: 40/@myrem;
      color: rgb(40, 40, 40);
    }
    .link.active {
      color: rgb(255, 120, 0);
      &:after {
        content: '';
        width: 0;
        height: 0;
        border-width: 10/@myrem 20/@myrem;
        border-style: solid;
        border-color: #ff7800 transparent transparent transparent;
        position: absolute;
        left: 50%;
        top: 115%;
        margin-left: -20/@myrem;
      }
    }
  }
}

.home-container {
  height: 100%;
  padding-top: 88/@myrem;
  background-color: rgb(245, 245, 245);
  overflow-y: scroll;
}
</style>
