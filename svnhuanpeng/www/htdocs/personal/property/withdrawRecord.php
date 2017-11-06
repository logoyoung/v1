<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/23
 * Time: 下午4:05
 */

include '../../../include/init.php';
include_once WEBSITE_PERSON."isAnchor.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__;?>person.css?v=1.0.5">
	<link rel="stylesheet" type="text/css" href="property.css?v=1.0.4"/>

	<script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<style>
		#withdrawRecord{
			padding: 0px 20px;
		}
		#withdrawRecord .withdraw-record{
			margin-top: 35px;
		}
		#withdrawRecord .withdraw-record-table thead tr{
			height: 40px;
		}
		#withdrawRecord .withdraw-record-table thead tr th{
			text-align: left;
			font-size: 16px;
			color: #333333;
			border: 1px solid #e0e0e0;
			font-weight: 200;
			padding-left:30px
		}
		#withdrawRecord .withdraw-record-table tbody tr{
			font-size: 14px;
			height: 60px;
			color: #666666;
		}
		#withdrawRecord .withdraw-record-table tbody tr td{
			text-align: left;
			padding-left: 30px;
			border: 1px solid #e0e0e0;
			font-size: 14px;
		}
		#withdrawRecord .withdraw-record-table tbody tr td span{
			float: left;
			margin-right: 4px;
			width: 20px;
			height: 20px;
		}
		#withdrawRecord .withdraw-record-table tbody .loading .money{
			color: #666666;
			font-weight: bold;
		}
		#withdrawRecord .withdraw-record-table tbody .loading .status{
			color: #333333;
		}
		#withdrawRecord .withdraw-record-table tbody .finished .money{
			color: #FF7800;
			font-weight: bold;
		}
		#withdrawRecord .withdraw-record-table tbody .finished .status{
			color: #333333;
		}
		#withdrawRecord .withdraw-record-table tbody .loading .status .personal_icon{
			background-position: -275px -24px;
		}
		#withdrawRecord .withdraw-record-table tbody .finished .status .personal_icon {
			background-position:-252px -24px;
		}
	</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="withdrawRecord">
			<p class="page-title">
				<a href="index.php">我的收益</a>
				<span class="sub-title">></span>
				<span class="sub-title">提现纪录</span>
			</p>
			<div class="row-fluid withdraw-record">
				<table class="withdraw-record-table table">
					<thead>
						<tr>
							<th>流水号</th>
							<th>时间</th>
							<th>提取详情</th>
							<th>提取金额(元)</th>
							<th>状态</th>
						</tr>
					</thead>
					<tbody>
                    <!--						<tr class="loading">-->
<!--							<td>123123</td>-->
<!--							<td>2015-07-06</td>-->
<!--							<td>200+133</td>-->
<!--							<td class="money">9000</td>-->
<!--							<td class="status"><span class="personal_icon"></span>处理中</td>-->
<!--						</tr>-->
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script type="text/javascript">
    personalCenter_sidebar('property');
	$(document).ready(function(){
		function initRecordTable(list){
			var htmlstr = '';
			for(var i in list){
				if(list[i].status == 0)
					htmlstr += '<tr class="loading">';
				else
					htmlstr += '<tr class="finished">';

				htmlstr += '<td>'+list[i].id+'</td>' +'<td>'+list[i].ctime+'</td>';
				htmlstr += '<td>'+list[i].coin+'金币+'+list[i].bean+'金豆</td>';
				htmlstr += '<td class="money">'+list[i].money+'</td>';
				if(list[i].status == 0)
					htmlstr += '<td class="status"><span class="personal_icon"></span>处理中</td>';
				else
					htmlstr += '<td class="status"><span class="personal_icon"></span>已完成</td>';

				htmlstr += '</tr>';
			}

			$('.withdraw-record-table tbody').html(htmlstr);
		}
		$.ajax({
			url:'api_withdrawRecord.php',
			type:"post",
			dataType:'json',
			data:{
				uid:getCookie('_uid'),
				encpass:getCookie('_enc')
			},
			success:function(d){
				if(d.recordList){
					var list = d.recordList;
					initRecordTable(list);
				}
			}
		});
	});
</script>
</html>