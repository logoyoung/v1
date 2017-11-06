<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/16
 * Time: 上午10:40
 */
include '../includeAdmin/init.php';
include INCLUDE_DIR . 'Admin.class.php';

$db = new DBHelperi_admin();
if((int)$_COOKIE['admin_uid'] && $_COOKIE['admin_enc'] && $_COOKIE['admin_type']){
    $admin = New AdminHelp($_COOKIE['admin_uid'], $_COOKIE['admin_type'], $db);
    if(!$ret = $admin->loginError($_COOKIE['admin_enc'])){

        header("Location:http://" . $GLOBALS['env-def'][$GLOBALS['env']]['domain'] . "/admin2/view/index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>手游直播后台管理系统</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!--    --><?php //include ADMIN_MODULE.'mainStyle.php';?>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="../common/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="../common/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->

    <!-- BEGIN THEME STYLES -->
    <link href="../common/global/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="../common/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../common/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../common/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->

    <link href="../common/admin/pages/css/login.css" rel="stylesheet" type="text/css"/>
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo"></div>
<!-- END LOGO -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <form class="login-form" action="" method="post">
        <h3 class="form-title">手游直播后台管理系统</h3>
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
			<span>请输入用户名和密码. </span>
        </div>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">邮箱</label>
            <input class="form-control form-control-solid placeholder-no-fix input-icon" type="text" autocomplete="off" placeholder="邮箱" name="username"/>

        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">密码</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="password"/>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">用户类型</label>
            <select name="group" class="form-control">
                <option value="1">管理员</option>
                <option value="2">审查人员</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success uppercase">登录</button>
            <a href="javascript:;" id="forget-password" class="forget-password">忘记密码?</a>
        </div>
    </form>
    <!-- END LOGIN FORM -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="forget-form" action="index.html" method="post">
        <h3>忘记密码 ?</h3>
        <p>
            输入邮箱找回密码
        </p>
        <div class="form-group">
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="邮箱地址" name="email"/>
        </div>
        <div class="form-actions">
            <button type="button" id="back-btn" class="btn btn-default">返回</button>
            <button type="submit" class="btn btn-success uppercase pull-right">提交</button>
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->
</div>
<?php include ADMIN_MODULE.'footer.php';?>

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../common/global/plugins/respond.min.js"></script>
<script src="../common/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../common/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jqPaginator.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../common/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../common/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
<script src="../common/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="../common/global/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>
<script src="../common/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../common/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../common/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../common/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="../common/global/scripts/common.js"></script>
<script src="../common/admin/pages/scripts/login.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script type="text/javascript">
    $(document).ready(function(){
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        Login.init()

    });
</script>
<script>

</script>
</body>
</html>