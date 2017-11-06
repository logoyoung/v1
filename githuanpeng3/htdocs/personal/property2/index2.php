<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/19
 * Time: 下午5:20
 */

//include "../../../../include/init.php";
//include INCLUDE_DIR.'Anchor.class.php';

$year = date('Y');
$month = date('m');

if((int)$_GET['year'] >= 2015 and (int)$_GET['year'] <= date('Y')){
	$year == (int)$_GET['year'];
}
if((int)$_GET['month'] and (int)$_GET['month'] > 0 and (int)$_GET['month'] <= 12){
	if(!($year == date('Y') and $_GET['month'] > date('m'))) {
		$month = $_GET['month'];
	}
}

for($i = 2015; $i <= date('Y'); $i++){
	$yearlist[] = $i;
}
for($i = 1; $i <= date('m'); $i++){
	$monthlist[] = $i < 10 ? "0$i" : $i;
}


?>

<html>
<head>
	<title>个人中心-欢朋直播-精彩手游直播平台！</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" type="text/css" href="../../static/css/common.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="../../static/css/home.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.4">
	<script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<style>
		body{
			background-color: #eeeeee;
		}
		#property{
			padding: 0px 20px;
		}
		#property .page-title{
			margin-top: 48px;
			font-size: 18px;
			border-bottom: 1px solid #e0e0e0;
			height: 40px;
			padding-left: 12px;
		}
		#property .property-top-block{
			padding: 0px 50px;
		}
		.my-balance{
			margin-top: 36px;
			width: 50%;
			float: left;
		}
		.my-balance .my-balance-title{
			font-size: 16px;
		}
		.my-balance .today-benefit-title{
			font-size: 14px;
		}
		.my-balance .control-group{
			margin-bottom: 20px;
		}

		.my-balance .control-group  .control-label{
			width: 70px;
			padding-top: 0px;
			font-size: 14px;
			color: #333333;
		}

		.my-balance .control-group  .controls{
			margin-left: 90px;
		}

		.my-balance .control-label span{
			float: left;
		}
		.my-balance .control-label .personal_icon{
			width: 20px;
			height: 20px;
			margin-right: 12px;
			margin-top: -2px;
		}
		.personal_icon.hpbean{
			background-position: -275px 1px;
		}
		.personal_icon.hpcoin{
			background-position: -251px 1px;
		}
		.my-balance .total-benefit-coin .total-benefit-time span:first-child{
			margin-left: 30px;
		}

		.my-balance .total-benefit-coin .total-benefit-coin-num ,.my-balance .total-benefit-bean .total-benefit-bean-num{
			font-size: 20px;
			color: #ff7800;
		}

		.my-balance .total-benefit-coin .control-label,.my-balance .total-benefit-bean .control-label{
			padding-top:6px;
		}

		.my-balance .today-benefit .control-label{
			padding-top: 3px;
		}

		.my-balance .today-benefit .today-benefit-coin-num, .my-balance .today-benefit .today-benefit-bean-num{
			font-size: 16px;
			color: #ff7800;
		}


		.other-desc-div{
			margin-left: 50%;
			width: 50%;
			margin-top: 36px;
		}

		.other-desc-div .property-option #withdraw{
			width: 80px;
			border-color: #ff7800;
			background-color:#ef6c00;
			border-radius: 4px;
		}

		.other-desc-div .property-option .withdraw-record{
			color:#ff7800;
			font-size: 14px;
			cursor:pointer;
		}

		.other-desc-div .property-option .personal_icon{
			width: 20px;
			height: 20px;
			background-position: -159px -25px;
			float: left;
			margin-right: 4px;
		}



		.benefit-record{
			padding: 0 50px;
		}
		.benefit-record .benefit-title{
			font-size: 16px;
		}
		.benefit-record .benefit-option{
			border-bottom: 1px solid #e0e0e0;
			height: 57px;
		}

		.benefit-record .benefit-option .select_tab{
			margin: 20px 0px 0px 20px;
		}
		.benefit-record .benefit-option .select_tab li{
			width: 76px;
			height: 36px;
			line-height: 36px;
			border: 1px solid #e0e0e0;
			font-size: 14px;
			border-radius: 6px 6px 0px 0px;
			margin-right: 12px;
			margin-left: 1px;
		}
		.benefit-record .benefit-option .select_tab li.selected{
			border-bottom: 0;
			border-color: #ff9e48;
			border-width: 2px;
			margin-right: 10px;
			margin-left: 0px;
			background-color:#fff;
			color:#ff9e48;
		}
		.benefit-record .benefit-option .select-time{
			float: right;
			margin-top: 25px;
			color: #333;
		}

		.benefit-record .benefit-option .select-time select {
			appearance: none;
			-moz-appearance: none;
			-webkit-appearance: none;
			padding: 3px 14px 3px 6px;
			min-width: 40px;
			outline: 0;
			background: url("http://dev.huanpeng.com/main/static/img/icon/select-arrow.png") no-repeat scroll right center transparent;
			background-position: 21px 4px;
		}
		.benefit-record .benefit-option .select-time select::-ms-expand {display: none;}

		.benefit-record .benefit-option .select-time #selectyear{
			width: 54px;
			background-position: 35px 4px;
		}

		.benefit-record .benefit-option .select-time #query-submit{
			background-color: #ff9e48;
			border: 1px solid #ff9e48;
			border-radius: 4px;
			color: #fff;
			outline: none;
			cursor: pointer;
			padding: 3px 10px;
		}

		.benefit-record .benefit-table-div{
			margin-top: 30px;
		}
		.benefit-record .benefit-table-div .table thead tr{
			height: 40px;
		}
		.benefit-record .benefit-table-div .table thead tr th{
			text-align: right;
			font-size: 16px;
			color: #333333;
			border: 1px solid #e0e0e0;
			font-weight: 200;
		}

		.benefit-record .benefit-table-div .table thead tr .th-time{
			padding-right: 115px;
			width: 60px;
			height: 40px;
		}
		.benefit-record .benefit-table-div .table thead tr .th-user{
			padding-right: 176px;
			width: 80px;
		}

		.benefit-record .benefit-table-div .table thead tr .th-gift{
			padding-right: 143px;
			width: 107px;
		}

		.benefit-record .benefit-table-div .table thead tr .th-coin{
			padding-right: 28px;
		}

		.benefit-record .benefit-table-div .table tbody tr{
			height: 60px;
			font-size: 14px;
			color: #666666;
		}

		.benefit-record .benefit-table-div .table tbody tr td{
			text-align: left;
			padding-left: 30px;
			border: 1px solid #e0e0e0;
		}
		.benefit-record .benefit-table-div .table tbody tr td img{
			width: 38px;
			height: 38px;
			vertical-align: middle;
			margin-right: 10px;
		}
	</style>
