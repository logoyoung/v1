<?php
$realpath = realpath(__DIR__)."/";
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$userCertifyStatus =  get_userCertifyStatus($_COOKIE['_uid'], $db);

?>

<html>
<head>
    <title></title>
    <meta charset='utf-8'>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
<link rel="stylesheet" type="text/css" href="../../../static/css/common.css?v=1.0.4">
<link rel="stylesheet" type="text/css" href="../../../static/css/home_v3.css?v=1.0.4">
<link rel="stylesheet" type="text/css" href="../../../static/css/person.css?v=1.0.5">

<script type="text/javascript" src="../../../static/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../static/js/common.js?v=1.0.4"></script>
<style type="text/css">
    body{
        background-color: #eeeeee;
    }
    #modifyPhone .container{
        background-color: #fff;
    }

</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<?php
	$url = $_SERVER['PHP_SELF'];
	$error = notLoginErrorPage($_COOKIE['_uid'], $_COOKIE['_enc'], $url, $db);
	if($error !== true){
		exit( $error."</body></html>" );
	}

?>
<div id="modifyPhone">
    <div class="container">
        <div class="pagecontent">
            <div class="pageTitle">修改绑定手机</div>
            <ul class="step">
                <li class="step_identify current">
                    1.身份验证
                    <em></em>
                    <i></i>
                </li>
                <li class="step_modify">
                    2.修改手机
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
            <div class="form-horizontal mt-60 none">
                <div class="control-group">
                    <div class="control-label">手机号码</div>
                    <div class="controls">
                        <input class="w-230" type="text" placeholder="请输入您的手机号">
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
                    <div class="control-label">输入验证码</div>
                    <div class="controls">
                        <input class="w-155 mr-20 left" type="text" placeholder="请输入验证码">
                        <span class="identifyCode mr-20"></span>
                        <span class="changeOne mr-20 mt-15 left">换一张</span>
                        <span class="errInfo left w-170" style=" margin-top:15px;"></span>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">验证码</div>
                    <div class="controls">
                        <input class="w-155 mr-20" type="text" placeholder="请输入短信验证码">
                        <!-- CLASS="wait" 30s后重发-->
                        <input id="getMessCode" class="button blue " type="button" value="免费获取短信验证码">
                        <span class="errInfo"></span>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="button red mt-40" style="width: 54px;padding:14px 32px;">下一步</div>
                    </div>
                </div>
            </div>
            <div class="modifyFinishDiv mt-80">
                <div class="modifyFinish">
                    <div class="logo mr-20"></div>
                    <p>修改号码成功！ 
                        <a href="" style="color: #f44336;">返回首页</a>
                    </p>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
</script>
</body>
</html>