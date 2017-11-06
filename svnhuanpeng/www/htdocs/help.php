<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/11/22
 * Time: 18:24
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>帮助中心</title>
    <?php include '../../tpl/commSource.php';?>
    <style>
        *{
            padding: 0;
            margin: 0;
        }
        img{
            width: 100%;
            height:100%;
            overflow: hidden;
        }
        a{
            text-decoration: none;
        }
        a:hover{
            color: inherit;
        }
        a:visited{
            color: inherit;
        }
        .nav{
            width: 100%;
            background: #fff;
            padding-top:12px;
            height: 45px;
        }
        .help-nav{
            width: 1200px;
            margin: auto;
        }

        .help-nav .help-logo{
            float: left;
        }
        .help-nav .help-title{
            float: left;
            font-size: 16px;
            padding: 0px 20px 0px 32px;
            line-height: 22px;
            border-left: 2px solid #ddd;
            margin: 9px 32px;
        }
        .help-nav .help-home{
            float: right;
            font-size: 14px;
            padding: 13px 20px 0px 17px;
            color: #666;
        }
        .help-nav .help-home .icon-go-home{
            width: 20px;
            height: 20px;
            float: left;
            background: url(<?php echo WEB_ROOT_URL; ?>static/img/download/btn-download.png) 0px -398px no-repeat;
        }
        .help-nav .help-home:hover .icon-go-home{
            background-position: -21px -398px;
        }
        .help-nav .help-home:hover{
            color: #ff7800;
        }
        .help-contain{
            width: 1200px;
            margin: auto;
        }
        .help-contain .help-content{
            float: left;
            width: 100%;
            min-height: 700px;
            background: #fff;
            margin-top: 40px;
        }
        .help-content .content-left{
            padding: 30px;
            height: 100%;
            float: left;
            border-right: 1px solid #ddd;
        }
        .content-left .slider-list{
            background: #F5F5F5;
        }
        .content-left .slider-list .slider-one{
            width: 222px;
            border-bottom: 1px solid #ddd;
        }
        .slider-list .slider-one .one-title{
            text-align: center;
            height: 40px;
            line-height: 40px;
            font-size: 16px;
        }
        .slider-list .slider-one .one-tab{
            text-align: center;
            height: 36px;
            line-height: 36px;
            font-size: 14px;
            color:#444;
            cursor:pointer;
            border-left: 2px solid transparent;
        }
        .slider-list .slider-one .one-tab.show{
            background: #fff;
            border-left-color: #ff7800;
            color: #ff7800;
        }
        .help-content .content-right{
            padding: 40px;
            height: 100%;
            float: left;
        }
        .help-content .content-right .show-content{
            width: 600px;
            height: 500px;
            display: none;
        }
        .help-content .content-right .show-content.show{
            display: block;
        }
        .help-content .content-right .show-content>p{
            line-height: 2.6em;
            color: #666;
            font-size: 14px;
        }
        .help-content .content-right .show-content p.tips{
            margin-top: 50px;
        }
        .help-content .content-right .show-content p.account-register{
            margin-bottom: 13px;
            font-size: 16px;
            color: #000;
            line-height: 18px;
        }
        .footer-content{
            width: 1200px;
            height: 66px;
            text-align: center;
            float: left;
            margin-top: 30px;
            line-height: 2em;
            color: #666;
        }
    </style>
</head>
<body>
<div class="nav">
<div class="help-nav">
    <div class="help-logo"><a href="http://dev.huanpeng.com/main"><img src="http://dev.huanpeng.com/main/static/img/logo_v2.png"></a></div>
    <div class="help-title">帮助中心</div>
    <div class="help-home"><a href="http://dev.huanpeng.com/main"><div class="icon-go-home"></div><span>返回首页</span></a></div>
</div>
    </div>
<div class="help-contain">
    <div class="help-content">
    <div class="content-left">
        <div class="slider-list">
            <div class="slider-one">
                <div class="one-title">账号相关</div>
                <div class="one-tab show">账号注册</div>
                <div class="one-tab">账号登陆</div>
                <div class="one-tab">密保问题</div>
            </div>
        </div>
        <div class="slider-list">
            <div class="slider-one">
                <div class="one-title">主播相关</div>
                <div class="one-tab">主播认证</div>
                <div class="one-tab">主播收益</div>
                <div class="one-tab">主播提现</div>
            </div>
        </div>
        <div class="slider-list">
            <div class="slider-one">
                <div class="one-title">直播教程</div>
                <div class="one-tab">PC直播客户端</div>
                <div class="one-tab">iPhone版</div>
                <div class="one-tab">安卓版</div>
            </div>
        </div>
    </div>
        <div class="content-right">
            <div class="show-content show">
                <p class="account-register">账号注册</p>
                <p>1.欢朋直播账号怎么注册？</p>
                <p>欢朋直播网站主页右上角点击"注册"开始注册；也可以使用手机APP进行注册。</p>
                <p>2.获取不到短信验证码？</p>
                <p>您如果没有收到短信验证码，可以在60s后重新获取；每天能获取5次验证码。</p>
                <p class="tips">友情提示：</p>
                <p>欢朋直播账号可通过手机注册，也可以通过使用第三方账号登录；</p>
                <p>为了账号安全，请在注册账号后即使绑定手机；</p>
                <p>同一个手机仅可注册一个账号，一注册成功的手机无法重新注册。</p>

            </div>
            <div class="show-content">2</div>
            <div class="show-content">3</div>
            <div class="show-content">4</div>
        </div>
    </div>
    <div class="footer-content">
        <p>©Copyright 2015 huanoeng.com All Rights Reserved 备案号：京ICP备09082681号－8</p>
        <p>欢朋直播 版权所有</p>
    </div>
</div>
<script>
    $(function () {

    })
</script>
</body>
</html>