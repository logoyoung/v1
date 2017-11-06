<?php
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();

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

function calTodaySendHpBean($uid, $db){
	$stime = date('Y-m-d')." 00:00:00";
	$etime = date('Y-m-d')." 23:59:59";

	$sql = "select sum(giftnum) as num from giftrecord where uid=$uid and ctime BETWEEN '$stime' and '$etime'";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	$num = (int)$row['num'];

	return $num * 100;
}
function calTodaySendHpCoin($uid, $db){
	$stime = date('Y-m-d')." 00:00:00";
	$etime = date('Y-m-d')." 23:59:59";

	$sql = "select giftid, giftnum from giftrecordcoin where uid=$uid and ctime BETWEEN '$stime' and '$etime'";
	$res = $db->query($sql);

	$sum = 0;
	while($row = $res->fetch_assoc()){
		$tmp = calGiftCost($row['giftid'], $row['giftnum'], $db);
		$sum += $tmp;
	}

	return $sum;
}
function calGiftCost($id, $num, $db){
	$sql = "select money from gift where id = $id";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return $num * (int)$row['money'];
}
?>


<html>
<head>
    <title></title>
    <meta charset='utf-8'>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <link rel="stylesheet" type="text/css" href="../../static/css/common.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/home.css?v=1.0.4">
    <link rel="stylesheet" type="text/css" href="../../static/css/person.css?v=1.0.5">
    <script type="text/javascript" src="../../static/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../static/js/common.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/page.js?v=1.0.4"></script>
	<script type="text/javascript" src="../../static/js/home_data_load.js?v=1.0.4"></script>
    <style type="text/css">
        body{
            background-color:#eeeeee;
        }
        #giftCon{
            padding: 0px 20px;
        }
        #giftCon .select_tab{
            float: none;
            border-bottom: 1px solid #e0e0e0;
        }
        #giftCon .optgroup .btn{
            border-radius: 3px;
            border-color: #03a9f4;
            background-color: #03a9f4;

            padding: 8px 16px;
            font-size: 14px;
        }
        #giftCon .table thead tr{
            height: 40px;
            border-top: 1px solid #cccccc;
            border-bottom: 1px solid #cccccc;
        }

        #giftCon .table tbody td img{
            width: 25px;
            height: 25px;
            vertical-align: middle;
            background-color: #f0f0f0;
            border:0px;
            border-radius: 3px;
            margin-right: 5px;
        }
		#query_submit{
			/*margin-left: 10px;*/
			background: #3b9eeb;
			border: 1px solid #2d96e8;
			border-radius: 3px;
			color: #fff;
			padding: 3px 10px;
			outline: none;
			cursor: pointer
		}
		#giftCon .select_tab{
			float: right;
		}
		#giftCon .select_tab li{
			background: 0;
			border: 1px solid #ddd;
			line-height: 35px;
			height: 35px;
			width: 70px;
			font-size: 14px;
			margin-right: 0;
		}
		#giftCon .select_tab li.selected{
			background-color:#3b9eeb;
			border:1px solid #3b9eeb;
			color: #fff;
		}
    </style>
