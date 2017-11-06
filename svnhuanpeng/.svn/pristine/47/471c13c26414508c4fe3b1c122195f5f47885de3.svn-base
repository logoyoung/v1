<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 上午11:41
 */

date_default_timezone_set('Asia/Shanghai');

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
	<title></title>
	<meta charset="utf-8"/>
	<title>个人中心-欢朋直播-精彩手游直播平台！</title>
	<link rel="stylesheet" type="text/css" href="../../static/css/common.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="../../static/css/home.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.4">
	<link rel="stylesheet" type="text/css" href="property.css?v=1.0.4"/>

	<script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<style>
		body{
			background-color:#eeeeee;
		}
		#gift-record{
            padding:0px 20px;
		}
        #gift-record .gift-record-content{
            padding:0px 50px;
        }
		.table-option{
			border-bottom: 1px solid #e0e0e0;
			height: 57px;
		}
		.table-option .table-option-right{
			float: right;
		}
        .today-cost{
            margin-top:36px;
        }
        .today-cost span{
            float:left;
            font-size:14px;
			line-height: 24px;
        }
        .today-cost .personal_icon{
            width:20px;
            height:20px;
			margin-right: 10px;
        }
        .today-cost .label{
            font-size:16px;
			margin-right: 20px;
        }
        .today-cost .num{
            color:#ff7800;
			margin-left: 20px;
        }
		.coin-table {
			margin-top: 30px;
		}
		.coin-table thead th{
			height: 40px;
			text-align: left;
			padding-left: 30px;
			border: 1px solid #e0e0e0;
			font-size: 16px;
			font-weight: normal;
		}
		.coin-table tbody td{
			height: 60px;
			text-align: left;
			padding-left: 30px;
			border:1px solid #e0e0e0;
		}
		.coin-table tbody td img{
			vertical-align: middle;
			margin-right: 10px;
		}
	</style>
</head>
<body>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="gift-record">
			<p class="page-title">送礼记录</p>
			<div class="gift-record-content">
				<div class="row-fluid today-cost">
					<span class="label" for="">今日赠送</span>
					<span class="personal_icon hpcoin"></span>
					<span>欢朋币</span>
					<span class="coin-num num mr-50">123</span>
					<span class="personal_icon hpbean"></span>
					<span>欢朋豆</span>
					<span class="bean-num num">123</span>
					<div class="clear"></div>
				</div>
				<div class="table-option mt-45">
					<ul class="table-option-left select_tab left">
						<li class="selected">欢朋币</li>
						<li>欢朋豆</li>
					</ul>
					<div class="table-option-right select-time">
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
				<table class="coin-table table">
					<thead>
						<tr>
							<th>送礼时间</th>
							<th>收礼人</th>
							<th>礼物和数量</th>
							<th>房间号</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
				<table class="bean-table none">
					<thead>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
</body>
<script type="text/javascript">
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

	$(document).ready(function(){
		!function(){
			$.ajax({
				url:
			});
		}();
	});
</script>
</html>
