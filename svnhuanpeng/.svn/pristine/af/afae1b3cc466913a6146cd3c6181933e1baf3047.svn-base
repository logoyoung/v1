<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 16/11/28
 * Time: 17:28
 */
include '../module/checkLogin.php';

define('ADMIN_MODULE','../module/');
define('__ADMIN_STATIC__','../common/');
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8"/>
	<title>站内信列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>
	<style type="text/css">
		.verticalAlign{ vertical-align:middle; display:inline-block; height:100%; margin-left:-1px;}
		.xcConfirm .xc_layer{position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #666666; opacity: 0.5; z-index: 2147000000;}
		.xcConfirm .popBox{position: fixed; left: 50%; top: 50%; background-color: #ffffff; z-index: 2147000001; width: 570px; height: 300px; margin-left: -285px; margin-top: -150px; border-radius: 5px; font-weight: bold; color: #535e66;}
		.xcConfirm .popBox .ttBox{height: 30px; line-height: 30px; padding: 30px 30px; border-bottom: solid 1px #eef0f1;}
		.xcConfirm .popBox .ttBox .tt{font-size: 18px; display: block; float: left; height: 30px; position: relative;}
		.xcConfirm .popBox .ttBox .clsBtn{display: block; cursor: pointer; width: 12px; height: 12px; position: absolute; top: 22px; right: 30px; background: url(../common/global/img/icons.png) -48px -96px no-repeat;}
		.xcConfirm .popBox .txtBox{margin: 40px 100px; height: 100px; overflow: hidden;}
		.xcConfirm .popBox .txtBox .bigIcon{float: left; margin-right: 20px; width: 48px; height: 48px; background-image: url(../common/global/img/icons.png); background-repeat: no-repeat; background-position: 48px 0;}
		.xcConfirm .popBox .txtBox p{ height: 84px; margin-top: 16px; line-height: 26px; overflow-x: hidden; overflow-y: auto;}
		.xcConfirm .popBox .txtBox p input{width: 364px; height: 30px; border: solid 1px #eef0f1; font-size: 18px; margin-top: 6px;}
		.xcConfirm .popBox .btnArea{border-top: solid 1px #eef0f1;}
		.xcConfirm .popBox .btnGroup{float: right;}
		.xcConfirm .popBox .btnGroup .sgBtn{margin-top: 14px; margin-right: 10px;}
		.xcConfirm .popBox .sgBtn{display: block; cursor: pointer; float: left; width: 95px; height: 35px; line-height: 35px; text-align: center; color: #FFFFFF; border-radius: 5px;}
		.xcConfirm .popBox .sgBtn.ok{background-color: #ff7800; color: #FFFFFF;}
		.xcConfirm .popBox .sgBtn.cancel{background-color: #a0a0a0; color: #FFFFFF;}
		.table th, .table td {
			text-align: center;
			vertical-align: middle!important;
		}
	</style>
	<?php include '../module/mainStyle.php'; ?>
	<link href="../common/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>
	<link href="../common/admin/pages/css/userHead.css" rel="stylesheet" type="text/css"/>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square content_body  page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container">
	<?php include '../module/sidebar.php'; ?>
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box bg-yellow-gold" id="newsRecommend">
						<div class="portlet-title">
							<div class="caption">
								<h4 class="page-title">昵称审核</h4>
							</div>
							<div class="tools">
							</div>
						</div>
						<div class="portlet-body inner">

							<div class="tabbable">
								<ul class="nav nav-tabs">
									<li class="active"><a href="news" data-toggle="tab" aria-expanded="true" @click="changeView(0)">焦点推荐</a></li>
									<li class=""><a href="events" data-toggle="tab" aria-expanded="true" @click="changeView(1)">活动与新闻</a></li>

									<div class="option-bar pull-right">
										<a class="btn" @click="create">新建</a>
									</div>
								</ul>
								<div class="tab-content no-space" >
									<recommend :is-show.sync="isShow" :view-page="viewPage" :dialog-opt-type.sync="dialogOptType"></recommend>
								</div>
							</div>

						</div>
					</div>
					<!-- END PORTLET-->
				</div>
			</div>
		</div>
		<?php include '../module/footer.php'; ?>
		<?php include '../module/mainScript.php'; ?>
		<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
		<script type="text/javascript" src="../common/global/plugins/xcConfirm.js"></script>
		<script type="text/javascript" src="../common/global/scripts/common.js" ></script>
		<script type="text/javascript" src="../common/admin/pages/scripts/newsRecommend.js"></script>
		<script type="text/javascript">
		</script>
</body>
</html>
