<?php
$path = realpath(__DIR__);
include_once ($path.'/../../../include/init.php');
include_once WEBSITE_MAIN . "initCookie.php";
include_once WEBSITE_PERSON . "isLogin.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH; ?>person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/jquery.form.js"></script>
    <style type="text/css">
        body {
            background-color: #eeeeee;
        }

        .content {
            min-height: 820px;
        }
        .control-group.three-side-bind{
            margin-top:50px;
        }

        .control-group.three-side-bind .control-label{
            width: 90px;
        }
        .control-group.three-side-bind .controls{
            margin-left:144px;
        }
        .control-group.three-side-bind .controls .bind-body{
            display: inline-block;
            margin-right:60px;
            text-align: center;
        }
        .control-group.three-side-bind .controls .bind-body img{
            width:60px;
            height: 60px;
            display: inline-block;
        }
        .control-group.three-side-bind .controls .bind-body .todo{
            color: blue;
            cursor: pointer;
        }
        .control-group .nick{
            color: blue;
            cursor: pointer;
        }

        .select-container {
            font-size: 0;
            box-sizing: border-box;
            font-family: 微软雅黑;
            position: relative;
            margin: 0 auto;
            width: 800px;
            height: 36px;
        }
        .select-container .selectCity {
            width: 178px;
            height: 36px;
            display: inline-block;
        }
        .select-container .selectCity #proCity {
            width: 178px;
            height: 36px;
            line-height: 36px;
            text-indent: 10px;
            color: #666;
            font-size: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        .select-container .selectCity .selectTitle {
            position: absolute;
            width: 448px;
            height: 216px;
            left: 0;
            top: 48px;
            padding: 5px;
            border: 1px solid #ccc;

        }

        #personal .select-container .selectCity .selectTitle .title.block-title {
            position: absolute;
            width: 458px;
            height: 30px;
            line-height: 30px;
            left: 0;
            top: 0;
            padding: 0px;
        }
        .select-container .selectCity .selectTitle .title > div {
            width: 50%;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            background-color: #efefef;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            -o-transition: all 300ms;
            -moz-transition: all 300ms;
            -webkit-transition: all 300ms;
            transition: all 300ms;
        }
        .select-container .selectCity .selectTitle .title .title-prov {
            font-size: 14px;
            color: black;
        }
        .select-container .selectCity .selectTitle .title .title-city {
            font-size: 14px;
            color: black;
            border-right: none;
        }
        .select-container .selectCity .selectTitle .title .curr {
            background-color: #fff;
            border-bottom: 1px solid #fff;
        }
        .select-container .selectCity .selectTitle .contentBox {
            height: 192px;
            width: 458px;
            position: absolute;
            left: 0;
            bottom: 0;
            border-top: none;
            overflow: hidden;
        }
        .select-container .selectCity .selectTitle .contentBox .content {
            width: 920px;
            height: 196px;
            position: absolute;
            left: 0;
            bottom: 8px;
            -o-transition: all 1s;
            -moz-transition: all 1s;
            -webkit-transition: all 1s;
            transition: all 1s;
            min-height:0px;
        }
        .select-container .selectCity .selectTitle .contentBox .content > div {
            width: 454px;
            height: 180px;
            padding-top: 5px;
            display: inline-block;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentLeft {
            background-color: white;
            border-right: none;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentLeft > a {
            font-size: 12px;
            text-decoration: none;
            padding: 2px 4px;
            border: 1px solid white;
            float: left;
            margin: 5px;
            border-radius: 2px;
            color: #666;
            cursor: pointer;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentLeft > a:hover {
            border: 1px solid #ff7800;
            background-color: #ff7800;
            color: #fff;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentRight {
            background-color: white;
            margin-left: 2px;
            border-left: none;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentRight > a {
            font-size: 12px;
            text-decoration: none;
            padding: 2px 4px;
            border: 1px solid white;
            float: left;
            margin: 5px;
            border-radius: 2px;
            color: #666;
            cursor: pointer;
        }
        .select-container .selectCity .selectTitle .contentBox .content .contentRight > a:hover {
            border: 1px solid #ff7800;
            background-color: #ff7800;
            color: #fff;
        }
        .select-container .resLoc {
            width: 178px;
            height: 36px;
            display: inline-block;
            margin-left: 20px;
        }
        .select-container .resLoc #conLoc {
            width: 178px;
            height: 36px;
            line-height: 36px;
            text-indent: 10px;
            color: #666;
            font-size: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            cursor: pointer;
            outline: none;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        .select-container .handle {
            display: inline-block;
            width: 180px;
            height: 36px;
            margin-left: 15px;
        }
        .select-container .handle > button {
            font-size: 14px;
            color: #ff7800;
            margin: 0 5px;
            border: none;
            outline: none;
            cursor: pointer;
            width: 50px;
            text-align: center;
            height: 36px;
            background-color: transparent;
        }
        .ui-dialog-button>button:nth-of-type(2){
            margin: 10px 0 0 35px;
            width: 180px;
            float: left;
        }

    </style>
    <script type=text/javascript>
        if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i)){
            location.href = $conf.domain+'mobile/';
        }
    </script>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>
    new head(null);
</script>
<?php
$db = new DBHelperi_huanpeng();
$userCertifyStatus = get_userCertifyStatus($_COOKIE['_uid'], $db);
?>
<div class="container">
    <?php include WEBSITE_PERSON . 'sidebar_center.php'; ?>
    <div class="content">
        <div id="personal">
            <div class="basic">
                <?php include WEBSITE_PERSON . 'mp/pdetail.html.php' ?>
                <div class="pblockdiv">
                    <div class="pblock">
                        <?php include WEBSITE_PERSON . 'mp/titleLink.html.php'; ?>
                        <div class="list cur">
                            <div class="form-horizontal mt-20 personalInfos">
                                <div id="p_unick" class="control-group">
                                    <div class="control-label">
                                        <span class="icon personal_icon userIcon"></span>
                                        <span class="label">昵称 : </span>
                                    </div>
                                    <!-- 银行卡绑定暂时隐藏 -->
                                    <div class="controls" >
                                        <input class="mr-20" type="text" placeholder="请输入银行卡号" style="width: 220px;visibility: hidden;">
                                        <span class="option" style="display: none;">保存</span>
                                    </div>
                                </div>
                                <div id="p_usex" class="control-group">
                                    <div class="control-label">
                                        <span class="icon personal_icon sexIcon"></span>
                                        <span class="label">性别 : </span>
                                    </div>
                                    <div class="controls">
                                        <span class="sex mr-20 left">男</span>
                                        <span class="sex left">女</span>

                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="clear mt-10" style="border-top: 1px dotted #dfe2e6;"></div>
                                <div id="p_umail" class="control-group">
                                    <div class="control-label">
                                        <span class="icon personal_icon mailIcon"></span>
                                        <span class="label">邮箱认证 : </span>
                                    </div>
                                    <div class="controls">
                                        <span class="identifyDetail mt-12 mr-20 left">您认证的邮箱为</span>
                                        <span class="option mt-12 left">修改</span>

                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div id="p_uphone" class="control-group ">
                                    <div class="control-label">
                                        <span class="icon personal_icon phoneIcon"></span>
                                        <span class="label">手机认证 :</span>
                                    </div>
                                    <div class="controls">
                                        <span class="identifyDetail mt-12 mr-20 left">您认证的邮箱为</span>
                                        <span class="option mt-12 left">立即认证</span>

                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="control-group" id="anchorAddr">
                                    <div class="control-label">
                                        <span class="icon personal_icon locaIcon" style="background-position: -216px -102px;"></span>
                                        <span class="label">地址信息 :</span>
                                    </div>
                                    <div class="controls">

                                    </div>
                                </div>
<!--                                <div id="p_urealname" class="control-group">-->
<!--                                    <div class="control-label">-->
<!--                                        <span class="icon personal_icon realNameIcon"></span>-->
<!--                                        <span class="label">实名认证 :</span>-->
<!---->
<!--                                        <div class="clear"></div>-->
<!--                                    </div>-->
<!--                                    <div class="controls">-->
<!--                                        <span class="identifyDetail mt-12 mr-20 left"></span>-->
<!--                                        <span class="option mt-12 left">请先认证</span>-->
<!---->
<!--                                        <div class="clear"></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div id="p_ubankcard" class="control-group">-->
<!--                                    <div class="control-label">-->
<!--                                        <span class="icon personal_icon bankCardIcon"></span>-->
<!--                                        <span class="label">银行卡认证 :</span>-->
<!--                                    </div>-->
<!--                                    <div class="controls">-->
<!--                                        <span class="identifyDetail mt-12 mr-20 left">请先验证</span>-->
<!--                                        <span class="option mt-12 left">立即认证</span>-->
<!---->
<!--                                        <div class="clear"></div>-->
<!--                                    </div>-->
<!--                                </div>-->
                                <div class="clear mt-10" style="border-top: 1px dotted #dfe2e6;"></div>
                                <div class="control-group three-side-bind">
                                    <div class="control-label">社交账号</div>
                                    <div class="controls">
                                        <div class="weibo-bind bind-body">
                                            <img src="<?php echo STATIC_IMG_PATH.'threeParty/60-weibo-gray.png'; ?>" alt="">
                                            <div class="option">
                                                <p class="nick"></p>
                                                <a class="todo"></a>
                                            </div>
                                        </div>
                                        <div class="qq-bind bind-body">
                                            <img src="<?php echo STATIC_IMG_PATH.'threeParty/60-qq-gray.png'; ?>" alt="">
                                            <div class="option">
                                                <p class="nick"></p>
                                                <a class="todo"></a>
                                            </div>
                                        </div>
                                        <div class="wechat-bind bind-body">
                                            <img src="<?php echo STATIC_IMG_PATH.'threeParty/60-wechat-gray.png'; ?>" alt="">
                                            <div class="option">
                                                <p class="nick"></p>
                                                <a class="todo"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<script src="<?php echo STATIC_JS_PATH; ?>personal.js?v=1.0.4"></script>
<script>

</script>
</body>
</html>
