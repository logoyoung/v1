<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/11/22
 * Time: 11:00
 */
/**
 * 关于我们页面
 *
 *
 *   */
?>
<!DOCTYPE html>
<html>
<head>
    <title>关于我们-欢朋直播-精彩手游直播平台！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset='utf-8'>
    <?php include './tpl/commSource.php'; ?>
    <script type="text/javascript" src="./static/js/page.js?v=1.0.4"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        img {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        html, body {
            background: #fff;
        }

        .about-contain {
            width: 1438px;
            margin: auto;
            color: #444;
        }

        .about-contain .contain-head {
            width: 100%;
            height: 320px;
            text-align: center;
            line-height: 320px;
            font-size: 20px;
            color: #fff;
            background: url(static/img/about/about_city.jpg) no-repeat;
        }

        .about-contain .contain-body {
            width: 1200px;
            margin: auto;
        }

        .contain-body .body-nav {
            width: 100%;
            height: 68px;
            border-bottom: 1px solid #ddd;
        }

        .contain-body .body-content {
            width: 100%;
        }

        .body-nav .nav-tab {
            float: left;
            font-size: 16px;
            line-height: 50px;
            padding: 0px 21px;
            margin: 17px 50px 0px 0px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .body-nav .nav-tab:hover {
            color: #ff7800;
        }

        .body-nav .nav-tab.show {
            border-bottom-color: #ff7800;
            color: #ff7800;
        }

        .content-about .about-left {
            width: 360px;
            height: 240px;
            float: left;
            margin: 56px 40px 0px 0px;
        }

        .content-about .about-right {
            width: 790px;
            min-height: 500px;
            float: left;
            margin-top: 13px;
        }

        .content-about .about-right p {
            font-size: 14px;
            text-indent: 2em;
            line-height: 2.5em;
            margin: 30px 0px;
        }

        .content-connect .connect-left {
            width: 400px;
            margin-top: 0px;
            float: left;
        }

        .connect-left .connect-info-one {
            margin: 40px 0px;
        }

        .connect-info-one .connect-one-head {
            border-bottom: 2px solid #ff7800;
            /* line-height: 63px; */
            padding-bottom: 5px;
        }

        .connect-info-one p {
            line-height: 40px;
            font-size: 14px;
        }

        .content-connect .connect-right {
            width: 600px;
            height: 350px;
            float: right;
            margin-top: 51px;
        }

        .content-connect, .content-about {
            display: none;
        }

        .content-connect.show, .content-about.show {
            display: block;
        }

        .footer-content {
            width: 100%;
            height: 66px;
            text-align: center;
            float: left;
            padding-top: 30px;
            line-height: 2em;
            color: #666;
            background: #f2f2f2;
        }

    </style>
</head>
<body>
<?php include 'head.php' ?>
<div class="about-contain">
    <div class="contain-head">我们是一群有追求的人，让直播互动更美好</div>
    <div class="contain-body">
        <div class="body-nav">
            <div class="nav-tab nav-about">关于我们</div>
            <div class="nav-tab nav-connect">联系我们</div>
        </div>
        <div class="body-content">
            <div id="block-about" class="content-about">
                <div class="about-left">
                    <img src="static/img/about/about_office.jpg">
                </div>
                <div class="about-right">
                    <p>欢朋直播（六间房旗下直播平台）是一家致力于为用户带来快乐的手游直播互动平台。
                        我们力图为您提供最好的直播与观看体验；并希望与您共同进步，绘制更加美好的未来。</p>
                    <p>欢朋直播产品覆盖WEB、移动、PC三端，拥有包括游戏直播、欢朋星秀等多种品类精彩直播，
                        涵盖游戏、音乐、美女、户外、真人秀、娱乐、美食等多元化热门内容</p>
                    <p>2017年初，六间房宣布成立游戏直播事业部，以游戏直播、游戏运营、游戏资讯、三位一体的运营架构，
                        搭建富集生态圈战略，并通过生态圈内外资源整合，建立一套循环再生的闭环经营模式。</p>
                    <p>精彩手游直播，尽在欢朋！</p>
                </div>
            </div>
            <div id="block-connect" class="content-connect">
                <div class="connect-left">
                    <div class="connect-info-one">
                        <p><span class="connect-one-head">联系地址</span></p>
                        <p>北京市海淀区首体南路9号主语国际5号楼8F</p>
                        <p>邮编：100044</p>
                    </div>
                    <div class="connect-info-one">
                        <p><span class="connect-one-head">商务合作</span></p>
                        <p>邮箱：xiaoqi@6.cn</p>
                    </div>
                    <div class="connect-info-one">
                        <p><span class="connect-one-head">广告合作</span></p>
                        <p>邮箱：fengyan@6.cn</p>
                    </div>

                </div>
                <div class="connect-right">
                    <img src="static/img/about/about_map.jpg">
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="footer-content">
    <p>©Copyright 2015 huanoeng.com All Rights Reserved 备案号：京ICP备09082681号－8</p>
    <p>欢朋直播 版权所有</p>
</div>-->
<?php $path = realpath(__DIR__); include $path.'/footerSub.php' ?>

<script>
    var head = new head(null, false);
    var page = new page();
    page.init();
    $('.footer-bottom').css('background','#fff');
</script>
<script>
    var sw = function (refType) {
        if (refType == '#about') {
            $('.nav-about,.content-about').addClass('show');
            $('.nav-connect,.content-connect').removeClass('show');
            window.location.hash='#about';
        }
        else {
            $('.nav-connect,.content-connect').addClass('show');
            $('.nav-about,.content-about').removeClass('show');
            window.location.hash='#connect';
        }
    }
    $(function () {
        var refType = document.location.hash ? document.location.hash : '#about';
        sw(refType);
        $('.nav-about').bind('click', function () {
            sw('#about');
        })
        $('.nav-connect').bind('click', function () {
            sw('#connect');
        })

    })
</script>
</body>
</html>