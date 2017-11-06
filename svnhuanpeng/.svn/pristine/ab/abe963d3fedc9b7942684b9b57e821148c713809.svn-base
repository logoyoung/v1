// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/store'
import axios from 'axios'
// import fastclick from 'fastclick'
import common from '../static/lib/common'
import VueLazyload from 'vue-lazyload'


Vue.prototype.$ajax = axios
Vue.prototype.$common = common


// fastclick.attach(document.body)
// Vue.config.productionTip = false

// 图片懒加载
Vue.use(VueLazyload, {
  loading: require('assets/image/index_cate.png'),
  error: 'http://www.huanpeng.com/static/img/userface.png',
  filter: {
      progressive (listener, options) {
          const imgType = listener.el.dataset.type;
          if(imgType === 'live') {
          	listener.loading = require('assets/image/lazyload-2.png');
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
