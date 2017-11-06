<?php
require ('../../include/init.php');
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR.'LiveRoom.class.php';
//include_once $path.'/wxShare.php';
use service\live\LiveService;
use service\room\LiveRoomService;
use service\user\UserDataService;

if(!isMobile())
    header("Location:".WEB_ROOT_URL."room.php?luid={$_GET['u']}");
//jump to mobile
header("Location:".WEB_ROOT_URL."mobile/room/room.html?luid={$_GET['u']}");
exit;

if(isset($_GET['channel']) && $_GET['channel'] == 'wechat_callback'){
    if($_COOKIE['_uid'] && $_COOKIE['_enc'] && UserHelp::getUserEncpass((int)$_COOKIE['_uid']) == $_COOKIE['_enc']){

    }else{
        exit('登录出错 请返回');
    }
}

//查看当前渠道是否为微信分享
if(isset($_GET['channel']) && $_GET['channel'] == 'wechat'){
    UserHelp::$db2 = new DBHelperi_huanpeng();
    //查看来源是否为网站扫码来源
    if($_GET['client_share']!=1){
        //登录结果是否设置
        header("Location:".WEB_PERSONAL_URL."oauth/index.php?channel=wechat&order=login&refUser=".$_GET['suid']."&isWxClient=1&luid={$_GET['u']}");
        exit;
    }
}

$uid  = isset($_GET['u']) ? (int) $_GET['u'] : 0;
if(!$uid)
{
    header("Location:".WEB_ROOT_URL."mobile/");
    exit;
}
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$liveService     = new LiveService();
$liveService->setCaller('api:'.__FILE__.';line:'.__LINE__);
$liveService->setLuid($uid);
$liveRoomService = new LiveRoomService();
$liveRoomService->setCaller('api:'.__FILE__.';line:'.__LINE__);
$liveRoomService->setLuid($uid);
//是否在直播
$isLiving    = $liveService->isLiving();
$live        = $liveService->getLastLive();
$userService = new UserDataService();
$userService->setCaller('api:'.__FILE__.';line:'.__LINE__);
$userService->setUid($uid);
$user   = $userService->getUserInfo();
//观看人数
$viewerCount = $liveRoomService->getLiveUserCountFictitious();
$deg    = $live['orientation'];
$stream = base64_encode($live['stream']);
$liveService->setLivePoster($live['poster']);
$poster = $liveService->getLivePoster();

?>

<!DOCTYPE html>
<html max-width="540">
<head>
    <meta charset="utf-8">
    <title><?php echo $live['gamename'] .'-'. $live['title'];?></title>
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"  name="viewport"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no" />
    <meta content="email=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="x5-fullscreen" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="keywords" content="游戏直播,手机游戏直播,电子竞技直播,热门游戏直播,欢朋直播,王者荣耀直播,CF手游直播">
    <meta name="description" content="欢朋直播是一家致力于为用户带来欢乐的手游直播互动平台。欢朋直播拥有包括热门手游直播，如王者荣耀、球球⼤作战、cf枪战王者、天天酷跑、皇室战争、阴阳师、炉石传说等；以及摄像头直播，如户外直播、星秀、校园、二次元等多元化内容。">
    <meta name="author" content="huanpeng">
    <link rel="stylesheet" href="css/liveRoom.css?v=1.0.4">
    <style>
        * {
            -webkit-touch-callout:none;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color:transparent;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            -webkit-overflow-scrolling : touch;
            -webkit-transform: translate3d(0,0,0);
            -moz-transform: translate3d(0,0,0);
            -ms-transform: translate3d(0,0,0);
            transform: translate3d(0,0,0);
        }
    </style>
    <script>
        /*rem*/
        (function(){
            size();
            window.onresize = function (){
                size();
            };
            function size(){
                var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
                document.documentElement.style.fontSize = winW / 23.4375 +'px';
            }
        })();
    </script>
</head>
<body style="visibility: visible;">
<header id="header">
    <div class="logo">
        <a href="../mobile/">
            <img src="img/logo_H5.png">
        </a>
    </div>
