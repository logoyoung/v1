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
    <script> opt = 'help-iPhone'; </script>
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
                <p class="account-register">iPhone版</p>
                <h3>一、直播前准备：</h3>
                <p>安装欢朋直播 <a href="../download.php" target="_blank" style="color: #ff7800;">iPhone 版 App</a>;认证主播才可以发起直播，<a href="../personal/beanchor.php" target="_blank" style="color: #ff7800;">立即认证。</a></p>
                <p>建议使用iPhone 5 及以上的设备进行直播，系统版本 iOS 8.0 及其以上。</p>
                <h3>二、iPhone版App教程</h3>
                <p><a href="../videoRoom.php?videoid=14845" style="color:#ff7800;">您也可以点击这里，观看视频教程。</a></p>
                <p>iPhone 直播暂时只支持“摄像头直播”。iPhone 录屏直播需要借助电脑，查看<a href="helpAssistant.php"  style="color: #00ff55;">录屏直播教程</a>。</p>
                <p>1.打开欢朋直播 App并登录帐号，点击首页底栏中间的“发直播”按钮，开启发直播页面（如图所示▼）；</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-15.png">
                </div>

                <p>2.请按提前开始摄像头、麦克风、位置权限（如图所示▼）；</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-16.png">
                </div>
                <p>3.编辑直播标题，并设置你要直播的清晰度，分享您的直播间给好友，再点击“开始直播”即可开启直播了（如图所示▼）；</p>
                <p>a.标题：好的标题可以吸粉哦～</p>
                <p>b.画质：你手机配置和网络够好，就选择高清画质吧</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-17.png">
                </div>
                <p>4.您的直播已经成功发起了，您可以粉丝进行互动了。收到礼物记得感谢哦～（如图所示▼）</p>
                <p>a.在线观众人数</p>
                <p>b.金豆收益，点击查看贡献榜单</p>
                <p>c.新进入直播间的粉丝</p>
                <p>d.粉丝送礼展示区域</p>
                <p>e.聊天区域</p>
                <p>f.发言按钮</p>
                <p>g.分享按钮</p>
                <p>h.翻转相机按钮</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-18.png">
                </div>
                <p>5.点击右上角的关闭按钮，可以结束直播（如图所示▼）；</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-19.png">
                </div>
                <p>6.结束直播后，会显示您本场直播的统计哦，您可以分享一下成果～同时您也可以发布本场直播的视频哦（如图所示▼）。</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-20.png">
                </div>
                <p>如您在使用过程中遇到问题、有建议或意见，可以找到的设置中的意见反馈，编辑好发送给我们。</p>
                <p>温馨提示：</p>
                <p style="line-height: 1em;">你可以使用WI-FI或4G进行直播，4G直播会消耗流量，建议您购买电信运营商的流量套餐。</p>
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
