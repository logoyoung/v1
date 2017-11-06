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
    <script> opt = 'recharge'; </script>
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
                <p class="account-register">充值问题</p>
                <p>1.什么是欢朋币？</p>
                <p>欢朋币是欢朋直播平台用户消费的虚拟货币，可以用来购买虚拟物品赠送给主播。</p>
                <p>2.充值欢朋币</p>
                <p>目前可充值10元、50元、100元、500元、1000元以及其他自定义金额，可通过官网、手机APP进行充值，目前支持支付宝、微信充值。人民币与欢朋币的充值兑换比例为1:10。</p>
                <p class="tips">温馨提示：</p>
                <p>充值数额最低为10元；</p>
                <p>支付成功后页面会提示“充值成功”，您可以刷新页面后查看。</p>
                <p>如果您充值遇到问题，可以联系客服。</p>
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
