/**
 * Created by hantong on 16/11/1.
 */
var path = require('path');

module.exports = {
	entry:{
		userHead:'./vue/UserManage/userHead/index.js',
		liveTitle:'./vue/UserManage/liveTitle/index.js',
		userNick:'./vue/UserManage/userNick/index.js',
		newsUnpublish:'./vue/newsManage/unpub/index.js',
		newsPublish:  './vue/newsManage/pub/index.js',
		newsRecommend:'./vue/newsManage/recommend/index.js',
		liveRecommend:'./vue/anchorRecommend/allAnchor/index.js',
	},
	output:{
		path:'./common/admin/pages/scripts/',
		filename:"[name].js"
	},
	externals:{
		jquery:'window.$',
		Vue:'Vue'
	},
	module:{
		loaders:[
			{
				// test:path.join(__dirname, 'src'),
				test:/\.js$/,
				loader:'babel-loader',
				query:{
					presets:['es2015']
				}
			},
			{
				test:/\.vue$/,
				// exclude:/node_modules/,
				loader:'vue-loader'
			}
		]
	}
}