</header>
<div class="liveRoom-container">
    <div class="liveRoom-content">
        <div id="stage">
            <div class="no-live">
                <h1>主播还未开播...</h1>
                <h2>下载欢朋直播,观看更多精彩直播哦</h2>
                <a href="../download.php">
                    <div class="btn_download_nolive">下载欢朋直播</div>
                </a>
            </div>
            <div class="has-live">
                <div class="btn_play_div">
                    <div id="btn_loading" class="rotation"></div>
                    <div id="btn_play"></div>
                </div>
                <div id="poster">
                    <img src="<?php echo $poster;?>">
                </div>
            </div>

        </div>
        <div id="owner">
            <section class="row-1">
                <figure>
                    <img id="avatorimg" src="<?php if($user['pic']) echo $user['pic'];else echo __IMG__.'userface.png' ?>">
                    <figcaption>
                        <h2 id="hostname"><?php echo $user['nick']; ?></h2>
                        <p id="roomname">
                            <span><?php echo $viewerCount; ?></span>人正在观看
                        </p>
                    </figcaption>
                </figure>
                <div class="btn-toApp">
                    <a href="javascript:;" id="openToHP">
                        <button class="goapp">APP观看</button>
                    </a>
                </div>
            </section>
            <section class="row-2">
                <div class="box-1 goapp">
                    <img src="img/icon_chat.png">
                    <span>弹幕</span>
                </div>
                <div class="box-2 goapp">
                    <img src="img/icon_follow.png">
                    <span>关注</span>
                </div>
                <div class="box-3 goapp">
                    <img src="img/icon_share.png">
                    <span>分享</span>
                </div>
            </section>
        </div>
        <div id="hot" style="display: none;">
            <section class="hotLive">
                <img src="img/icon_hot.png">
                <span>热门推荐</span>
            </section>
            <div id="recommendlist">
                <ul id="Live-list">
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="hp_download">
    <img src="img/closeBtn.png" class="close">
    <figure>
        <img src="img/hpLogo.jpg">
        <figcaption>
            <p>下载App领取欢朋豆</p>
            <span>精彩手游直播,尽在欢朋!</span>
        </figcaption>
    </figure>
    <a href="../download.php" class="openApp">下载</a>
</div>

<script type="text/javascript" src="../static/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

    var deg = <?php echo $deg; ?>;
    var poster = <?php echo $live['poster']?'"http://'.$conf['domain-img'].'/'.$live['poster'].'"':"''"; ?>;
    var liveStatus = <?php echo $isLiving; ?>;
    var uid = <?php echo (int)$uid; ?>;

    var ApkUrl = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng&android_schema=huanpeng://#' + <?php echo $live['uid']; ?>;
    $('.goapp').click(function () {
        location.href = ApkUrl;
    });

    !function(Jfn){
        'use strict';
        var live = 100,
            rate = 9/16,
            ua = null;

        var posterRate = 'auto';
        var videoProtype = {
            playsinline:'webkit-playsinline',
            autoplay:'',
            loop:'',
            controls:'controls'
        };
        var videoSty = {
            initTop:'-1000px',
            displayTop:'0px'
        }
        var getValueOrDef = function(v,d){
            if(typeof v =='undefined'||v==null)
                var d = '';
            else
                var d = d;
            if(typeof v =='undefined'||v==null)
                return d;
            else
                return v;
        }
        var videoH = function(){
            var videoWidth = Jfn(window).width();
            var height = videoWidth*rate;
            return height;
        }
        var show = function(selector){
            return Jfn(selector).css('display','block');
        }
        var close = function(selector){
            return Jfn(selector).css('display','none');
        }
        var showPoster = function(deg,poster){
            var deg = getValueOrDef(deg,'');
            var poster = getValueOrDef(poster,'');
            var posterBox = document.querySelector('#poster img');
            if(!deg&&poster){
                posterBox.style.width = posterRate;
            }
            //posterBox.style.width = '100%';
            //posterBox.setAttribute('src',poster);
        }
        var videoBack = function(video, streaming, poster){
            var protype = [].slice.call(videoProtype,0);
            //for(var i in protype)
            //video.setAttribute(protype[i],'');
            video.style.top = videoSty['initTop'];
            video.id = 'videoplayer';
            video.src = streaming;
            video.poster = poster;
            video.setAttribute('webkit-playsinline','');
            video.setAttribute('playsinline','');
            video.setAttribute('controls','');
            video.setAttribute('height','100%');
            video.setAttribute('width','100%');
            var aa = document.getElementById('stage');
            aa.appendChild(video);
        }
        var LiveStreaming = function(luid, poster) {
            this._deferred = null;
            this._poster = poster;
            this._luid = luid;
            this._delaycode = '';
            this._streamingURL = '';
            this._bindHandler('_requestStreamingID', '_requestStreamingIDBack',
                '_validateStreamingBack','_validateStreaming','_validateStreamingBack');
        };
        LiveStreaming.prototype = {
            get: function() {
                if (!this._deferred) {
                    this._deferred = Jfn.Deferred();
                    this._requestStreamingID();
                }
                return this._deferred;
            },
            _requestStreamingID: function() {
                Jfn.ajax({
                    url: '../api/live/getHlsStreamList.php',
                    data:{luid:this._luid},
                    dataType: 'json',
                    type:'post'
                }).always(this._requestStreamingIDBack);
            },
            _requestStreamingIDBack: function(data, textStatus) {

                if (textStatus != 'success') {
//                    console.log(textStatus);
                } else {
                    if (!data.content.stream) {
                        setTimeout(this._requestStreamingID, 1000);
                    } else {
                        var streamList = data.content.streamList;
                        var hlsStream = data.content.stream.split('?');
                        var m3u8 = 'http://' + streamList[0] + '/' +
                            hlsStream[0] + '/playlist.m3u8?'+hlsStream[1];
                        this._streamingURL = m3u8;
                        this._validateStreaming();
                    }
                }
            },
            _validateStreaming: function() {
                //this._streamingURL = this._streamingURL+'?'+new Date().getTime();
                var url = this._streamingURL+'&clean='+new Date().getTime();
                Jfn.ajax({
                    url: url,
                    dataType: 'text'
                }).always(this._validateStreamingBack);
            },

            _validateStreamingBack: function(data, textStatus, jqxhr) {
//                console.log(data.length);
                //检测文件
                //if (textStatus == 'success') {
                if (true) {//测试
                    this._deferred.resolve(this._streamingURL,this._poster);
                } else {
                    setTimeout(this._validateStreaming, 1000);
                }
            },

            _bindHandler: function() {
                var args = [].slice.call(arguments, 0);
                var i = 0;
                for (; i < args.length; i++) {
                    this[args[i]] = this[args[i]].bind(this);
                }
            }
        };

        var xx = new LiveStreaming(uid, poster);
        Jfn.when(xx.get()).done(function(streaming,poster){
            var video = document.createElement('video');
            videoBack(video, streaming, poster);
        });
        Jfn(function(){
            if(liveStatus!=1 || liveStatus == 0){
                show('.no-live');
                return false;
            }else{
                show('.has-live');
                showPoster(deg,poster);
            }
            var playCallBack = function(){

                close('#btn_play');
                var video = document.getElementById('videoplayer');
                if(video){

                    video.style.top = videoSty.displayTop;

                    video.play();
                    close('.has-live');

                    setInterval(function(){
                        if(video.readyState<3){
                            show('.has-live,#btn_loadding,#poster');
                            video.style.top = videoSty.initTop;
                        }else{
                            close('.has-live,#btn_loadding,#poster');
                            video.style.top = videoSty.displayTop;
                        }
                    },1000);
                }
                else{
                    show('#btn_loadding');
                    setTimeout(playCallBack,1000);
                }

            }
            Jfn('#btn_play').bind('click',playCallBack);
        })

    }(jQuery)

