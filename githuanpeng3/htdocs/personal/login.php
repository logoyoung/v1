<?php

require_once '../../include/init.php';
$url = urlencode('/personal');
if (isset($_GET['ref_url'])) {
    $url = $_GET['ref_url'];
}
$db = new DBHelperi_huanpeng();
$uid = isset($_COOKIE['_uid']) ? (int) $_COOKIE['_uid']   : 0;
$enc = isset($_COOKIE['_enc']) ? trim(($_COOKIE['_enc'])) : '';

if ($uid && $enc) {
    $code = checkUserState($uid, $enc, $db);
    if ($code === true) {
        echo '<meta http-equiv="refresh" content="0;url='.WEB_PERSONAL_URL.'">';
        exit;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>登录-欢朋直播-精彩手游直播平台</title>
    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__; ?>person.css?v=1.0.5">
    <style>
        .container.login-container{
            margin: 60px auto 0 auto;
            box-shadow: none;
            background-color: transparent;
            min-height: 720px;
            width: 1180px;
            position: relative;
        }
        </style>
</head>
<!--<body style="background-color: #eeeeee;background-image: url('http://dev.huanpeng.com/main/static/img/login-bg.png');">-->
<body style="background-color: #eeeeee;">
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container login-container">

    <div class="login-logo"><img src="../static/img/logo/login-logo.png" alt=""/></div>
    <div id="loginModal" class="loginModal"></div>
    <div class="clear"></div>

</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php'; ?>
</body>
<script>
    loginFast.login(0, $('#loginModal'), true);
    $('.toRegister').attr('onclick',null).attr('href','register.php');
</script>
</html>