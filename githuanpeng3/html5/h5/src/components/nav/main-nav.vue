<template>
  <transition enter-active-class="animated fast fadeInLeft" leave-active-class="animated fast fadeOutLeft">
    <!-- <transition name="slide"> -->
    <div class="nav-box fast-click" v-show="navShow" @touchstart="slideStart" @touchend="slideEnd" @touchmove.prevent>
      <div class="wrapper">
        <section class="nav-user blur-container" v-show="uid">
          <div class="blur-bg" v-lazy:background-image="userDetail.head" data-type="userface"></div>
          <div class="nav-user-photo">
            <img class="user-photo" v-lazy="userDetail.head" data-type="userface">
          </div>
          <div class="nav-user-name">{{userDetail.nick}}</div>
          <div class="nav-user-asset">
            <div class="asset-info">
              <div class="icon icon-coin"></div>
              <p>{{userDetail.hpcoin}}</p>
            </div>
            <div class="asset-info">
              <div class="icon icon-bean"></div>
              <p>{{userDetail.hpbean}}</p>
            </div>
          </div>
        </section>
        <section class="nav-user blur-container" v-show="!uid">
          <div class="blur-bg" v-lazy:background-image="userDetail.head" data-type="userface"></div>
          <div class="nav-user-photo">
            <img class="user-photo" v-lazy="userDetail.head" data-type="userface">
            <a href="/mobile/h5login/index.html" target="_self" class="login"></a>
          </div>
          <a href="/mobile/h5login/index.html" target="_self" class="login-btn">点击登录</a>
        </section>
        <ul class="nav-main">
          <li class="nav-item">
            <router-link to="/home" class="nav-link">
              <i class="icon icon-home"></i>
              <p class="nav-title">首页</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/classify" class="nav-link">
              <i class="icon icon-catalog"></i>
              <p class="nav-title">分类</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/inpour" class="nav-link" v-show="uid&&encpass">
              <i class="icon icon-inpour"></i>
              <p class="nav-title">充值</p>
            </router-link>
            <a href="javascript:void(0);" class="nav-link" v-show="!uid||!encpass" @click="payMustBeLogin">
              <i class="icon icon-inpour"></i>
              <p class="nav-title">充值</p>
            </a>
          </li>
          <li class="nav-item">
            <!-- <router-link to="/user" class="nav-link"> -->
            <a target="_self" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng" class="nav-link">
              <div class="icon icon-app"></div>
              <p class="nav-title">APP下载</p>
              <!-- </router-link> -->
            </a>
          </li>

        </ul>
        <section class="nav-userout" @click="goLogout" v-show="uid">
          <i class="icon icon-logoff"></i>
          <p class="userout-title">注销</p>
        </section>
        <h2 class="copyright">COPYRIGNT © 2017 HUANPENG.COM</h2>
      </div>
      <div class="back-box" @click="hide"></div>
      <v-dialog ref="dialog" :dialog-type="dialogType" @emitSubmit="logout">
        <p>{{dialogInfo}}</p>
      </v-dialog>
    </div>
  </transition>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import { setCookie, getCookie, deleteCookie } from 'assets/js/common';
import vDialog from 'components/dialog/dialog';
import qs from 'qs';

export default {
  name: 'mainNav',
  data() {
    return {
      navShow: false,
      touchStartX: 0,
      touchEndX: 0,
      userDetail: [],
      dialogInfo: '',
      dialogType: ''
    }
  },
  computed: {
    ...mapGetters([
      'uid',
      'encpass'
    ])
  },
  created() {
    this.commitUserInfo()
  },
  mounted() {
    this.$fastclick('.fast-click');
  },
  methods: {
    loadimg(imgSrc) {
      alert(imgSrc);
    },
    hide() {
      this.navShow = false;
    },
    show() {
      this.navShow = true;
    },
    slideStart(e) {
      this.touchStartX = e.changedTouches[0].screenX;
    },
    slideEnd(e) {
      this.touchEndX = e.changedTouches[0].screenX;
      if (this.touchStartX - this.touchEndX > 30) {
        this.navShow = false;
      }
    },
    // login() {
    //   this.userLogin({ uid: '1860', encpass: '9db06bcff9248837f86d1a6bcf41c9e7' })
    // },
    logout() {
      this.userLogout();
      deleteCookie('_uid');
      deleteCookie('_enc');
      this.userDetail = {};

      // 充值页面注销后跳到首页
      this.$emit('toIndex');
    },
    goLogout() {
      this.dialogInfo = '你是否要退出登录？';
      this.dialogType = 'affirm';
      this.$refs.dialog.show();
    },
    commitUserInfo() {
      // const uid = getCookie('_uid') || '1860';
      // const encpass = getCookie('_enc') || '9db06bcff9248837f86d1a6bcf41c9e7';
      const uid = getCookie('_uid') || '';
      const encpass = getCookie('_enc') || '';

      this.setUid(uid);
      this.setEncpass(encpass);
      this.getUserDetail();
    },
    getUserDetail() {
      if (this.uid && this.encpass) {
        this.$ajax.post('/api/user/info/getUserDetail.php', qs.stringify({
          'uid': this.uid,
          'encpass': this.encpass
        })).then((res) => {
          this.userDetail = res.data.content;
          this.userDetail.hpbean = this.$common.numberFormat(this.userDetail.hpbean, 2);
          this.userDetail.hpcoin = this.$common.numberFormat(this.userDetail.hpcoin, 2);
        })
      }
      else {
        this.userDetail = {};
      }
    },
    payMustBeLogin() {
      this.dialogInfo = '充值需要先登录哦！';
      this.dialogType = 'goLogin';
      this.$refs.dialog.show();
    },
    ...mapMutations({
      setUid: 'SET_UID',
      setEncpass: 'SET_ENCPASS'
    }),
    ...mapActions([
      'userLogin',
      'userLogout'
    ])
  },
  components: { vDialog }
}
</script>