</script>
<script type="text/javascript" src="../static/js/common.js"></script>
<script type="text/javascript">
    var openApp = document.querySelector('.btn-toApp');
    var downApp = document.querySelector('.openApp');
    var closeBtn = document.querySelector('.close');
    var hpDownload = document.querySelector('.hp_download');
    openApp.ontouchstart= function(){
        this.style.backgroundColor= '#ff7800';
    };
    openApp.ontouchend = function(){
        this.style.backgroundColor= '#ff9b42';
    };
    downApp.ontouchstart = function(){
        this.style.backgroundColor= '#ff5a00';
    };
    downApp.ontouchend = function(){
        this.style.backgroundColor= '#ff7800';
    };
    closeBtn.onclick = function(){
        hpDownload.style.display = 'none';
    };

    $(function(){
        var rqUrl = $conf.api + 'other/guessYouLike.php';
        var rqData = { size : 6 };
        ajaxRequest({url:rqUrl,data:rqData},function(d){
            var cot = d.list;
            var htmContent = [];
            if(cot == '' ){
                $('#hot').hide();
                return false;
            }
            if(!cot.length){
                return false;
            }
            if(cot.length >=6){
                for(var i = 0; i < 6 ;i++ ){
                    htmContent.push(CreateLi(cot[i]));
                }
            }else if(cot.length < 6){
                for(var i = 0; i < cot.length ;i++ ){
                    htmContent.push(CreateLi(cot[i]));
                }
            }

            if(htmContent == ''){
                $('.liveRoom-container').height('100%');
                return;
            }else{
                $('#Live-list').html(htmContent).append('<div class="more-div"></div>');
                $('#hot').show();
            }

        })
    });
    function CreateLi(obj){
        if( uid == obj.uid){
            return '';
        }
        var roomLink = '../h5share/live.php?u='+obj.uid;
        var tpl = '<li class="liveOne">\
                        <a href="'+roomLink+'">\
                        <div class="div-poster">\
                            <img class="img_poster" src="'+obj.poster+'">\
                            <p class="img_title">'+obj.gameName+'</p>\
                            <div class="img_author">\
                                <img src="'+obj.head+'">\
                            </div>\
                        </div>\
                        <div class="author-desc">\
                            <p class="author-name">'+obj.nick+'</p>\
                            <p class="author-person">'+numberFormat(obj.userCount)+' 人</p>\
                        </div>\
                        <section class="room-name">'+obj.title+'</section>\
                        </a>\
                </li>';
        return tpl;
    }
    $('#openToHP','.row-2>div').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var iframe_href = $(this).attr('href');
        var open_iframe = '<iframe class="open_iframe" src="'+iframe_href+'" style="width: 0;height: 0;border: 0;margin: 0;padding: 0;"></iframe>';
        $('.liveRoom-container').append(open_iframe);
        setTimeout(function () {
            location.href='../download.php';
        },1500);

    });
</script>
</body>
</html>