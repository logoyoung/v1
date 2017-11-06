<?php
require_once('../../../include/init.php');
include_once WEBSITE_PERSON . "isLogin.php";

$db = new DBHelperi_huanpeng();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>我的关注-欢朋直播-精彩手游直播平台！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__; ?>person.css?v=1.0.5">
    <style type="text/css">
        body {
            background-color: #eeeeee;
        }

        /*.container{
            margin:auto;

            width: 1180px;
            display: block;
            height: 820px;
            background-color: #eeeeee;
            margin-top: 35px;
            box-shadow: 2px 2px 2px #cbcbcb;
        }*/
        /*.content{
            float:right;
            width: 948px;
            height:100%;
            margin-left: 2px;
            background-color: #ffffff;
        }*/

        #ensureDelete {
            margin-left: 120px;
            background-color: #f44336;
            border-color: #f44336;
        }
    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>
    new head(null);
</script>
<?php
$userCertifyStatus = get_userCertifyStatus($_COOKIE['_uid'], $db);
?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div class="follow_opt">
            <ul class="select_tab follow_tab left">
                <li class="selected">关注</li>
                <!--				<li>观看历史</li>-->
                <li>收藏</li>
            </ul>
            <button id="deleteCollect" class="btn none">编辑</button>
            <div class="deleteBtngroup none">
                <button id="deleteFinish" class="btn">确认</button>
                <button id="deleteCancel" class="btn">取消</button>
            </div>
        </div>
        <div class="follow_info">
            <div class="tabCon follow_list">
                <div class="followListContainer">

                </div>
            </div>
            <!--			<div class="tabCon historyList none"></div>-->
            <div class="tabCon videoList none">
                <div class="videoListContainer">

                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<!-- <div id="noticeBox" style="position:fixed;left:50%;top:320px;z-index: 1000;">
	<div class="theBox" style="padding: 26px 16px">
		<div class="box_head">
			<div class="closeBox">
				<span class='personal_icon close'></span>
				<div class="clear"></div>
			</div>
		</div>
		<div class="box_body">
			<div class="imgLogo"></div>
			<p>真的要删除这些收藏么？</p>
		</div>
		<div class="box_foot">
			<button id="ensureDelete" class="btn">确认删除</button>
			<button class="btn closeBox">关闭</button>
		</div>
	</div>
</div> -->
</body>
<!--<script src="http://dev.huanpeng.com/main/static/js/personal.js"></script>-->
<script src="follow.js?v=1.0.5"></script>
<script type="text/javascript">
var $conf = conf.getConf();
personalCenter_sidebar('follow');
$(document).ready(function () {
    Follow.init();
    return;
});
</script>
</html>