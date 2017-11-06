/**
 * Created by logoyoung on 16/11/9.
 */
/**==========requirejs配置================*/
require.config({
    baseUrl:'static/requirejs',
    paths:{
        jquery:'./lib/jquery.js?v1.0.2',
        my:'./lib/my',
        my2:'./lib/my',
        UA:'./lib/UA',
        download:'./app/download'
        //ko:'lib/ko',
        //json2:'lib/json2'
    },
    shim:{}
});
//console.log('test');

/**=============模块化配置================*/

/**
 * 浏览器兼容及其他差异化处理模块
 * */
var M_DIFF = [];

/**
 * 第三方类库
 * */
var M_LIBS = [
    'jquery',   //jquery类库
    'ko'        //knockout类库
];

/**
 * 通用模块配置
 * */
var M_UTILS = [
    'common',//common模块，系统变量、系统环境配置
];

/**
 * 首页模块配置
 * */
var M_HOME = [
    'home'
];

/**
 * 直播间模块配置
 * */
var M_ROOM = [
    'room'
];

/**
 * 播放器模块配置
 * */
var M_PLAYER = [];


/**=====================================*/
require(
    //动态加载
    (function(){
        var module = ['download'];
        /*if('__proto__' in {})
         module.push('my');
         else
         module.push('my2');

         module.push('common');
         //console.log(module);*/
        //module.push(M_LIBS);
        //module.push(M_UTILS);
       /* module.add = function(m){
            return [].concat.apply(this,m);
        }*/
        //module.add(M_LIBS);
        //module.concat(M_LIBS);
        //console.log(M_LIBS);
        return module;
    }()),
    function($){
        //todo
       // $('body').append('<h3>加载完毕</h3><br><h4>========================</h4>');
        //console.log($conf);
    })

/**=====直接调用方式========*/
require(['',''],function () {

})