</head>
<body>
<?php  include '../../header.html'; ?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
		<div class="row-fluid" style="margin-top: 20px;background: #f5f5f5;font-size: 16px;padding: 10px 0;line-height: 28px">
			<p style="margin: 0px 0px 0px 20px">送礼记录</p>
		</div>
        <div id="giftCon">

			<div class="row-fluid" style="margin-top: 0px">
				<p class="gift_des" style="font-size:14px; float: left;margin-top: 20px;margin-bottom: 0px;height: 35px;line-height: 35px;">
					今日赠送：
					<span style="color: #a3a3a3;padding-right: 2px;">欢朋豆</span>
					<em style="color: #ff8c00;font-size: 12px;padding-right: 12px;font-style: normal;"><?php echo calTodaySendHpBean($_COOKIE['_uid'], $db); ?>个</em>
					<span style="color: #a3a3a3;padding-right: 2px;">欢朋币</span>
					<em style="color: #ff8c00;font-size: 12px;padding-right: 12px;font-style: normal;"><?php echo calTodaySendHpCoin($_COOKIE['_uid'], $db); ?>个</em>
				</p>
				<ul class="select_tab" style="margin-top: 20px;">
					<li class="selected">欢朋豆</li>
					<li>欢朋币</li>
					<div class="clear"></div>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="mt-10 optgroup">
				<!--                <button class=" right btn">查询</button>-->
				<!--                <div class="clear"></div>-->
				<div class="row-fluid mt-20 right">
					<label for="">查询时间</label>
					<select name="" id="selectyear">
						<?php
						foreach($yearlist as $k => $v){
							echo"<option value=".$v.">".$v."</option>";
						}
						?>
					</select>
					<span style="float: none; margin:0 5px;">年</span>
					<select name="" id="selectmonth">
						<?php
						foreach($monthlist as $k => $v){
							echo"<option value=".$v.">".$v."</option>";
						}
						?>
					</select>
					<span style="float: none;margin: 0 5px;">月</span>
					<button id="query_submit">查询</button>
				</div>
				<div class="clear"></div>
			</div>
            <div class="tabConDiv mt-30">
                <div class="tab_con sendgift">
					<table class="table">
						<thead>
						<tr>
							<th>送礼时间</th>
							<th>礼物名称</th>
							<th>礼物数量</th>
							<th>房间号</th>
							<th>收礼人</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><a>2015.11.29</a><a style="margin-left: 20px;">10 : 38</a></td>
							<td><img src="">钻石</td>
							<td>6</td>
							<td>50</td>
							<td>这个杀手不太冷</td>
						</tr>
						</tbody>
					</table>
					<div class="pageIndex"></div>
                </div>
                <div class="tab_con recvgift none">
					<table class="table">
						<thead>
							<tr>
								<th>送礼时间</th>
								<th>礼物名称</th>
								<th>礼物数量</th>
								<th>礼物金额</th>
								<th>房间号</th>
								<th>收礼人</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><a>2015.11.29</a> <a style="margin-left: 20px;">10 : 38</a></td>
								<td><img src="">钻石</td>
								<td>6</td>
								<td>50</td>
								<td>0375</td>
								<td>这个杀手不太冷</td>
							</tr>
						</tbody>
					</table>
					<div class="pageIndex"></div>
                </div>
            </div>
        </div>
    </div>
	<div class="clear"></div>
