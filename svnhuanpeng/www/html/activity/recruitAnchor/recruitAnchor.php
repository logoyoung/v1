<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/2/25
 * Time: 20:07
 */
include_once __DIR__.'/../../../include/init.php';
include_once (INCLUDE_DIR.'User.class.php');
$isMobile = isMobile();
$isMobile = $isMobile?1:0;
?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <title>欢朋直播-精彩手游直播</title>
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no" />
    <meta content="email=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name=”apple-mobile-web-app-status-bar-style” content=black” />
    <meta name="keywords" content="游戏直播,手机游戏直播,电子竞技直播,热门游戏直播,欢朋直播,王者荣耀直播,CF手游直播">
    <meta name="description" content="欢朋直播是一家致力于为用户带来欢乐的手游直播互动平台。欢朋直播拥有包括热门手游直播，如王者荣耀、球球⼤作战、cf枪战王者、天天酷跑、皇室战争、阴阳师、炉石传说等；以及摄像头直播，如户外直播、星秀、校园、二次元等多元化内容。">
    <meta name="author" content="huanpeng">
    <!--<link rel="stylesheet" href="css/reset.css">-->
    <link rel="stylesheet" href="css/index.css" id="styleCss">
    <!--百度统计-->
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?f97f114982484f9851e7c242cc1dac9b";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <script>
        (function () {
            var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
            if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i) || winW <= 500) {
                var styleCss = document.querySelector('#styleCss');
                styleCss.href = 'css/mobile.css';

                /*rem*/
                (function(){
                    size();
                    window.onresize = function (){
                        size();
                    };
                    function size(){
                        var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
                        document.documentElement.style.fontSize = winW / 20 +'px';
                    }
                })();
            }
        })();
    </script>

    <?php $path = realpath(__DIR__); include $path . '/../../../htdocs/tpl/commSource.php';?>
    <style>
        #btn1>img,#btn2>img,#scene>li>img{
            width: auto;
            height: auto;
        }
    </style>
</head>
<body>
<div id="rechead" style="display: none;">
<?php /*if(!$isMobile)*/ include $path . '/../../../htdocs/head.php';?>
    </div>
<!--招募容器-->
<div class="hp_container">
    <div class="hp_wrap">
    <div class="hp_syzm" style="display: none;">
        <ul id="scene">
            <li class="layer" data-depth="0.3">
                <img src="img/lb.png" class="lb">
            </li>
            <li class="layer" data-depth="0.2">
                <img src="img/dc.png" class="dc">
            </li>
            <li class="layer" data-depth="0.1">
                <img src="img/mn.png" class="mn">
            </li>
            <li class="layer" data-depth="0.3">
                <img src="img/k1.png" class="k1">
            </li>
            <li class="layer" data-depth="0.2">
                <img src="img/n1.png" class="n1">
            </li>
            <li class="layer" data-depth="0.1">
                <img src="img/n2.png" class="n2">
            </li>
            <img src="img/headerMa.png" class="headMa">
        </ul>

    </div>
    <!--招募简介-->
    <div class="hp_main">
        <!--招募简介-->
        <div class="hp_intro">
            <div class="intro_border">
                <div class="intro_desc">
                    <p>◆&nbsp;&nbsp;想成为万众瞩目的手游达人吗？</p>
                    <p>◆&nbsp;&nbsp;想打造别具一格的手游直播公会吗？</p>
                    <p>◆&nbsp;&nbsp;喜欢尝试但没有经验怎么办？</p>
                    <p>◆&nbsp;&nbsp;没关系，我来教！不管你是不是主播！现在加入手游达人招募计划，你的成就将远远超越这些！</p>
                    <p>◆&nbsp;&nbsp;只要你有善于分享的性格，只要你喜欢手游，那么欢朋直播团队将为你定制专属的推广方案。</p>
                </div>
            </div>
        </div>
        <!--招募简介-->
        <!--达人申请-->
        <div class="hp_apply">
            <div class="apply_border">
                <div class="apply_desc">
                    <p>◆&nbsp;&nbsp;招募对象：所有想直播手游的主播和公会或者喜欢手游想成为主播的用户。</p>
                    <p>◆&nbsp;&nbsp;唯一条件：热爱游戏！喜欢直播！</p>
                    <p>
                        <strong>招募主播QQ:</strong>
                        <span class="hp_color">2746304894</span>
                        <a id="btn1" target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=2746304894&site=qq&menu=yes"><img border="0" src="https://wpa.qq.com/pa?p=2:2746304894:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
                    </p>
                    <p>
                        <strong>招募经纪公司QQ:</strong>
                        <span class="hp_color">2653534448</span>
                        <a id="btn2" target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=2653534448&site=qq&menu=yes"><img border="0" src="https://wpa.qq.com/pa?p=2:2653534448:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>

                    </p>
                </div>
            </div>
        </div>
        <!--达人申请-->
        <!--扶持方案-->
        <div class="hp_project">
            <div class="hp_core">
                <p>◆&nbsp;&nbsp;欢朋主站置顶推荐</p>
                <p>◆&nbsp;&nbsp;六间房主站推荐</p>
                <p>◆&nbsp;&nbsp;石榴直播强势推荐</p>
                <p>◆&nbsp;&nbsp;各大合作渠道专区头条</p>
            </div>
            <div class="hp_money">
                <p>◆&nbsp;&nbsp;丰厚底薪</p>
                <p>◆&nbsp;&nbsp;高额礼物兑换</p>
                <p>◆&nbsp;&nbsp;活动高额补贴</p>
                <p>◆&nbsp;&nbsp;收益额外补贴</p>
                <p>◆&nbsp;&nbsp;只有你想不到！</p>
            </div>
        </div>
        <div class="hp_clear">
            <p><!--此招募活动最终解释权归欢朋运营团队所有--></p>
        </div>
    </div>
    <!--注意事项-->
    <div id="recfooter" style="display: none;">
    <?php /*if(!$isMobile)*/ include $path . '/../../footerSub.php';?>
        </div>
</div>
</div>


<div class="hp_download" style="display: none;">
    <img src="img/closeBtn.png" class="close">
    <figure>
        <img src="img/hpLogo.jpg">
        <figcaption>
            <p>下载App领取欢朋豆</p>
            <span>精彩手游直播,尽在欢朋!</span>
        </figcaption>
    </figure>
    <a href="../../download.php" class="openApp">打开APP观看</a>
</div>

</body>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.parallax.min.js"></script>
<script>

    var isMobile = "<?php echo (int)$isMobile; ?>";
    isMobile = parseInt(isMobile);
    if(!isMobile)
        var head = new head(null,false);

    $(document).ready(function () {
        $('#scene').parallax();
    });
    window.onload = function () {
        var winW = document.documentElement.clientWidth || document.documentElement.body.clientWidth;
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i) || winW <= 500){
            var btn1     = document.querySelector('#btn1');
            var btn2     = document.querySelector('#btn2');

            btn1.style.display = 'none';
            btn2.style.display = 'none';

            if(window.huanpengShare){
                $('.hp_download').css('display','none');
            }
            $('.close').click(function(){
                $('.hp_download').css('display','none');
            });

        }else{
            $('#rechead,#recfooter,.hp_syzm').css('display','block');
        }
    }
</script>
 
</html>
