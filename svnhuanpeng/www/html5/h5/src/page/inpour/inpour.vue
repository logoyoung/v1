<template>
  <div>
    <v-header>
      <a slot="main-nav" class="main-navbtn" @click="showMainNav" v-if="backEvent"></a>
      <a slot="main-nav" class="go-backbtn" @click="backRoom" v-if="!backEvent"></a>
      <p slot="title" class="title">充值</p>
    </v-header>
    <div class="inpour-container fast-click">
      <div class="user-info">
        <p>账号:
          <span class="name">{{username}}</span>
        </p>
        <p>余额:
          <span class="account">{{digitsFormat(coin)}} </span>
        </p>
      </div>
      <div class="inpour-wrap">
        <p class="hint">请选择充值数额</p>
        <ul class="inpour-list">
          <li v-for="price in inpourList" :class="{ 'active': price === selectPrice }" @click="selectPrice = price">
            <p>{{price * 10}} 欢朋币</p>
            <p>￥{{price}}</p>
          </li>
        </ul>
      </div>
      <div class="pay-mode">
        <p class="hint">请选择支付方式</p>
        <ul class="pay-mode-list">
          <li class="list-item" @click="payMode=1" v-if="isInWechat">
            <div class="item-wrap">
              <img src="../../assets/image/icon_Alipay@2x.png" alt="icon_Alipay">
              <div class="item-right">
                <p class="mode-name">支付宝</p>
                <div class="pay-mode-btn" :class="{'check':payMode === 1,'uncheck':payMode===2}"></div>
              </div>
            </div>
          </li>
          <li class="list-item" @click="payMode=2">
            <div class="item-wrap">
              <img src="../../assets/image/icon_WechatPay@2x.png" alt="icon_WechatPay">
              <div class="item-right">
                <p class="mode-name">微信支付</p>
                <div class="pay-mode-btn" :class="{'check':payMode === 2,'uncheck':payMode===1}"></div>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <p class="price">售价:
        <span>{{selectPrice}}</span>
        <span>元</span>
      </p>
      <div class="live-pay-wrap">
        <button class="live-pay-btn" @click="payToHP(payMode, selectPrice)">立即充值</button>
        <div class="pay-mode-btn" :class="{'check':agreement === true,'uncheck':agreement===false}" @click="agreement=!agreement"></div>
        <p>我已阅读并同意
          <a @click="goIframe">《欢朋充值服务协议》</a>
        </p>
      </div>
    </div>
    <main-nav ref="mainNav" @toIndex="goIndex"></main-nav>
  </div>
</template>

<script>
import vHeader from '@/components/header/header';
import mainNav from '@/components/nav/main-nav';
import qs from 'qs';

import fastclick from 'fastclick';
fastclick.attach(document.body, 'fast-click');