</div>
</body>
<script type="text/javascript">
	var page=new page();
	page.init();

	personalCenter_sidebar('giftHistory');
	(function(){
		$("#selectyear").val("<?php echo $year; ?>");
		$('#selectmonth').val("<?php echo $month; ?>");

		$("#selectyear").change(function(){
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
	}());

    $(document).ready(function(){

		var img = {
			31:'../../static/img/gift/hpbean.png',
			32:'http://staticlive.douyutv.com/upload/dygift/0dc60e11063a7dd81b9f2bb213d0cfeb.png',
			33:'../../static/img/gift/diamond.png',
			34:'../../static/img/gift/motorcycle.png',
			35:'http://staticlive.douyutv.com/upload/dygift/447b61f6c0d6890d4490a90d0bdbf8bc.png'
		}

		var size = 15;

		function initHpBeanList(d){
			var htmlstr = '';
			for(var i in d){
				htmlstr += '<tr><td><a>'+d[i].date+'</a><a style="margin-left: 20px;">'+d[i].time+'</a></td><td><img src="../../static/img/gift/hpbean.png">欢朋豆</td> <td>'+d[i].giftnum+'</td> <td>'+d[i].luid+'</td> <td>'+d[i].anchorNick+'</td> </tr>';
			}
			$('.tabConDiv .tab_con.sendgift .table tbody').html(htmlstr);
		}
		function initHpCoinList(d){
			var htmlstr = '';

			for(var i in d){
				htmlstr += '<tr> <td><a>'+d[i].date+'</a> <a style="margin-left: 20px;">'+d[i].time+'</a></td><td><img src="'+img[d[i].giftid]+'">'+d[i].giftname+'</td> <td>'+d[i].giftnum+'</td> <td>'+d[i].giftcost+'</td> <td>'+d[i].luid+'</td> <td>'+d[i].anchorNick+'</td> </tr>';
			}
			$('.tabConDiv .tab_con.recvgift .table tbody').html(htmlstr);
		}
		function initHpBeanPageCode(allCount){
			if(allCount > size){
				var pageCount = parseInt(allCount / size);
				if(allCount % size != 0){
					pageCount += 1;
				}
				$('.tabConDiv .tab_con.sendgift .pageIndex').remove();
				$('.tabConDiv .tab_con.sendgift').append('<div class="pageIndex"></div>');
				$('.tabConDiv .tab_con.sendgift .pageIndex').createPage({
					pageCount:pageCount,
					backFn:function(page){
						pageCallBackFunction(page);
					}
				});
			}else{
				$('.tabConDiv .tab_con.sendgift .pageIndex').remove();
			}
			function pageCallBackFunction(page){
				var year = $("#selectyear").val();
				var month = $('#selectmonth').val();
				var url = 'sendGiftHistoryBean_ajax.php';

				$.ajax({
					url:url,
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						year:year,
						month:month,
						page:page
					},
					success:function(d){
						initHpBeanList(d.sendGiftRecord);
					}
				});
			}
		}
		function initHpCoinPageCode(allCount){
			if(allCount > size){
				var pageCount = parseInt(allCount / size);
				if(allCount % size != 0){
					pageCount += 1;
				}
				$('.tabConDiv .tab_con.recvgift .pageIndex').remove();
				$('.tabConDiv .tab_con.recvgift').append('<div class="pageIndex"></div>');
				$('.tabConDiv .tab_con.recvgift .pageIndex').createPage({
					pageCount:pageCount,
					backFn:function(page){
						pageCallBackFunction(page);
					}
				});
			}else{
				$('.tabConDiv .tab_con.recvgift .pageIndex').remove();
			}

			function pageCallBackFunction(page){
				var year = $("#selectyear").val();
				var month = $('#selectmonth').val();
				var url = 'sendGiftHistoryCoin_ajax.php';

				$.ajax({
					url:url,
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						year:year,
						month:month,
						page:page
					},
					success:function(d){
						initHpCoinList(d.sendGiftRecord);
					}
				});
			}
		}

		function dataHandelEvent(d, type){
			if(type == 'hp_coin'){
				initHpCoinList(d.sendGiftRecord)
				initHpCoinPageCode(d.allCount);
			}else{
				initHpBeanList(d.sendGiftRecord);
				initHpBeanPageCode(d.allCount);
			}
		}

		function getGiftHistoryRecord(){
			var year = $("#selectyear").val();
			var month = $('#selectmonth').val();
			var type = 'hp_bean';
			var url = 'sendGiftHistoryBean_ajax.php';
			if($('.select_tab li').eq(1).hasClass('selected')){
				type = 'hp_coin';
				url = 'sendGiftHistoryCoin_ajax.php';
			}

			$.ajax({
				url:url,
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					year:year,
					month:month
				},
				success:function(d){
					dataHandelEvent(d, type);
				}
			});
		};

        (function(){
			getGiftHistoryRecord();

            $('.select_tab li').click(function(){
                $('.select_tab li').removeClass('selected');
                $('.tabConDiv .tab_con').addClass('none');

                var i = $(this).index();

                $(this).addClass('selected');
                $('.tabConDiv .tab_con').eq(i).removeClass('none');
				getGiftHistoryRecord();
            })
			$('#query_submit').bind('click', function(){
				getGiftHistoryRecord();
			});
        }());

    })
</script>
</html>