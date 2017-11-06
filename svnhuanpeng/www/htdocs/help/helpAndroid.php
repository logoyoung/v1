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
    <script> opt = 'help-Android'; </script>
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
                <p class="account-register">Android版</p>
                <h3>一、直播前准备：</h3>
                <p>安装欢朋直播<a href="../download.php" target="_blank" style="color:#ff7800;">Android版App</a>；认证主播才可以发起直播，<a href="../personal/beanchor.php" style="color: #ff7800;">立即认证</a>。</p>
                <p>建议使用性能比较好的安卓设备进行直播，系统版本Android 5.0及其以上。</p>
                <h3>一、Android版App教程</h3>
                <p ><a href="../videoRoom.php?videoid=14840" style="color:#ff7800;">您也可以点击这里，观看视频教程。</a></p>
                <p>安卓直播有两种形式：“手游录屏直播”和“摄像头直播”，以下以“手游录屏直播”为例进行直播。</p>
                <p>1.打开欢朋直播 App并登录帐号，点击首页底栏中间的“发直播”按钮，选择“手游录屏直播”发起直播（如图所示▼）；</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-21.png">
                </div>

                <p>2.请按提示开启权限；</p>
                <p>3.编辑直播标题，并设置你要直播的清晰度，分享您的直播间给好友，再点击“开始直播”即可开启直播了（如图所示▼）；</p>
                <p>a.标题：好的标题可以吸粉哦～</p>
                <p>b.游戏：一定要选择你要直播的游戏哦～</p>
                <p>c.画质：你手机配置和网络够好，就选择高清画质吧</p>
                <p>d.横竖屏：请确认横竖屏跟你要直播的游戏画面保持一致～</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-22.png">
                </div>
                <p>4.您的直播已经成功发起了，点击圆形的“回到桌面”按钮打开您要直播的游戏，表现的时刻就到了（如图所示▼）；</p>
                <div style="width: 658px;">
                    <img src="../static/img/help/help-23.png">
                </div>
                <p>5.在直播的过程中，您可以利用悬浮窗和粉丝进行互动哦。收到礼物记得感谢～（如图所示▼）；</p>
                <p>a.欢朋小标：可以收起和展开菜单</p>
                <p>b.“回到应用”按钮：点击回到应用中您的直播间</p>
                <p>c.“聊天开关”按钮：点击开启或关闭右侧聊天区域</p>
                <p>d.“隐私模式”按钮：点击可以开启隐私模式，画面不会被直播出去；</p>
                <p>d.聊天区域：用户送礼和发言会在这里显示，右上角是观众数量，右下角可以发送文字；</p>
                <div style="width: 658px;">
                    <img src="../static/img/help/help-24.png">
                </div>
                <p>点击欢朋小标可以收起菜单（如图所示▼）</p>
                <div style="width: 658px;">
                    <img src="../static/img/help/help-25.png">
                </div>
                <p>6.点击悬浮窗中的“回到应用”按钮回到您的直播间，点击右上角的关闭按钮，就可以结束直播了（如图所示▼）。</p>
                <div style="width: 658px;">
                    <img src="../static/img/help/help-26.png">
                </div>
                <p>7.结束直播后，会显示您本场直播的统计哦，您可以分享一下成果～同时您也可以发布本场直播的视频哦（如图所示▼）。</p>
                <div style="width: 250px;">
                    <img src="../static/img/help/help-27.png">
                </div>
                <p>如您在使用过程中遇到问题、有建议或意见，可以找到的设置中的意见反馈，编辑好发送给我们。</p>
                <p>温馨提示：</p>
                <p style="line-height: 1em;">你可以使用WI-FI或4G进行直播，4G直播会消耗流量，建议您购买电信运营商的流量套餐。</p>
                <p style="line-height: 1em;">小米手机需要单独开启悬浮窗权限，“安全中心-授权管理-应用权限管理-权限管理-显示悬浮窗”找到欢朋直播开启。</p>
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