export default {
  name: 'inpour',
  data() {
    return {
      username: '',
      coin: 0,
      inpourList: [10, 20, 50, 100, 500, 1000],
      selectPrice: 10,
      inpourAmount: 10,
      payMode: 1,
      AliBtnStyle: 'check',
      WeChectBtnStyle: 'uncheck',
      agreement: true,
      backEvent: true,
      isInWechat: true
    }
  },
  created() {
    // this.$fastclick.attach(document.body,'.fast-click');
    this.getBeanJson();
    if (this.queryHrefStr().roomlink == 'room') {
      this.backEvent = false;
    }

    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
      // this.payMode = 2; 
      // this.isInWechat = false;
      if (!sessionStorage.getItem('_wxCode')) {
        this.refresh();
      } else {
        this.$ajax.post('/api/app/getWxOpenID.php', qs.stringify({
          code: sessionStorage.getItem('_wxCode')
        })).then(res => {
          let status = res.data.status;
          if (status == 1) {
            let openId = res.data.content.opendid;
            localStorage.setItem('_openId', openId);
          } else {
            this.refresh();
          }
        })
      }
    }


    if (this.queryHrefStr().orderid) {
      let uid = this.cookieTodo.getCookie('_uid'),
        encpass = this.cookieTodo.getCookie('_enc'),
        orderid = this.queryHrefStr().orderid;
      if (!uid || !encpass || !orderid) {
        return;
      } else {
        var checkLock = true;
        if (checkLock) {
          var self = this;
          var timer = setInterval(function() {
            if (checkLock) {
              self.$ajax.post('/api/wxpay/status.php', qs.stringify({
                uid: uid,
                encpass: encpass,
                orderid: orderid
              })).then(res => {
                if (res.data.content) {
                  console.log(res.data.content);
                  if (res.data.content.step == 'wait') {
                    checkLock = true;
                  } else if (res.data.content.step == 'finish') {
                    self.coin = res.data.content.hpcoin;
                    alert('充值成功');
                    checkLock = false;
                    return;
                  }
                }
              })
            } else {
              clearInterval(timer);
            }
          }, 1000);
        }

      }

    }
  },
  computed: {
    refresh() {
      return function() {
        let url = location.href;
        let redirectURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx79c0b818ca367bc6&redirect_uri=' + encodeURIComponent(url) + '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        location.href = redirectURL;
      }
    },
    cookieTodo() {
      return this.$common.cookieTodo;
    },
    digitsFormat() {
      return this.$common.digitsFormat;
    },
    queryHrefStr() {
      return function() {
        var b = location.href;
        var c = new Object();
        if (b.indexOf('?') > -1) {
          var str = b.substr(b.indexOf('?') + 1);
          var strs = str.split("&");
          for (var i = 0; i < strs.length; i++) {
            c[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
          }
        }
        return c;
      }
    }
  },
  methods: {
    // 获取用户欢朋币信息
    getBeanJson() {
      this.$ajax.post('/api/user/info/getUserDetail.php', qs.stringify({
        uid: this.cookieTodo.getCookie('_uid') || '',
        encpass: this.cookieTodo.getCookie('_enc') || ''
      })).then((res) => {
        if (res.data.status == 1 && res.data.content.LoginStatus != 0) {
          this.username = res.data.content.nick;
          this.coin = res.data.content.hpcoin || 0;
          // console.log(this.username, this.coin);
        } else {
          this.username = '';
          this.coin = 0;
        }
      })
    },
    // 展示用户导航
    showMainNav() {
      this.$refs.mainNav.show();
    },
    //返回直播间
    backRoom() {
      history.go(-1);
    },
    payToHP(payMode, selectPrice) {
      if (!payMode || !selectPrice) {
        return;
      }
      let uid = this.cookieTodo.getCookie('_uid') || '',
        encpass = this.cookieTodo.getCookie('_enc') || '',
        quantity = selectPrice * 10;
      if (payMode == 1) {
        //支付宝支付
        let reqUrl = 'http://' + document.domain + '/payment/alipay/mwebpay.php';
        let openUrl = reqUrl + '?uid=' + uid + '&encpass=' + encpass + '&quantity=' + quantity;
        location.href = openUrl;

      } else if (payMode == 2) {
        //微信支付
        let productID = 5,
          channel = 'wechat';

        var ua = window.navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == 'micromessenger') {
          //微信里

          var openId = localStorage.getItem('_openId');
          if (!openId || openId == '') {
            this.refresh();
            return;
          }
          this.$ajax.post('/api/wxpay/unifiedorder.php', qs.stringify({
            uid: uid,
            encpass: encpass,
            quantity: quantity,
            productID: productID,
            channel: channel,
            client: 'wxjs',
            openid: openId
          })).then(res => {
            let responseData = res.data;
            if (responseData.status == 1) {
              let resContent = responseData.content;
              function onBridgeReady() {
                WeixinJSBridge.invoke(
                  'getBrandWCPayRequest', {
                    "appId": resContent.appid,     //公众号名称，由商户传入
                    "timeStamp": resContent.timestamp,         //时间戳，自1970年以来的秒数
                    "nonceStr": resContent.noncestr, //随机串
                    "package": resContent.package,
                    "signType": resContent.signType,         //微信签名方式：
                    "paySign": resContent.paySign //微信签名
                  },
                  function(res) {
                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                      // alert('充值成功');
                      return;
                    } else {
                      localStorage.removeItem('_openId');
                      this.refresh();
                      return;
                    }
                  }
                );
              };
              if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                  document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                } else if (document.attachEvent) {
                  document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                  document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
              } else {
                onBridgeReady();
              }
            }
          })

        } else {
          //微信外
          // huanpengShare : 欢朋安卓 
          // 秀场安卓 appAndroid
          // appCloseWebPage appLogin  appSendCommand 秀场 欢朋iOS
          if (window.huanpengShare || window.appAndroid || window.appCloseWebPage || window.appLogin || window.appSendCommand) {
            alert('请在系统浏览器内打开');
            return false;
          }
          this.$ajax.post('/api/wxpay/unifiedorder.php', qs.stringify({
            uid: uid,
            encpass: encpass,
            quantity: quantity,
            productID: productID,
            channel: channel,
            client: 'h5'
          })).then(res => {
            console.log(res);
            if (res.data.status == 1) {
              let resContent = res.data.content;
              let openUrl = resContent.mweb_url;
              location.href = openUrl;
            }

          })
        }

      }


    },
    // 跳转协议页
    goIframe() {
      // const url = 'http://www.huanpeng.com/protocol/mobileLivePay.html';
      this.$router.push({
        path: 'iframe'
      });
    },

    goIndex() {
      this.$router.push({
        path: '/'
      })
    }
  },
  components: { mainNav, vHeader }
}
</script>

<style lang="less" scoped>
@myrem: 75rem;
@color-gray: rgb(146, 146, 146);
@color-black: rgb(40, 40, 40);
@color-orange: rgb(255, 120, 0);
@color-white: rgb(255, 255, 255);
.go-backbtn {
  width: 56/@myrem;
  height: 56/@myrem;
  position: absolute;
  left: 32/@myrem;
  top: 16/@myrem;
  background: url(../../assets/image/icon_back.png) no-repeat;
  background-size: 56/@myrem;
}

.inpour-container {
  padding-top: 88/@myrem;
  height: 100%;
}

