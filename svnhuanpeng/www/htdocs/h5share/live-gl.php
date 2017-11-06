<?php
require ('../init.php');
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR.'LiveRoom.class.php';
include 'wxShare.php';
session_start();
if(!isMobile())
    header("Location:".WEB_ROOT_URL."room.php?luid={$_GET['u']}");

if($_GET['channel'] == 'wechat_callback'){
    if($_COOKIE['_uid'] && $_COOKIE['_enc'] && UserHelp::getUserEncpass((int)$_COOKIE['_uid']) == $_COOKIE['_enc']){

    }else{
        exit('登录出错 请返回');
    }
}

//查看当前渠道是否为微信分享
if($_GET['channel'] == 'wechat'){
    UserHelp::$db2 = new DBHelperi_huanpeng();
    //查看来源是否为网站扫码来源
    if($_GET['client_share']!=1){
        //登录结果是否设置
        header("Location:".WEB_PERSONAL_URL."oauth/index.php?channel=wechat&order=login&refUser=".$_GET['suid']."&isWxClient=1&luid={$_GET['u']}");
        exit;
    }
}

define('RECOMMEND', 6);
function getLiveByUser($db, $uid)
{
    $sql = "select * from live where uid=$uid order by ctime desc limit 1";
    $res = $db->query($sql);
    if (! $res) {
        $t = 'Query Error (' . $db->errno() . ') ' . $db->errstr();
        mylog($t);
        return false;
    }
    if (! $res->num_rows)
        return false;
    $row = $res->fetch_assoc();
    return $row;
}
function getViwerCount($luid, $db){
    /*$sql = "select count(`uid`) from liveroom where luid=$luid";
    $res = $db->query($sql);
    $row = $res->fetch_row();
    return $row[0];*/
    $room = new LiveRoom($luid, $db);
    return  $room->getLiveUserCountByLuid($luid);
}
function getNickByUid($uid, $db){
    $sql = "select `nick` from `userstatic` where uid=$uid";
    $res = $db->query($sql);
    $row = $res->fetch_row();
    return $row[0];
}
function getUserByUid($uid, $db){
    $sql = "select * from `userstatic` where uid=$uid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    return $row;
}
function getRecommendLive($db)
{
    $recommend = array();
    $sql = "select * from live where status=" . LIVE . " group by uid order by ctime limit " . RECOMMEND;
    $res = $db->query($sql);
    if (! $res) {
        $t = 'Query Error (' . $db->errno() . ') ' . $db->errstr();
        mylog($t);
        return false;
    }
    //if (! $res->num_rows)
    //return false;
    while ($row = $res->fetch_assoc()) {
        $user = getUserByUid($row['uid'], $db);
        $row['nick'] = $user['nick'];
        $row['viewerCount'] = getViwerCount($row['uid'], $db);
        $recommend[] = $row;
    }
    unset($user);
    if (! DEBUG)
        return $recommend;
    if (! ($num = RECOMMEND - count($recommend)))
        return $recommend;
    $sql = "select * from live where status=" . LIVE_VIDEO . " group by uid order by ctime limit " . $num;
    $res = $db->query($sql);
    if (! $res) {
        $t = 'Query Error (' . $db->errno() . ') ' . $db->errstr();
        mylog($t);
        return false;
    }
    if (! $res->num_rows)
        return $recommend;
    while ($row = $res->fetch_assoc()) {
        $user = getUserByUid($row['uid'], $db);
        $row['nick'] = $user['nick'];
        $row['viewerCount'] = getViwerCount($row['uid'], $db);
        $recommend[] = $row;
    }
    unset($user);
    return $recommend;
}
$uid = isset($_GET['u']) ? (int) $_GET['u'] : 0;
if (! $uid)
    ;//msgexit('-1015');

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();
$live = getLiveByUser($db, $uid);
if (! $live)
    ;//msgexit('-1016');
