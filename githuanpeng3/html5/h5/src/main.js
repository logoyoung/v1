// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import 'babel-polyfill'
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/store'
import axios from 'axios'
import fastclick from 'fastclick'
import common from '../static/lib/common'
import VueLazyload from 'vue-lazyload'

Vue.prototype.$ajax = axios
Vue.prototype.$common = common
Vue.prototype.$fastclick = (className) => {
  let fastClickElementList = document.querySelectorAll(className);
  for (let element of fastClickElementList) {
    fastclick.attach(element);
  }
}

// fastclick.attach(document.body)
// Vue.config.productionTip = false

// 图片懒加载
Vue.use(VueLazyload, {
  filter: {
    progressive(listener, options) {
      const imgType = listener.el.dataset.type;
      if (imgType === 'live') {
        listener.error = listener.loading = require('assets/image/lazyload-2.png');
      }
      else if (imgType === 'classify') {
        listener.error = listener.loading = require('assets/image/index_cate.png');
      }
      else if (imgType === 'userface') {
        listener.error = listener.loading = require('assets/image/userface.png');
      }
    }
  },
  adapter: {
    /* 
      用户发起竖屏直播，在显示占位图的时候设置 margin:0，确保占位图正常显示。
    */
    loading(listener) {
      if (listener.el.dataset.type === 'live' && listener.el.className === 'vertical') {
        listener.el.setAttribute('style', 'margin:0;');
      }
    },
    error(listener) {
      if (listener.el.dataset.type === 'live' && listener.el.className === 'vertical') {
        listener.el.setAttribute('style', 'margin:0;');
      }
    },
    loaded(listener) {
      if (listener.el.dataset.type === 'live' && listener.el.className === 'vertical') {
        listener.el.removeAttribute('style');
      }
    }
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})
