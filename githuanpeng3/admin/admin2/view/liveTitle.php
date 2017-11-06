<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/11
 * Time: 下午5:23
 */
include '../module/checkLogin.php';

define('ADMIN_MODULE', '../module/');
define('__ADMIN_STATIC__','../common/');
?>

<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>直播管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include '../module/mainStyle.php'; ?>
<link href="../common/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>
<link href="../common/admin/pages/css/userHead.css" rel="stylesheet" type="text/css"/>

</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-sidebar-fixed">
<?php include '../module/head.php'; ?>
<div class="clearfix"></div>
<div class="page-container ">
	<?php include '../module/sidebar.php'; ?>
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="portlet box green vertify-portlet">
				<div class="portlet-title">
					<div class="caption">直播标题审核</div>
					<div class="inputs">
						<div class="portlet-input input-inline input-small">
							<div class="input-icon right">
								<i class="icon-magnifier"></i>
								<input type="text" class="form-control" placeholder="search...">
							</div>
						</div>
					</div>
				</div>
				<div class=" portlet-body col-md-12" >
					<div class="tabbable" id="titleCheckBody">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#wait" id="" data-toggle="tab" aria-expanded="true" @click="changeView(0)">待审核</a></li>
							<li><a href="#pass" id="" data-toggle="tab" aria-expanded="false" data-page="pass" @click="changeView(1)">已通过 </a></li>
							<li><a href="#unpass" id=""data-toggle="tab" aria-expanded="false" data-page="unpass" @click="changeView(2)">未通过</a></li>
						</ul>
						<div class="tab-content no-space" :is="currentView">
							<check></check>
							<pass></pass>
							<unpass></unpass>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include '../module/footer.php'; ?>
<?php include '../module/mainScript.php'; ?>
<script type="text/javascript" src="../common/global/plugins/jquery.form.js"></script>
<script type="text/javascript" src="../common/global/plugins/swfobject.js"></script>
<script type="text/javascript" src="../common/global/scripts/common.js"></script>
<script type="text/javascript" src="../common/admin/pages/scripts/liveTitle.js"></script>