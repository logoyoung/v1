<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/19
 * Time: 下午5:20
 */

include_once '../../../include/init.php';
include_once WEBSITE_PERSON . "isLogin.php";
include_once WEBSITE_PERSON."isAnchor.php";

$year = date('Y');
$month = date('m');

if((int)$_GET['year'] >= 2016 and (int)$_GET['year'] <= date('Y')){
	$year == (int)$_GET['year'];
}
if((int)$_GET['month'] and (int)$_GET['month'] > 0 and (int)$_GET['month'] <= 12){
	if(!($year == date('Y') and $_GET['month'] > date('m'))) {
		$month = $_GET['month'];
	}
}

for($i = 2016; $i <= date('Y'); $i++){
	$yearlist[] = $i;
}
for($i = 1; $i <= date('m'); $i++){
	$monthlist[] = $i < 10 ? "0$i" : $i;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>我的收益-欢朋直播-精彩手游直播平台！</title>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
	<link rel="stylesheet" type="text/css" href="<?php echo __CSS__;?>person.css?v=1.0.5">
	<link rel="stylesheet" type="text/css" href="property.css?v=1.0.4"/>

</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="property">
            <div class="page-title">
            	<span>我的收益</span>
            	<!--<a href="#" class="tx_record"><i class="exchange-icon"></i><span>提现记录</span></a>-->
            </div>
			<!--<div class="withdraw-record right">
				<span class="personal_icon"></span>
				<span>兑换纪录</span>
			</div>-->
            <div class="property-top-block">
				<div class="my-balance form-horizontal">
					<p class="my-balance-title">收益余额</p>
					<div class="control-group total-benefit-coin total-benefit-group mt-20">
						<div class="control-label">
							<span class="personal_icon hpcoin"></span>
							<span>金币:</span>
						</div>
						<div class="controls total-benefit-coin-num" style="width: 150px;"></div>
					</div>
					<div class="control-group total-benefit-bean total-benefit-group mt-20">
						<div class="control-label">
							<span class="personal_icon hpbean"></span>
							<span>金豆:</span>
						</div>
						<div class="controls total-benefit-bean-num" style="width: 150px;"></div>
						<!--<a href="javascript:;" class="exchange_btn">兑换</a>-->
					</div>
					<!--<div class="total-benefit-time mt-10">
						<span>本月直播:</span>
						<span class="total-live-time"><i></i>小时</span>
						<span>|</span>
						<span>奖励 <i class="time-reward-coin"></i>金豆</span>
					</div>-->
                    <div class="clear"></div>
					<p class="today-benefit-title mt-30">本月收益</p>
					<div class="today-benefit mt-15">
						<div class="control-group today-benefit-coin left mr-50">
							<div class="control-label left mr-10">
								<span class="personal_icon hpcoin"></span>
								<span>金币:</span>
							</div>
							<div class="today-benefit-coin-num left"></div>
<!--							<div class="platform-reward left">1000</div>-->
						</div>
						<div class="control-group today-benefit-bean left">
							<div class="control-label left mr-10">
								<span class="personal_icon hpbean "></span>
								<span>金豆:</span>
							</div>
							<div class="today-benefit-bean-num left"></div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="total-benefit-time mt-10">
						<p>平台奖励&nbsp;<span id="platfrom-ratio" style="color: #20b64a;">20%</span>&nbsp;的金币，已加入每笔收益</p>
					</div>
					<div id="basicSalary" class="total-benefit-time mt-10 none">
						<p class="today-benefit-title mt-30">平台签约主播</p>
						<p style="line-height: 3em">本月底薪：&nbsp;&nbsp;<span id="basicSalaryNum" style="color: #ff7800;font-size:16px; ">123</span>&nbsp;人民币</p>
					</div>
				</div>
				<div class="other-desc-div">
					<div class="property-option">
						<a href="exchange.php" id="exchange-hpbean" class="btn">兑换欢朋币</a>
						<button id="withdraw" class="btn">申请提现</button>
<!--						<div class="withdraw-record right">-->
<!--							<span class="personal_icon"></span>-->
<!--							<span>兑换纪录</span>-->
<!--						</div>-->
					</div>
					<div class="withdraw-rule-explain mt-60">
						<p>提示：</p>
						<p>金豆可以兑换为金币用于提现，兑换比例： 1 金豆 = 1 金币；</p>
						<p>金币可以兑换为欢朋币，兑换比例： 1 金币 = 10 欢朋币；</p>
						<p>金币可在每月固定日期（25日～月底）可提现1次，本月没兑换可累积到下月。</p>
<!--						<p>注：首次提现将在2017年04月05日开启。</p>-->

<!--						<p class="mt-14">2.金币和金豆总额超过800元才可以兑换，切欢朋金豆必须 >= 100 金豆;礼物收益 10欢朋币＝1金币;欢朋豆受益10000豆=1金豆;</p>-->

<!--						<p class="mt-14">3.两个金币可提现1元，2个金豆可提现一元</p>-->
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
				<div class="benefit-table-div coin-table">
					<div  class="today-benefit-display">今日收益：&nbsp;<span id="today-coin"></span>&nbsp;金币</div>
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
<!--							<td>2016-01-30 10:33:33</td>-->
<!--							<td>奥拓不是四个圈</td>-->
<!--							<td><img src="../../static/img/gift/hpbean.png" alt=""/>滑板x100</td>-->
<!--							<td>7,000</td>-->
						</tr>
						</tbody>
					</table>
				</div>
				<div class="benefit-table-div bean-table none" style="margin-bottom: 30px;">
					<div class="today-benefit-display">今日收益：&nbsp;<span id="today-bean"></span>&nbsp;金豆</div>
					<table class="benefit-table table">
						<thead>
						<tr>
							<th class="th-time">时间</th>
							<th class="th-user">赠送人</th>
							<th class="th-gift">礼物和数量</th>
							<th class="th-coin">获得金豆</th>
						</tr>
						</thead>
						<tbody>
						<tr>
<!--							<td>2016-01-30 10:33:33</td>-->
<!--							<td>奥拓不是四个圈</td>-->
<!--							<td><img src="../../static/img/gift/hpbean.png" alt=""/>滑板x100</td>-->
<!--							<td>7,000</td>-->
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="pageIndex"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script>
    personalCenter_sidebar('property');

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
			//定义个人财产信息
			var requestUrl = $conf.api + 'property/api_myProperty.php';
			var requestData = {
				uid:getCookie('_uid'),
				encpass:getCookie('_enc')
			};
			ajaxRequest({url:requestUrl,data:requestData},function (d) {
				/*$('.total-benefit-coin-num').text(digitsFormat(d.coin,false));
				$('.total-benefit-bean-num').text(digitsFormat(d.bean,false));
				$('.today-benefit-coin-num').text(digitsFormat(d.monthCoin,false));
				$('.today-benefit-bean-num').text(digitsFormat(d.monthBean,false));
				$('.platform-reward').text('+'+d.reward);
				$('#platfrom-ratio').text(d.ratio);
				$('#today-coin').text(digitsFormat(d.todayCoin,false));
				$('#today-bean').text(digitsFormat(d.todayBean,false));*/
				$('.total-benefit-coin-num').text(parseFloat(d.coin).toFixed(2));
				$('.total-benefit-bean-num').text(parseFloat(d.bean).toFixed(2));
				$('.today-benefit-coin-num').text(parseFloat(d.monthCoin).toFixed(2));
				$('.today-benefit-bean-num').text(parseFloat(d.monthBean).toFixed(2));
//				$('.platform-reward').text('+'+parseFloat(d.reward).toFixed(1));
				$('#platfrom-ratio').text(d.ratio);
				$('#today-coin').text(d.todayCoin);
				$('#today-bean').text(d.todayBean);
				/*if(parseInt(d.basicSalary)>0){
					$('#basicSalary').css('display','block');
					$('#basicSalaryNum').text(d.basicSalary);
				}*/

			})
		}()

		var img = {
			31:'../../static/img/gift/gift-1.png',
			32:'../../static/img/gift/gift-2.png',
			33:'../../static/img/gift/gift-3.png',
			34:'../../static/img/gift/gift-4.png',
			35:'../../static/img/gift/gift-5.png',
		}
		var size = 10;
		var type = 0;
		var receiveRecordUrl = $conf.api + 'property/api_receiveRecord.php';

		!function(){


			function pageCallBackFunction(page){
				var year = $('#selectyeasr').val();
				var month = $('#selectmonth').val();
				var getType = type == 0 ? 'coin':'bean';
				var requestUrl = receiveRecordUrl;
				var requestData = {
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					year:year,
					month:month,
					page:page,
					size:size,
					type:getType
				};
				ajaxRequest({url:requestUrl,data:requestData},function(d){
					initRecordList(d.list);
				});
			}

			function initPageCode(allCount){
				if(allCount > size){
					var pageCount = parseInt(allCount / size);
					if(allCount % size != 0){
						pageCount += 1;
					}
					$('.pageIndex').remove();
					$('#property').append('<div class="pageIndex"></div>');
					$('.pageIndex').createPage({
						pageCount:pageCount,
						backFn:function(page){
							pageCallBackFunction(page);
						}
					});
				}else{
					$('.pageIndex').remove();
				}
			}

			function initRecordList(d){
				var htmlStr = '';
				for(var i in d){
					htmlStr += '<tr><td>'+d[i].date+'</td><td>'+d[i].nick+'('+ d[i].uid+')</td> <td><img src="'+img[d[i].giftid]+'" alt=""/>'+d[i].giftName+'x'+d[i].giftNum+'</td> <td>'+d[i].benefit+'</td> </tr>';
				}
				$('.benefit-table-div').eq(type).find('table tbody').html(htmlStr);
			}

			function initRecord(d){
				initRecordList(d.list);
				initPageCode(d.total);
			}

			function requestReceiveRecord(){
				var year = $("#selectyear").val();
				var month = $('#selectmonth').val();
				var getType = type==0? 'coin' : 'bean';
				var requestUrl = receiveRecordUrl;
				var requestData = {
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					year:year,
					month:month,
					page:1,
					size:size,
					type:getType
				};
				ajaxRequest({url:requestUrl,data:requestData},function (d) {
					initRecord(d);
				});
			}

			requestReceiveRecord();
			$('.select_tab li').click(function(){
				$('.select_tab li').removeClass('selected');
				$('.benefit-table-div').addClass('none');

				var i = $(this).index();
				type = i;
				$(this).addClass('selected');
				$('.benefit-table-div').eq(i).removeClass('none');
				requestReceiveRecord();
			});

			$('#query-submit').bind('click', function(){
				requestReceiveRecord();
			});
			$('#withdraw').bind('click', function(){
				tips('请在欢朋移动端进行提现');
				return;
                var rqUrl = $conf.api + 'user/revise/cashAdvance.php';
                var rqData = {uid:getCookie('_uid'),encpass:getCookie('_enc')};
                ajaxRequest({url:rqUrl,data:rqData},function (responseData) {
                    //location.href = 'withdraw.php';
                },function (responseData) {
                    if(responseData.code == '-4108'){
                        tips('欢朋移动端提现，6月1日开启');
                    }else if(responseData.code == '-4107'){
                        tips('兑换6月1日正式启用');
                    }else if(responseData.code == '-4097'){
                        tips('经纪公司主播不支持提现和兑换');
                    }

                })
			});
			$('.withdraw-record').bind('click', function(){
//				location.href =  'withdrawRecord.php';
			});
		}();
	});
</script>
</html>