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
    <script> opt = 'login'; </script>
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
                <p class="account-register">账号相关</p>
                <p>1.欢朋账号登录</p>
                <p>支持手机号登录，或者也可使用第三方账户进行登录。若您的手机已经绑定过第三方账号，您可以通过第三方账号或者手机号登录。</p>
                <p>2. 为什么登录账号时提示“账号已被封禁”？</p>
                <p>若账号违反欢朋直播平台规范条例被系统封禁，登录时会提示“账号已被封禁”。如果您没有做违规操作，请联系客服。</p>
                <p>3.如何修改账号昵称？</p>
                <p>进入个人中心，在“个人资料”页面，点击昵称右侧的“修改”按钮修改昵称，修改昵称需要花费600欢朋币；
                    目前第三方账号登录可以免费修改一次昵称，昵称应为4-16个字符，不得与其他用户的昵称重复。</p>
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
