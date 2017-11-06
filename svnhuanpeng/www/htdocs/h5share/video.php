<?php
require ('../init.php');
define('RECOMMEND', 6); 

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$vid = isset($_GET['v'])?(int)$_GET['v']:'';
if(!$vid) exit;

$db = new DBHelperi_huanpeng();
//获取录像
$video = getVideoInfoById($vid, $db);//var_dump($video);
//获取推荐直播
$vList = getRecommendLiveList($video['uid'], RECOMMEND, $db);//var_dump($vList);
?>
<!DOCTYPE html>
<html lang="en" data-dpr="3" max-width="540">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $video['gameName'] .'-'. $video['title'];?></title>
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
  width:100%;
  height:100%; 
  position: absolute;
  left: 0;
  top: 0;
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
	<script type="text/javascript" src="../static/jquery.min.js"></script>
	<script type="text/javascript">
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
        <video  controls  width="100%" height="100%" src="<?php echo $video['videoUrl']; ?>">
            浏览器不支持HTML5播放器，请换别的浏览器播放。
        </video>
	</div>
	
	<div id="owner">
        <div class="avatar"><img id="avatarimg" src="<?php echo $video['head']; ?>"></div>
        <div class="info">
            <h2 id="hostname"><?php echo $video['nick']; ?></h2>
            <p id="roomname" style="font-size:1rem;"><span style="color:#ff7800;"><?php echo $video['viewCount']; ?></span>人观看</p>
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
       // var_dump($vList);
    foreach ($vList['list'] as $k => $v) {
        $angle = $v['orientation']?'angle_hp':'angle_sp';
        $v['poster'] = $v['poster']?$v['poster']:'../static/img/src/default/260x150.png';
        /*echo "<div class=\"img_recommend fl\"><a href=\"http://dev.huanpeng.com/main/h5share/live.php?u={$v['uid']}\"><img class=\"{$angle}\" src=\"http://{$conf['domain-img']}"."/"."{$v['poster']}\" alt=\"huanpengTV\">
              <p class=\"img_mask\"></p><p class=\"img_title\">{$v['title']}</p><p class=\"bot_title clearfix\">
              <span class=\"main_title fl\">{$v['uid']}</span><span class=\"people_number fr\">666</span></p></a></div>";
              */
              echo "<li class=\"liveOne\"><div class=\"div-poster\"><div class=\"img-block\"><a href=\"video.php?v={$v['vid']}\"><div class=\"img_recommend {$angle}\"><img class=\"{$angle}\" src=\"{$v['poster']}\" alt=\"huanpengTV\">
              </div><p class=\"img_mask\"></p><p class=\"img_title\">{$v['title']}</p><p class=\"bot_title clearfix\">
              <span class=\"main_title fl\">{$v['nick']}</span><span class=\"people_number fr\">{$v['viewCount']}</span></p></a></div></div></li>";

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
        <h2>下载APP领取更多好礼</h2>
        <p>更多精彩直播&nbsp&nbsp尽在欢朋TV</p>
    </div> 
    <div class="btn-app">
        <a href="../download.php">打开APP观看</a>
    </div>
</div>

<script>

</script>
</body>
</html>
