require('./check-versions')()

var config = require('../config')
if (!process.env.NODE_ENV) {
  process.env.NODE_ENV = JSON.parse(config.dev.env.NODE_ENV)
}

var opn = require('opn')
var path = require('path')
var express = require('express')
var webpack = require('webpack')
var proxyMiddleware = require('http-proxy-middleware')
var webpackConfig = require('./webpack.dev.conf')
var axios = require('axios')
var qs = require('qs')
var bodyParser = require('body-parser')
// var multer = require('multer')
// default port where dev server listens for incoming traffic
var port = process.env.PORT || config.dev.port

// automatically open browser, if not set will be false
var autoOpenBrowser = !!config.dev.autoOpenBrowser
// Define HTTP proxies to your custom API backend
// https://github.com/chimurai/http-proxy-middleware
var proxyTable = config.dev.proxyTable

var app = express()

/**
 * @methods 反向代理
 * @author  fanguang  2017/07/24
 * @update  反向代理已配置在 /config/index.js
 */
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
// app.use(multer());
var apiRouter = express.Router();

apiRouter.post('/app/gameClassifyForApp.php', (req, res) => {
  var url = 'http://www.huanpeng.com/api/app/gameClassifyForApp.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});
apiRouter.post('/app/gameInfoForApp.php', (req, res) => {
  var url = 'http://www.huanpeng.com/api/app/gameInfoForApp.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});
//获取个人信息
apiRouter.post('/other/getCarousel.php', (req, res) => {
  var url = 'http://www.huanpeng.com/api/other/getCarousel.php';
  // console.log(req.query);
  // axios.post(url,req.query,{
  //   header: {
  //     referer: 'http://dev.huanpeng.com',
  //     host: 'dev.huanpeng.com'
  //   }
  // }).then((response)=> {
  //   res.json(response.data);
  // }).catch((e)=> {
  //   console.log(e);
  // })
  console.log(req.body);
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
})
//app首页
apiRouter.post('/app/indexForApp.php', (req, res) => {
  var url = 'http://www.huanpeng.com/api/app/indexForApp.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
})
//关注列表
apiRouter.post('/user/info/followList.php', (req, res) => {
  var url = 'http://www.huanpeng.com/api/user/info/followList.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
})
//获取个人信息
apiRouter.post('/user/info/getUserDetail.php', (req, res) => {
  var url = 'http://dev.huanpeng.com/api/user/info/getUserDetail.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});
//充值
apiRouter.post('/wxpay/unifiedorder.php', (req, res) => {
  var url = 'http://dev.huanpeng.com/api/wxpay/unifiedorder.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});
//getWechatOpenID
apiRouter.post('/app/getWxOpenID.php', (req, res) => {
  var url = 'http://dev.huanpeng.com/api/app/getWxOpenID.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});
//checkPayStatus
apiRouter.post('/wxpay/status.php', (req, res) => {
  var url = 'http://dev.huanpeng.com/api/wxpay/status.php';
  var data = qs.stringify(req.body);
  axios.post(url, data).then((response) => {
    res.json(response.data);
  })
});

app.use('/api', apiRouter);


var compiler = webpack(webpackConfig)

var devMiddleware = require('webpack-dev-middleware')(compiler, {
  publicPath: webpackConfig.output.publicPath,
  quiet: true
})

var hotMiddleware = require('webpack-hot-middleware')(compiler, {
  log: () => {
  }
})
// force page reload when html-webpack-plugin template changes
compiler.plugin('compilation', function (compilation) {
  compilation.plugin('html-webpack-plugin-after-emit', function (data, cb) {
    hotMiddleware.publish({ action: 'reload' })
    cb()
  })
})

// proxy api requests
Object.keys(proxyTable).forEach(function (context) {
  var options = proxyTable[context]
  if (typeof options === 'string') {
    options = { target: options }
  }
  app.use(proxyMiddleware(options.filter || context, options))
})

// handle fallback for HTML5 history API
app.use(require('connect-history-api-fallback')())

// serve webpack bundle output
app.use(devMiddleware)

// enable hot-reload and state-preserving
// compilation error display
app.use(hotMiddleware)

// serve pure static assets
var staticPath = path.posix.join(config.dev.assetsPublicPath, config.dev.assetsSubDirectory)
app.use(staticPath, express.static('./static'))

var uri = 'http://localhost:' + port

var _resolve
var readyPromise = new Promise(resolve => {
  _resolve = resolve
})

console.log('> Starting dev server...')
devMiddleware.waitUntilValid(() => {
  console.log('> Listening at ' + uri + '\n')
  // when env is testing, don't need open it
  if (autoOpenBrowser && process.env.NODE_ENV !== 'testing') {
    opn(uri)
  }
  _resolve()
})

var server = app.listen(port)

module.exports = {
  ready: readyPromise,
  close: () => {
    server.close()
  }
}
