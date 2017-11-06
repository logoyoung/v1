<?php
include_once '../../../../include/init.php';
?>

<html>
<head>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
<link rel="stylesheet" type="text/css" href="../../../static/css/common.css?v=1.0.4">
<link rel="stylesheet" type="text/css" href="../../../static/css/home_v3.css?v=1.0.4">
<link rel="stylesheet" type="text/css" href="../../../static/css/person.css?v=1.0.5">

<script type="text/javascript" src="../../../static/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../static/js/common.js?v=1.0.4"></script>
<style type="text/css">
    body{
        background-color: #eeeeee;
    }
    #certEmail .container{
        background-color: #fff;
    }
    
    .form-horizontal .control-group p.noticeWord{
        text-align: center;
        font-size: 18px;
        margin-bottom: 15px;
        margin-top: 0px;
        color: #333333;
    }
</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="certEmail">
    <div class="container">
        <div class="pagecontent">
            <div class="pageTitle">修改(绑定)邮箱</div>
            <ul class="step">
                <li class="step_identify current">
                    1.身份验证
                    <em></em>
                    <i></i>
                </li>
                <li class="step_modify">
                    2.邮箱确认
                    <em></em>
                    <i></i>
                </li>
                <li class="step_finished">
                    3.完成修改
                    <em></em>
                    <i></i>
                </li>
                <div class="clear"></div>
            </ul>
            <div class="clear"></div>
            <div class="form-horizontal mt-60 ">
                 <div class="control-group">
                    <div class="control-label">邮箱地址</div>
                    <div class="controls">
                        <input class="w-230" type="text" placeholder="请输入您的邮箱地址">
                        <span class="errInfo"></span>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">登录密码</div>
                    <div class="controls">
                        <input class="w-230" type="password" placeholder="请输入您的密码">
                        <span class="errInfo"></span>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="button red mt-40" style="width: 54px;padding:14px 32px;">下一步</div>
                    </div>
                </div>
            </div>
            <div class="form-horizontal mt-60 none">
                <div class="control-group">
                    <p class='noticeWord'>我们已经向您的邮箱wulalalnvwang@qq.com 发送了一封激活邮件，请点击邮件中的链接完成注册</p>
                    <p class='noticeWord'>注册成功后，您就可以享受更多服务啦！</p>
                </div>
                <div class="controls-group">
                    <div class="controls">
                        <div class="button red mt-40" style="width: 108px;padding:14px 60px;">进入邮箱验证</div>
                    </div>
                </div>
            </div>
            <div class="modifyFinishDiv mt-80 none">
                <div class="modifyFinish">
                    <div class="logo mr-20"></div>
                    <p>邮箱绑定成功！ 
                        <a href="" style="color: #f44336;">返回首页</a>
                    </p>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>