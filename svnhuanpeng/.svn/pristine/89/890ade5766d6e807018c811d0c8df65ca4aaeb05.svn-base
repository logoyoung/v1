import Vue from 'vue'
import Router from 'vue-router'

// import Home from '@/page/home/home'
// import Hot  from '@/page/home/Hot'
// import Follow from '@/page/home/Follow'


// import Classify from '@/page/classify/classify'
// import Zone from '@/page/zone/zone'
// import Inpour from '@/page/inpour/inpour'
// import User from '@/page/user/user'

// import WebView from '@/page/webview/webview'

// 模块按需加载
const Home = (resolve) => {
  import('page/home/home').then((module)=> {
    resolve(module)
  })
}
const Hot = (resolve) => {
  import('page/home/hot').then((module)=> {
    resolve(module)
  })
}
const Follow = (resolve) => {
  import('page/home/Follow').then((module)=> {
    resolve(module)
  })
}
const Classify = (resolve) => {
  import('page/classify/classify').then((module)=> {
    resolve(module)
  })
}
const Zone = (resolve) => {
  import('page/zone/zone').then((module) => {
    resolve(module)
  })
}
const Inpour = (resolve) => {
  import('page/inpour/inpour').then((module) => {
    resolve(module)
  })
}
const WebView = (resolve) => {
  import('page/webview/webview').then((module)=> {
    resolve(module)
  })
}
const Iframe = (resolve) => {
  import('page/iframe/iframe').then((module)=> {
    resolve(module)
  })
}


Vue.use(Router)

export default new Router({
  linkActiveClass:'active',
  routes: [
    {
      path: '*',
      redirect: '/home/Hot'
    },
    {
      path: '/home',
      name: 'home',
      component: Home,
      children:[
        { path: '/home/Hot', name: 'home_Hot', component: Hot},
        { path: '/home/Follow', name: 'home_Follow', component: Follow}
      ],
      redirect:'/home/Hot'
    },
    {
      path: '/classify',
      name: 'classify',
      component: Classify
    },
    {
      path: '/zone',
      name: 'zone',
      component: Zone
    },
    {
      path: '/inpour',
      name: 'inpour',
      component: Inpour
    },
    {
      path: '/webview',
      name: 'webview',
      component: WebView
    },
    {
      path: '/iframe',
      name: 'iframe',
      component: Iframe
    }
  ]
})