<style lang="less">
@import '~assets/css/common';
@import '~assets/css/animate';

@keyframes mask-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

// @-webkit-keyframes mask-in {
//   from {
//     opacity: 0;
//   }
//   to {
//     opacity: 1;
//   }
// }
.slide-enter-active,
.slide-leave-active {
  transition: all .4s ease;
}

.slide-enter,
.slide-leave-to {
  transform: translateX(-100%);
}

.nav-box {
  position: fixed;
  width: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 100;
  display: flex;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  .back-box {
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    animation: mask-in 1s;
    animation-delay: .3s;
    animation-fill-mode: forwards;
    transform: translate3d(0, 0, 0);
  }
  .wrapper {
    height: 100%;
    flex: 0 0 608/@myrem;
    /* fix bug on ios 8.x */
    -webkit-flex: 0 0 608/@myrem;
    background: rgb(255, 255, 255);
  }
}

.blur-container {
  // background-color: rgba(39, 64, 139, .5);
  // overflow: hidden;
  .blur-bg {
    width: 100%;
    height: 100%;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    filter: blur(35/@myrem);
    transform: translate3d(0, 0, 0);
  }
}

.login-btn {
  position: absolute;
  top: 82/@myrem;
  left: 170/@myrem;
  font-size: 28/@myrem;
  color: rgb(255, 120, 0);
  transform: translate3d(0, 0, 0);
}

.nav-user {
  height: 344/@myrem;
  position: relative;
  color: rgb(255, 255, 255);
  .nav-user-photo {
    position: absolute;
    top: 32/@myrem;
    left: 32/@myrem;
    .user-photo {
      display: block;
      position: absolute;
      width: 120/@myrem;
      height: 120/@myrem;
      border: 4/@myrem solid rgb(232, 234, 235);
      border-radius: 50%;
      transform: translate3d(0, 0, 0);
    }
    .login {
      .user-photo;
    }
  }
  .nav-user-name {
    position: absolute;
    left: 32/@myrem;
    top: 192/@myrem;
    width: 290/@myrem;
    font-size: 32/@myrem;
    color: rgb(255, 255, 255);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  .nav-user-asset {
    position: absolute;
    left: 32/@myrem;
    right: 0;
    bottom: 24/@myrem;
    display: flex;
    /* fix bug on ios 8.x */
    display: -webkit-flex;
    .asset-info {
      display: flex;
      align-items: center;
      /* fix bug on ios 8.x */
      display: -webkit-flex;
      -webkit-align-items: center;
      height: 48/@myrem;
      width: 288/@myrem;
      .icon-coin {
        background-image: url("../../assets/image/icon_huanpeng_coin.png");
      }
      .icon-bean {
        background-image: url("../../assets/image/icon_huanpeng_bean.png");
      }
      p {
        padding-left: 16/@myrem;
        width: 100%;
        font-size: 28/@myrem;
        color: rgb(255, 255, 255);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
  }
}

.nav-main {
  height: 420/@myrem;
  padding: 16/@myrem 0;
  border-bottom: 2/@myrem solid rgb(235, 235, 235);
  .nav-item {
    height: 96/@myrem;
  }
}

.icon {
  width: 48/@myrem;
  height: 48/@myrem;
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
}

.nav-link {
  width: 100%;
  height: 100%;
  padding-left: 32/@myrem;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: flex-start;
  -webkit-align-items: center;
  .icon-home {
    background-image: url("../../assets/image/icon_home.png");
  }
  .icon-catalog {
    background-image: url("../../assets/image/icon_catalog.png");
  }
  .icon-inpour {
    background-image: url("../../assets/image/icon_inpour.png");
  }
  .icon-app {
    background-image: url("../../assets/image/icon_app.png");
  }
  .nav-title {
    padding-left: 64/@myrem;
    font-size: 32/@myrem;
    color: rgb(80, 80, 80);
  }
}

.nav-link.active {
  background-color: rgb(240, 240, 240);
  .icon-home {
    background-image: url("../../assets/image/icon_home_h.png");
  }
  .icon-catalog {
    background-image: url("../../assets/image/icon_catalog_h.png");
  }
  .icon-inpour {
    background-image: url("../../assets/image/icon_inpour_h.png");
  }
  .icon-app {
    background-image: url("../../assets/image/icon_app_h.png");
  }
  .nav-title {
    color: rgb(255, 120, 0);
  }
}

.nav-userout {
  width: 100%;
  height: 96/@myrem;
  padding-left: 32/@myrem;
  padding-top: 16/@myrem;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: flex-start;
  -webkit-align-items: center;
  .icon-logoff {
    background-image: url("../../assets/image/icon_logoff.png");
  }
  .userout-title {
    padding-left: 64/@myrem;
    font-size: 32/@myrem;
    color: rgb(80, 80, 80);
  }
}

.copyright {
  width: 608/@myrem;
  font-size: 20/@myrem;
  color: rgb(146, 146, 146);
  text-align: center;
  position: absolute;
  bottom: 32/@myrem;
}
</style>

