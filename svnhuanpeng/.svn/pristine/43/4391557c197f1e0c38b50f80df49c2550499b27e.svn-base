/**
 * Created by logoyoung on 16/11/15.
 */
/**
 * 下载页
 * */
define('download',['jquery','UA'],function($,UA){
    (function () {
        if(UA.versions.mobile){
            var appDown = {
                iphone:'https://itunes.apple.com/us/app/huan-peng/id1191399310?ls=1&mt=8',
                android:'api/app/download.php'
            };
            var appHref = UA.versions.android?appDown.android:appDown.iphone;
            /*$('#android-ios-download').bind('click',function () {
                document.location.href = appHref;
            })*/
            var weixinDown = {
            		iphone:'http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng',
            		//android:'http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng'
                    android:'http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng'
            }
            var otherDown = {
            		iphone:'https://itunes.apple.com/us/app/huan-peng/id1191399310?ls=1&mt=8',
            		android:'api/app/download.php'
            }
            var downurl = UA.versions.weixin?weixinDown:otherDown;
            var appdownurl = UA.versions.iPhone?downurl['iphone']:downurl['android'];
            //$('#app-down-btn-mobile').attr('href',appdownurl);alert(appdownurl);
            if(UA.versions.weixin)
                location.href = appdownurl;
            else {
                $('#android-ios-download').on('click', function (e) {
                    e.preventDefault();
                    location.href = appdownurl;
                })
                $('#android-ios-download').on('touchstart', function (e) {
                    e.preventDefault(e);
                    location.href = appdownurl;
                })
                $('#android-ios-download').on('touchend', function (e) {
                    e.preventDefault();
                    location.href = appdownurl;
                })
            }
            if(UA.versions.iPhone)
                $('#android-ios-download').text('立即下载');
            /*if(UA.versions.weixin)
                $('.h5-mask').addClass('show');
            $('.known').bind('click',function () {
                $('.h5-mask').removeClass('show');
            })*/
            return;
        }
        var DOWN = {};
        DOWN.M = function () {
            var M = {};
            M.data = {};
            M.conf = {
                'deviceType':'pc',
                'baseUrl':'',
                'pcUrl':'',
                'iosUrl':'',
                'android':''
            };

            M.method = {
            }
            return {
                get:function (param) {
                    var params = document.location.href.split('?');
                    if(!params[1]) return '';
                    params = params[1].split('=');
                    var $get = {};
                    for(var k=0;k<params.length-1;k++)
                    $get[params[k]] =params[k+1];
                    var args = [].slice.call(arguments);
                    if(param) return $get[param]||'';
                    else return $get;
                    }
            };
        };
        var dom = {
            appClass:'.nav-app',
            pcClass:'.nav-pc',
            appBlock:'.content-app',
            pcBlock:'.content-pc',
            sliderOne:'.slider-one',
            circleOne:'.circle-one',
            sliderList:'.slider-list',
            step:664
        }
        DOWN.V = function () {
            var M = DOWN.M;
            var V = {
                switch:function(platform){
                    //todo 切换平台
                },
                showApp:function(){
                    $(dom['appBlock']).css('display','block');
                    $(dom['pcBlock']).css('display','none');
                    $(dom['appClass']).addClass('open');
                    $(dom['pcClass']).removeClass('open');
                },
                showPC:function(){
                    $(dom['appBlock']).css('display','none');
                    $(dom['pcBlock']).css('display','block');
                    $(dom['appClass']).removeClass('open');
                    $(dom['pcClass']).addClass('open');
                },
                sliderTabShow:function(order){
                    $(dom['sliderOne']).removeClass('show');
                    var lock = [].slice.call(arguments,1);
                    var thisBind = this;
                    if(lock[0])//被锁住立即显示
                        thisBind.circleTabShow(order);
                    else//过渡显示
                        setTimeout(function(){
                            thisBind.circleTabShow(order);
                        },1000)
                },
                circleTabShow:function(order){
                    $(dom['sliderList']).css('top','-'+dom['step']*order+'px');
                    $(dom['sliderOne']).eq(order).addClass('show');
                    $(dom['circleOne']).removeClass('show');
                    $(dom['circleOne']).eq(order).addClass('show');
                }
            }
            return V;
        }
        DOWN.C = function () {
            var M = DOWN.M();
            var V = DOWN.V();
            var lock = false;
            var timer = -1;
            var count = 5;
            var C = {
                init:function () {
                    this.typeTab();
                    this.eventsRegister();
                    this.sliderTimer(timer);
                    this.circleEvent();
                },
                eventsRegister:function () {
                    $(dom['appClass']).bind('click',function(){V.showApp()});
                    $(dom['pcClass']).bind('click',function(){V.showPC()});
                },
                sliderTimer:function(){
                    if(!lock) {
                        timer++;
                        timer = timer % count;
                        V.sliderTabShow(timer);
                    }
                    setTimeout(arguments.callee,5000);
                },
                circleEvent:function () {
                    //var o = this;
                    $(dom['circleOne']).bind('mouseover',function(){
                        lock = true;
                        timer = parseInt($(this).index());
                        V.sliderTabShow(timer,lock);//console.log(timer);
                    })
                    $(dom['circleOne']).bind('mouseout',function(){
                        lock = false;
                    })
                },
                typeTab:function () {
                    var reftype = M.get('reftype');
                    if(reftype=='pc') V.showPC();
                    else V.showApp();
                }
            }
            C.init();
        }
        DOWN.C();
    }())
})