//$deg = (4 - $live['orientation']) * 90;
$user = getUserByUid($uid, $db);
$viewerCount = getViwerCount($uid, $db);
$deg = $live['orientation'];
$stream = base64_encode($live['stream']);
$recomend = getRecommendLive($db);
?>
<!DOCTYPE html>
<html lang="en" data-dpr="3" max-width="540">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo $live['gamename'] .'-'. $live['title'];?></title>
</head>
<style>
    a{
        color: inherit;
    }
    #stage {
        width: 100%; background: #333; margin: 0; padding: 0;  position:relative;height: 0px;padding-bottom: 56%;overflow: hidden;
    }
    #poster{
        width:100%;
        height:100%;
        position:absolute;
        background:#000;
    }
    #poster img{
        width:100%;
        height:100%;
        overflow:hidden;
    }
    video {
        position: absolute; top: -1000px;left:0px;
        /* -moz-transform: rotate(90deg);
        -webkit-transform:rotate(90deg);
        -o-transform:rotate(90deg);
       -ms-transform:rotate(90deg);
      transform:rotate(90deg); */
        width:100%;
        height:100%;
    }
    /*
    #stage { display: inline-block; width: 100%;  position: relative; vertical-align: middle;margin: 0px; }
    #stage:before { content: ""; display: inline-block; padding-bottom: 56.2%; width: .1px; vertical-align: middle; }
    #stage .div-poster { position: absolute; width: 100%;height:100%;display: inline-block;vertical-align: middle; }
    */
    a:active,a:hover {
        outline: 0
    }

    a,a:visited {
        text-decoration: none
    }

    a {
        -webkit-tap-highlight-color: rgba(255, 0, 0, 0);
    }

    #videoplayer {
        background: black;
    }

    .btn {
        -webkit-tap-highlight-color: rgba(255, 0, 0, 0);
    }

    #func,#header,#owner {
        background-color: #fff
    }

    #func .ico,#owner .count {
        background-repeat: no-repeat
    }

    #func .app,#header .func {
        right: .5rem; position: absolute
    }

    #chat,#func .app,#header .func {
        position: absolute
    }

    body {
        margin: 0; padding: 0
    }

    body,input,textarea {
        font: 12px STHeiti, "Microsoft YaHei", SimSun, STSong, "\5b8b\4f53", Arial, Helvetica, sans-serif
    }

    body, html {
        width: 100%;
        overflow: auto;
        min-height: 100%;
        background: white;
    }

    * {
        box-sizing: border-box
    }

    #header {
        width: 100%; height: 3rem; position: relative;
    }

    #header .logo {
        float: left;
        width: 5rem;
        height: 1.463rem;
        margin: .75rem 0 0 .5rem;
    }

    #header .logo img {
        width: 100%; height: 100%
    }

    #header .func {
        height: 1rem; top: .5rem
    }

    #header .func a {
        width: 1rem; height: 1rem; display: inline-block
    }

    #owner {
        width: 100%; height: 2.35rem; padding: .225rem 0;
    }

    #owner .avatar {
        float: left; width: 4rem; height: 4rem; border-radius: 4rem; border: .075rem solid #eee; margin-left: .5rem; overflow: hidden
    }

    #owner .avatar img {
        width: 100%; height: 100%
    }

    #owner .info {
        float: left; width: 7.5rem; margin-left: .3rem;margin-top:1rem;
    }

    #owner .info h2 {
        margin: 0; padding: .1rem; height: 1.1rem; line-height: .8rem; font-size: 1rem; color: #333; font-weight: 400;overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #owner .count,#owner .info p {
        height: .75rem; line-height: .75rem;padding:.1rem;
    }

    #owner .info p {
        margin: .3rem 0; padding: 0; font-size: .5rem; color: #ababab; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;height:2rem;line-height:1rem;
    }
    #owner .btn-appdown{
        float:right;
        background:#ff7800;
        border-radius:.3rem;
        color:#fff;
        margin:1rem;
        text-align:center;
        padding:.6rem;
        cursor:pointer;
    }
    #owner .count {
        font-size: .55rem; margin-top: 1.1rem; padding-left: .7rem; padding-right: .5rem; color: #009bfa; float: right; background-image: url("http://i6.pdim.gs/4f7d203d6248facb5fabe4f8203d978f.png"); background-position: left center; background-size: auto 67%
    }

    #func {
        width: 100%; height: 2.075rem; padding: .575rem .5rem 2rem; border-bottom: 1px solid #e2e2e2; display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; position: relative
    }

    #func .ico {
        width: 33.3%;
        cursor: default;
        height: 1.3rem;
        background-size: 1.3rem;
        padding-left: 1.4rem;
        line-height: 1.315rem;
        font-size: .8rem;
        margin-left: 1rem;
        color: #666;
    }

    #func .ico.bamboo {
        background-image: url(img/icon/icon_comment.png);
    }

    #func .ico.chat {
        background-image: url(img/icon/icon_follow.png);
    }

    #func .ico.gift {
        background-image: url(img/icon/icon_share.png);
    }
    #func .ico .txt{
        display:inline-block;
    }
    #func .app {
        cursor: default; width: 3.375rem; height: 1.25rem; border-radius: 1.25rem; background-color: #009bfa; color: #fff; font-size: .55rem; line-height: 1.25rem; text-align: center; top: .4rem
    }



    * {
        margin: 0px; padding: 0px;
    }

    .clearfix:after,.clearfix:before {
        content: ""; display: table;
    }

    .clearfix:after {
        display: block; clear: both;
    }

    .clearfix {
        zoom: 1;
    }

    .fl {
        float: left;
    }

    .fr {
        float: right;
    }
    #ban{
        width:100%;
        height:1.5rem;
        background:#f3f3f3;
    }
    #recommend {
        background-color: #fff; border-top: solid #e2e2e2 1px; overflow: hidden;height: 100%;margin-bottom: 4rem;
    }

    #recommend .recommendtitle {
        height: 2rem;
        line-height: 3rem;
        font-size: 1rem;
        color: #333333;
        padding-left: .25rem;
    }

    .img_recommend_Wrap {

    }

    .img_recommend {
        position: relative; overflow: hidden;
    }


    .img-block .img_mask {
        position: absolute; bottom: 1.4rem; height: .9rem; background-color: #000; opacity: .6;
    }

    .img-block .img_title {
        position: absolute; bottom: 0px; box-sizing: border-box; width: 100%; height: .9rem; line-height: .9rem; color: #fff; padding-left: .1rem; padding-right: .4rem; font-size: .8rem; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
    }

    .bot_title {
        width: 100%; height: 1.4rem; line-height: 1.4rem; color: #333333; font-size: .5rem;position: absolute;bottom: -30px;
    }

    .main_title {
        width: 60%; height: 1.4rem; padding-left: .1rem; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;font-size: .8rem;
    }

    .people_number {
        max-width: 30%;
        background: url(img/icon/icon_viewer.png) left no-repeat;
        background-size: 1.3rem;
        float: right;
        padding-left: 1.2rem;
        box-sizing: border-box;
        color: #ff7800;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        margin-right: 1rem;
        font-size: .8rem;
    }





    .btn_play_div{
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 999;
    }
    #btn_play,#btn_loadding{
        position: absolute;
        display: block;
        width:50px;
        height:50px;
        background: url(../static/img/src/play_btn.png) center no-repeat;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
    #btn_loadding{
        color:#fff;
        font-size:.3rem;
        text-align:center;
        line-height:50px;
        display:none;
        background: url(img/loadding.png);
    }
    @-webkit-keyframes rotation{
        from {-webkit-transform: rotate(0deg);}
        to {-webkit-transform: rotate(360deg);}
    }

    .rotation{
        -webkit-transform: rotate(360deg);
        animation: rotation 1.5s linear infinite;
        -moz-animation: rotation 1.5s linear infinite;
        -webkit-animation: rotation 1.5s linear infinite;
        -o-animation: rotation 1.5s linear infinite;
    }
    .no-live,.has-live{
        display: none;
    }
    .no-live h1{
        line-height: 5rem;
        color:#fff;
    }
    .no-live h2{
        color:#fff;
    }
    .no-live .btn_download_nolive{
        width: 10rem;
        height: 2rem;
        line-height: 2rem;
        margin-top: 2rem;
        background: #ff7800;
        text-align: center;
        color: #fff;
        border-radius: .3rem;
    }
    #block-bottom-download{
        display:block;
        width: 100%;
        height: 4rem;
        bottom: 0px;
        position: fixed;
        margin:0;
        padding: 0;
        left:0;
        z-index: 99999;
        background:rgba(0,0,0,.9);
    }
    #block-bottom-download .x{
        float: left;
        width: 2rem;
        height: 4rem;
        background: url(img/icon/icon_delete.png) center no-repeat;
    }
    #block-bottom-download .bottom-logo{
        float: left;
        height: 3rem;
        width: 3rem;
        margin: .5rem;
    }
    #block-bottom-download .bottom-logo img{
        width: 100%;
        height: 100%;
    }
    #block-bottom-download .bottom-txt{
        float: left;
        color: #fff;
        padding: 1rem 0rem;
    }
    #block-bottom-download .bottom-txt h2{
        color: #ff7800;
        font-size: .9rem;
    }
    #block-bottom-download .bottom-txt p{
        font-size: .7rem;
        color:#ccc;
    }
    #block-bottom-download .btn-app{
        float: right;
        background: #ff7800;
        border-radius: .3rem;
        color: #fff;
        margin: 1rem .2rem;
        text-align: center;
        padding: .6rem;
        cursor: pointer;
    }
    .liveOne { display: inline-block; width: 50%;  position: relative; vertical-align: middle;margin: 1rem 0rem 2rem 0rem; }
    .liveOne:before { content: ""; display: inline-block; padding-bottom: 56.2%; width: .1px; vertical-align: middle; }
    .liveOne .div-poster { position: absolute; width: 100%;height:100%;display: inline-block;vertical-align: middle; }
    .liveOne .div-poster .img-block{    margin: .5em;height: 100%;border-radius: .5em;overflow: hidden;background: #6b6b6b;}
    .liveOne .div-poster .img-block img{width: 100%;height: 100%;overflow: hidden;}
    .angle_hp{height: 100%;}
</style>
<body>
<script type="text/javascript" src="../static/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

    var deg = <?php echo $deg; ?>;
    var poster = <?php echo $live['poster']?'"http://'.$conf['domain-img'].'/'.$live['poster'].'"':"''"; ?>;
    var liveStatus = <?php echo $live['status']; ?>;
    var uid = <?php echo (int)$uid; ?>;
    !function(Jfn){
        'use strict';
        var live = 100,
            rate = 9/16,
            ua = null;

        var posterRate = '31.6%';
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
            if(!deg&&poster)
                posterBox.style.width = posterRate;
            else
                posterBox.style.width = '100%';
            posterBox.setAttribute('src',poster);
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
            //video.setAttribute('x5-video-player-type','h5');
            //video.setAttribute('x-webkit-airplay','');
            //video.setAttribute('x5-video-player-fullscreen','');
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
                    type:'post',
                }).always(this._requestStreamingIDBack);
            },
            _requestStreamingIDBack: function(data, textStatus) {

                console.log(data);console.log(textStatus);
                if (textStatus != 'success') {
                    console.log(textStatus);
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
                console.log(data.length);
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
            //var img = document.createElement('img');
            //img.src = streaming;

            //document.getElementById('stage').appendChild(img);
            //监听播放
            //video.addEventListener('play',close('.btn_play_div,#poster'));
            //show('.btn_play_div,#poster');
            //video.addEventListener('play',function(){$('.btn_play_div').css('display','none');$('#poster').remove();});
        });
        Jfn(function(){
            if(liveStatus!=live)
                show('.no-live');
            else{
                show('.has-live');
                showPoster(deg,poster);
            }
            var playCallBack = function(){

                close('#btn_play');console.log('loadding');
                var video = document.getElementById('videoplayer');
                if(video){
                    console.log(video);
                    video.style.top = videoSty.displayTop;
                    //video.src = video.src+'?'+new Date().getTime();
                    //console.log(video.played); console.log(video.paused);
                    video.play();//
                    close('.has-live');
                    //alert(video.played.length);alert(video.paused);
                    /* video.addEventListener('playing',function(){alert('playing')});
                     video.addEventListener('ended',function(){alert('ended')});
                     video.addEventListener('stalled',function(){alert('stalled')});
                     video.addEventListener('emptied',function(){alert('emptied')});
                     video.addEventListener('loadeddata',function(){alert('loadeddata')});
                     video.addEventListener('suspend',function(){alert('suspend')});
                     video.addEventListener('waiting',function(){alert('waiting')});
                     */
                    setInterval(function(){//alert('net:'+video.networkState)
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
<div id="header">
    <div class="logo">
        <img src="img/logo.png">
    </div>
    <div class="func">
        <a class="share" href="#" style="display: none;"></a>
    </div>
</div>
<div align="center" id='stage'>
    <div class="no-live">
        <h1>主播还未开播...</h1>
        <h2>下载欢朋直播，观看更多精彩直播哦</h2>
        <a href="../download.php"><div class="btn_download_nolive">
                下载欢朋直播
            </div></a>
    </div>
    <div class="has-live">
        <div class="btn_play_div">
            <div id="btn_loadding" class="rotation"></div>
            <div id="btn_play"></div></div>
        <div id="poster"><img src=""></div>
    </div>
</div>

<div id="owner">
    <div class="avatar"><img id="avatarimg" src="<?php if($user['pic']) echo "http://{$conf['domain-img']}"."/".$user['pic'];else echo __IMG__.'userface.png' ?>"></div>
    <div class="info">
        <h2 id="hostname"><?php echo $user['nick']; ?></h2>
        <p id="roomname" style="font-size:1rem;"><span style="color:#ff7800;"><?php echo $viewerCount; ?></span>人观看</p>
    </div>
    <div class="btn-appdown"><a href="../download.php">打开APP观看</a></div>
</div>

<div id="func">
    <div class="btn bamboo ico"><a href="../download.php">评论</a></div>
    <div class="btn chat ico"><a href="../download.php">关注</a></div>
    <div class="btn gift ico"><a href="../download.php">分享</a></div>
</div>
<div id="ban"></div>
<div id="recommend" style="">
    <p class="recommendtitle">精彩推荐</p>
    <div class="img_recommend_Wrap clearfix" id="recommendlist"><ul>
            <?php
            foreach ($recomend as $k => $v) {
                $angle = $v['orientation']?'angle_hp':'angle_sp';
                $v['poster'] = $v['poster']?"http://{$conf['domain-img']}"."/"."{$v['poster']}":'../static/img/src/default/260x150.png';
                /*echo "<div class=\"img_recommend fl\"><a href=\"http://dev.huanpeng.com/main/h5share/live.php?u={$v['uid']}\"><img class=\"{$angle}\" src=\"http://{$conf['domain-img']}"."/"."{$v['poster']}\" alt=\"huanpengTV\">
                      <p class=\"img_mask\"></p><p class=\"img_title\">{$v['title']}</p><p class=\"bot_title clearfix\">
                      <span class=\"main_title fl\">{$v['uid']}</span><span class=\"people_number fr\">666</span></p></a></div>";
                      */
                echo "<li class=\"liveOne\"><div class=\"div-poster\"><div class=\"img-block\"><a href=\"live.php?u={$v['uid']}\"><div class=\"img_recommend {$angle}\"><img class=\"{$angle}\" src=\"{$v['poster']}\" alt=\"huanpengTV\">
              </div><p class=\"img_mask\"></p><p class=\"img_title\">{$v['title']}</p><p class=\"bot_title clearfix\">
              <span class=\"main_title fl\">{$v['nick']}</span><span class=\"people_number fr\">{$v['viewerCount']}</span></p></a></div></div></li>";

            }
            ?>
        </ul>
    </div>
</div>
<div id="block-bottom-download">
    <div class="x" onclick="document.getElementById('block-bottom-download').style.display='none'">
    </div>
    <div class="bottom-logo">
        <img src="img/sub_logo.png">
    </div>
    <div class="bottom-txt">
        <h2>下载App领取欢朋豆</h2>
        <p>精彩手游直播，尽在欢朋！</p>
    </div>
    <div class="btn-app">
        <a href="../download.php">打开APP观看</a>
    </div>
</div>
<script type="text/javascript" src="../static/js/common.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>

    //wx.config( config );
    var suid = <?php echo (int)$_GET['suid']; ?>;
    var luid = <?php echo (int)$_GET['u']; ?>;
    var title = '<?php echo $live['title']; ?>';
    var nick = '<?php echo $user['nick']; ?>';

    var wx_config = {
        debug:<?php echo $config['debug']; ?>,
        appId:'<?php echo $config['appId']; ?>',
        timestamp:<?php echo $config['timestamp']; ?>,
        nonceStr:'<?php echo $config['nonceStr']; ?>',
        signature:'<?php echo $config['signature']; ?>',
        jsApiList:[
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone'
        ]
    };
    wx.config(wx_config);
</script>

<script>
    //分享成功会跳
    var share_success = function(){
        //todo
        console.log('share success');
    }
    //分享失败会跳
    var share_cancel = function(){
        //todo
        console.log('share cancel');
    }

    //微信接口检测
    wx.checkJsApi({
        jsApiList:['onMenuShareQQ'],
        success:function(res){
            //console.log(res);
        }
    });
    //测试文案
    /* function getShareContent(){
     return {
     title:'huanpeng-test',
     link:'http://dev.huanpeng.com/main/h5share/live.php?u=15',
     imgUrl:'http://dev-img.huanpeng.com/3/4/34187b3d966925b9f74c2fe8479480fd.jpeg',
     content:'huanpeng-test-content',
     }
     } */
    //获取不同分享文案信息

    function getShare(){
        return {
            'weixin':(function(){//alert(suid);
                var content = getShareContent('wechat',title,nick,luid,suid,true);
                //var imgUrl = '';
                //console.log(content);alert(content['url']);
                return {
                    title:content['title'],
                    link:content['url'],
                    imgUrl:poster,
                    desc:content['content'],
                    success:share_success,
                    cancel:share_cancel
                };
            }()),
            'wechat-qq':(function(){
                var content = getShareContent('wechat-qq',title,nick,luid,suid,true);
                //var imgUrl = '';
                console.log(content);
                return {
                    title:content['title'],
                    link:content['url'],
                    imgUrl:poster,
                    desc:content['content'],
                    success:share_success,
                    cancel:share_cancel
                }
            }())
        }
    }

    var shareContent = getShare();

    //加载初始化事件
    wx.ready(function(){
        //todo
        /*============分享接口===============*/
        //weixin
        wx.onMenuShareTimeline(shareContent['weixin']);
        console.log(shareContent['weixin']);
        //weixin friend
        wx.onMenuShareAppMessage(shareContent['weixin']);
        //QQ friend
        wx.onMenuShareQQ(shareContent['wechat-qq']);
        //QZone
        wx.onMenuShareQZone(shareContent['wechat-qq']);
    })
    //打印注入失败信息
    wx.error(function(){
        //todo
    })

</script>
</body>
</html>
