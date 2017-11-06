<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/12/16
 * Time: 11:27
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>帮助中心-欢朋直播-精彩手游直播平台！</title>
    <?php include '../tpl/commSource.php';?>
    <link rel="stylesheet" type="text/css" href="../static/css/help.css">
    <script> opt = 'reg'; </script>
</head>
<body>
<div class="nav">
    <div class="help-nav">
        <div class="help-logo"><a href="../index.php"><img src="../static/img/logo_v2.png"></a></div>
        <div class="help-title">帮助中心</div>
        <div class="help-home"><a href="../index.php"><div class="icon-go-home"></div><span>返回首页</span></a></div>
    </div>
</div>
<div class="help-contain">
    <div class="help-content">
        <?php include 'helpSlider.php'; ?>
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
                <p>同一个手机仅可注册一个账号，一旦注册成功的手机无法重新注册。</p>
            </div>
        </div>
    </div>
    <?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</div>
<script>
    $(function () {

    })
</script>
</body>
</html>