</head>
<body>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="property">
			<div class="property-top-block">
				<p class="page-title">我的收益</p>
				<div class="my-balance form-horizontal">
					<p class="my-balance-title">账号余额</p>
					<div class="control-group total-benefit-coin mt-20">
						<div class="control-label">
							<span class="personal_icon hpcoin"></span>
							<span>金币:</span>
						</div>
						<div class="controls total-benefit-coin-num">
							120,000
						</div>
						<div class="total-benefit-time mt-10">
							<span>本月直播:</span>
							<span class="total-live-time"><i></i>小时</span>
							<span>|</span>
							<span>奖励 <i class="time-reward-coin"></i>金豆</span>
						</div>
					</div>
					<div class="control-group total-benefit-bean">
						<div class="control-label">
							<span class="personal_icon hpbean"></span>
							<span>金豆:</span>
						</div>
						<div class="controls total-benefit-bean-num">
							45,000,600
						</div>
					</div>
					<p class="today-benefit-title mt-30">今日奖励</p>
					<div class="today-benefit mt-15">
						<div class="control-group today-benefit-coin left mr-50">
							<div class="control-label left mr-20">
								<span class="personal_icon hpcoin"></span>
								<span>金币:</span>
							</div>
							<div class="today-benefit-coin-num left">5000</div>
						</div>
						<div class="control-group today-benefit-bean left">
							<div class="control-label left mr-20">
								<span class="personal_icon hpbean "></span>
								<span>金豆:</span>
							</div>
							<div class="today-benefit-bean-num left">34,002</div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="other-desc-div">
					<div class="property-option">
						<button id="withdraw" class="btn">申请提现</button>
						<div class="withdraw-record right">
							<span class="personal_icon"></span>
							<span>提现纪录</span>
						</div>
					</div>
					<div class="withdraw-rule-explain mt-30">
						<p>说明</p>

						<p>1.欢朋金币，金豆在每月固定日期（15～18号）可提现一次，本月没兑换累积到下月</p>

						<p class="mt-20">2.金币和金豆总额超过800元才可以兑换，切欢朋金豆必须 >= 100 金豆;礼物收益 10欢朋币＝1金币;欢朋豆受益10000豆=1金豆;</p>

						<p class="mt-20">3.两个金币可提现1元，2个金豆可提现一元</p>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="row-fluid benefit-record mt-30">
				<p class="benefit-title">收益记录</p>
				<div class="benefit-option">
					<ul class="option-left select_tab">
						<li class="selected">金币</li>
						<li>金豆</li>
					</ul>
					<div class="option-right select-time">
						<label for="">查询时间</label>
						<select name="" id="selectyear">
							<?php
							foreach($yearlist as $k => $v){
								echo "<option value=".$v.">".$v."</option>";
							}
							?>
						</select>
						<span class="year-lab">年</span>
						<select name="" id="selectmonth">
							<?php
							foreach($monthlist as $k => $v){
								echo "<option value=".$v.">".$v."</option>";
							}
							?>
						</select>
						<span class="month-lab">月</span>
						<button id="query-submit">查询</button>
					</div>
					<div class="clear"></div>
				</div>
				<div class="benefit-table-div">
					<table class="benefit-table table">
						<thead>
							<tr>
								<th class="th-time">时间</th>
								<th class="th-user">赠送人</th>
								<th class="th-gift">礼物和数量</th>
								<th class="th-coin">获得金币</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>2016-01-30 10:33:33</td>
								<td>奥拓不是四个圈</td>
								<td><img src="../../static/img/gift/hpbean.png" alt=""/>滑板x100</td>
								<td>7,000</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="pageIndex"></div>
		</div>
	</div>
</div>
</body>
<script>
	!function(){
		$("#selectyear").val("<?php echo $year; ?>");
		$("#selectmonth").val("<?php echo $month; ?>");

		$('#selectyear').change(function(){
			var date = new Date();
			var year = $(this).val();
			var month = 12;
			if(year == date.getFullYear()){
				month = date.getMonth() + 1;
			}

			$('#selectmonth option').remove();

			for(var i = 1; i <= month; i++){
				var theMonth = i < 10 ? '0' + i : i;
				$('#selectmonth').append('<option value=' + theMonth + '>' + theMonth + '</option>');
			}

			$('#selectmonth').val(theMonth);
		});
	}();
</script>
</html>