.user-info {
  padding: 54/@myrem 32/@myrem;
  display: flex;
  justify-content: flex-start;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: flex-start;
  p {
    width: 343/75rem;
    font-size: 30/@myrem;
    color: @color-gray;
  }
  span {
    display: inline-block;
    vertical-align: top;
    width: 70%;
    padding-left: 20/@myrem;
  }
  .name {
    color: @color-black;
  }
  .account {
    color: @color-orange
  }
}

.inpour-wrap {
  width: 100%;
  padding: 0 32/@myrem 40/@myrem 32/@myrem;
  .hint {
    padding-bottom: 28/@myrem;
    font-size: 28/@myrem;
    color: @color-gray;
  }
  .inpour-list {
    width: 100%;
    height: 224/@myrem;
    display: flex;
    justify-content: space-between;
    align-content: space-between;
    flex-wrap: wrap;
    /* fix bug on ios 8.x */
    display: -webkit-flex;
    -webkit-justify-content: space-between;
    -webkit-align-items: space-between;
    -webkit-flex-wrap: wrap;
    li {
      user-select: none;
      border-radius: 10/@myrem;
      width: 218/@myrem;
      height: 96/@myrem;
      background-color: @color-white;
      display: flex;
      flex-direction: column;
      justify-content: space-around;
      align-items: center;
      /* fix bug on ios 8.x */
      display: -webkit-flex;
      -webkit-flex-direction: column !important;
      -webkit-justify-content: space-around;
      -webkit-align-items: center;
      font-size: 30/@myrem;
      color: @color-black;
      p:nth-of-type(2) {
        font-size: 24/@myrem;
        color: @color-gray;
      }
    }
    .active {
      border: 2/@myrem solid @color-orange;
      color: @color-orange;
      p:nth-of-type(2) {
        color: @color-orange;
      }
    }
  }
}

.pay-mode {
  width: 100%;
  padding: 0 32/@myrem 40/@myrem 32/@myrem;
  .hint {
    padding-bottom: 28/@myrem;
    font-size: 28/@myrem;
    color: @color-gray;
  }
  .pay-mode-list {
    width: 100%;
    .list-item {
      width: 100%;
      height: 104/@myrem;
      display: flex;
      align-items: center;
      /* fix bug on ios 8.x */
      display: -webkit-flex;
      -webkit-align-items: center;
      background-color: @color-white;
      font-size: 32/@myrem;
      &:nth-of-type(1) {
        border-top-left-radius: 15/@myrem;
        border-top-right-radius: 15/@myrem;
        position: relative;
        &:after {
          content: '';
          position: absolute;
          width: 85%;
          height: 1px;
          background: lightgrey;
          bottom: 0;
          right: 0;
        }
      }
      &:nth-of-type(2) {
        border-bottom-left-radius: 15/@myrem;
        border-bottom-right-radius: 15/@myrem;
      }
    }
    .item-wrap {
      width: 100%;
      height: 48/@myrem;
      padding: 0 32/@myrem;
      display: flex;
      /* fix bug on ios 8.x */
      display: -webkit-flex;
      img {
        height: 100%;
        padding-right: 32/@myrem;
      }
      .item-right {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* fix bug on ios 8.x */
        display: -webkit-flex;
        -webkit-justify-content: space-between;
        -webkit-align-items: center;
        .mode-name {
          font-size: 32/@myrem;
          color: @color-black;
        }
      }
    }
  }
}

.pay-mode-btn {
  width: 40/@myrem;
  height: 40/@myrem;
  border-radius: 50%;
}

.uncheck {
  background: url("../../assets/image/LoginAndRegister_button_alreadyRead_normal@2x.png") no-repeat;
  background-size: contain;
  background-position: center;
}

.check {
  background: url("../../assets/image/LoginAndRegister_button_alreadyRead_highlighted@2x.png") no-repeat;
  background-size: contain;
  background-position: center;
}

.price {
  padding-left: 32/@myrem;
  padding-bottom: 30/@myrem;
  font-size: 28/@myrem;
  color: @color-gray;
  span {
    color: @color-orange;
    &:first-child {
      font-size: 40/@myrem;
      padding-right: 5/@myrem;
    }
  }
}

.live-pay-wrap {
  padding: 0 32/@myrem;
  width: 100%;
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  /* fix bug on ios 8.x */
  display: -webkit-flex;
  -webkit-justify-content: center;
  -webkit-flex-wrap: wrap;
  .live-pay-btn {
    width: 100%;
    height: 96/@myrem;
    border: 0;
    background-color: @color-orange;
    border-radius: 48/@myrem;
    font-size: 36/@myrem;
    color: @color-white;
    outline: none;
    &:active {
      background-color: rgb(255, 108, 9);
    }
  }
  .pay-mode-btn {
    position: relative;
    top: 24/@myrem;
  }
  p {
    position: relative;
    top: 30/@myrem;
    left: 16/@myrem;
    font-size: 28/@myrem;
    color: @color-gray;
    a {
      color: @color-orange;
    }
  }
}
</style>
