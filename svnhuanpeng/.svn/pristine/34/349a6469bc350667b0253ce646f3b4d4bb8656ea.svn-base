<?php
$realpath = realpath(__DIR__)."/";
include '../../../../include/init.php';
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
    <title>我的收益-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
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

        #property{
            padding:0 20px;
        }
        #property .balanceDiv{
            line-height: 34px;
            font-size: 18px;
        }
        #property .balanceDiv .balance{
            color: #f44336;
        }
        #property .property_opt .btn{
            border-radius: 4px;
            border-style: solid;
            font-size: 14px;
        }
        #property .property_opt .btn#widthdraw{
            width: 94px;
            border-color: #f44336;
            background-color: #f44336;
        }
        #property .property_opt .btn#reckoning{
            width: 70px;
            background-color: #ffcdd2;
            border-color: #ffcdd2;
            color: #333333;

        }
        #property .select_tab{
            float: none;
            border-bottom: 1px solid #e0e0e0;
        }
        #property .select_tab li.rule{
            float: right;
            margin-right: -10px;
            font-size: 14px;
            height: 30px;
            line-height: 30px;
            margin-top: 15px;
        }
        #property .select_tab li.rule .personal_icon{
            width: 20px;
            height: 20px;
            float: left;
            background-position: -158px -5px;
            margin-top: 5px; 
            margin-right:-20px;
        }

        #property .detail{
            line-height: 42px;
        }
        #property .btngroup .convert{
            float: left;
            border:1px solid #cecece;
            background-color: #ffffff;
            color: #333333;
            border-radius: 3px;
        }
		#property .btngroup .convert:hover{
			background-color:#03a9f4;
			border-color: #03a9f4;
			color:#FFFFFF;
		}
        #property .selectcon{
            padding: 13px 10px;
            font-size: 14px;
            border-radius: 3px;
            border:1px solid #cccccc;
            position: relative;
            cursor: pointer;
        }
        .selectcon .name{
            margin: 0px;
            text-align: center;
        }
        .selectcon .arrow_bt, .selectcon .arrow_up{
            float: right;
            position: absolute;
            right: 0px;
            top: 13px;
        }
        #property .propertyrank .outrank {
            height: 40px;
            background-color: #cccccc;
            border:1px solid #cccccc;
            line-height: 40px;
            font-size: 14px;
            text-align: center;
        }
        #property .propertyrank .outrank .num{
            width: 50px;
            float: left;
            color: #333333;
            font-weight: bolder;
        }
        #property .propertyrank .outrank .nick{
            width: 176px;
            float: left;
            color:#333333;
        }
        #property .propertyrank .rankOne .label{
            font-size: 20px;
            color: #03a9f4;
            margin-left: 20px;
        }
        #property .propertyrank .rankOne .table{
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .rankOne .table thead tr{
            height: 40px;
            background-color: #e1f5fe;
            border:1px solid #cccccc;
        }
        .rankOne .table tbody tr{
            height: 40px;
            background-color: #fbfbfb;
        }
        .rankOne .table tbody td{
            padding: 10px 0px;
            line-height: 20px;
        }
        .rankOne .table tbody td.num{
            width: 40px;
        }
        .rankOne .table tbody td.nick{
            width: 140px;
            font-size: 14px;
            overflow: hidden;
            text-overflow:ellipsis;
        }
        .rankOne .table tbody td.num span{
            width: 20px;
            height: 20px;
            display: block;
            margin-left: 15px;
            background-color: #cccccc;
            color: #ffffff;
            line-height: 20px;
            border-radius: 3px;
        }

        .getcointab .table thead tr{
            height: 40px;
            border-style: solid none;
            border-width: 1px;
            border-color: #cccccc;
        }
        .getcointab .table tbody td.num{
            width: 120px;
        }
        .getcointab .table tbody td.date{
            width: 240px;
        }
        .getcointab .table tbody td.gcoin{
            width: 250px;
        }
        #exchange{
            border-radius: 3px;
            width: 118px;
            height: 42px;
            background-color: #f44336;
            border-color: #f44336;
            font-size: 14px;
            margin-left: 105px;
        }
		#withdraw_submit{
			border-radius: 3px;
			width: 118px;
			height: 42px;
			background-color: #f44336;
			border-color: #f44336;
			font-size: 14px;
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
		.propertyDetail{
			margin-bottom: 40px;
		}
		.propertyDetail .control-group{
			margin-top: 20px;
		}
		.propertyDetail .control-group .control-label{
			width: 115px;
			float: left;
			text-align: right;
		}
		.propertyDetail .control-group .controls{
			margin-left: 136px;
		}
		.propertyDetail .anchor_icon{
			float: left;
			width: 20px;
			height: 20px;
		}
		.propertyDetail .levelBarDiv{
			height: 4px;
			float: left;
			width: 120px;
			margin-top: 10px;
			background-color: #e0e0e0;
			border-radius: 4px;
		}
		.propertyDetail .levelBarDiv .levelBar{
			height: 4px;
			width: 46%;
			background-color: #03a9f4;
			border-radius: 4px;
			padding: 0px;
			float: left;
			font-size: 13px;
		}
		.propertyDetail .controls span{
			color: #999999;
		}
		.propertyDetail .controls span.myHpbean, .propertyDetail .controls span.myHpcoin{
			color: #f44336;
		}
		.sub_title ul{
			float: none;
			border-bottom: 1px solid #e0e0e0;
		}
		.sub_title li{
			height: 45px;
			font-size:16px;
			color: #29b0f5;
			width: 100px;
			border-bottom: 2px solid #03a9f4;
			text-align: center;
			line-height: 50px;
		}

    </style>
</head>
<body>
<?php  include $realpath.'../../header.html'; ?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
    <div class="content">
        <div id="property">
            <div class="row-fluid mt-30">
                <div class="left balanceDiv">
                    <span>我的余额</span>
                    <span class="balance">0.00元</span>
                </div>
                <div class="right property_opt">
                    <button id="widthdraw" class="btn">申请提现</button>
                    <a href="bill.php"><button id="reckoning" class="btn">账单</button></a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="row-fluid">
                <ul class="select_tab" style="margin-top: 20px">
<!--                    <li class="selected">排名</li>-->
                    <li class="selected">财产概况</li>
                    <li class="rule"><span class="personal_icon "></span>主播规则</li>
                    <div class="clear"></div>
                </ul>
            </div>
            <div class="row-fluid">
                <div class="tab_con hpCoin ">
					<div class="row-fluid propertyDetail">
						<div class="control-group">
							<div class="control-label">当前等级:</div>
							<div class="controls">
								<span class="anchor_icon lv1"></span>
								<div class="levelBarDiv">
									<strong class="levelBar"></strong>
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">欢朋币:</div>
							<div class="controls">
								<span class="myHpcoin  mr-30">123123</span>
								<span class="mr-10">本月直播:</span>
								<span class="mr-10">0.00小时</span>
								<span class="mr-10">|</span>
								<span>奖励 <a class="fc_red">18</a>欢朋币</span>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">欢豆:</div>
							<div class="controls">
								<span class="myHpbean">123123</span>
							</div>
						</div>
					</div>
					<div class="row-fluid sub_title">
						<ul>
							<li>欢朋币</li>
							<div class="clear"></div>
						</ul>
					</div>
                    <div class="row-fluid mt-10" style="padding: 0px 10px;">
                        <div class="detail left">
                            <span class="mr-10"><a class="fc_red">0</a>欢朋币可兑换</span>
                            <span class="mr-10">|</span>
                            <span>累计<a class="fc_red">0.00</a>欢朋币</span>
                        </div>
                        <div class="btngroup right mr-10">
                            <div id="convertbtn" class="btn convert">兑换</div>
                            <a href="record.php" class="btn convert">兑换纪录</a>
                        </div>
                         <div class="clear"></div>
                    </div>
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
                    <div class="row-fluid mt-20">
                        <div class="getcointab">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>日期</th>
                                        <th></th>
                                        <th>获得欢朋币</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab_con ruleCon none"></div>
            </div>
        </div>
    </div>
	<div class="clear"></div>
</div>

<!-- <div id="noticeBox" style="position:fixed;left:50%;top:100px;z-index: 1000;">
    <div class="theBox" style="padding: 26px 16px">
    </div>
</div> -->
</body>
<script type="text/javascript">
	var page=new page();
	page.init();
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
	personalCenter_sidebar('property');
    $('.select_tab li').click(function(){
        $('.select_tab li').removeClass('selected');
        $('.tab_con').addClass('none');

        var i = $(this).index();

        $(this).addClass('selected');
        $('.tab_con').eq(i).removeClass('none');

    });
    (function(){
        $("#convertbtn").bind('click',function(){
            function noticeBoxHtml(d){
                function noticeHead(){
                    var htmlstr = '';
                    
                    htmlstr += '<div class="box_head">';
                    htmlstr += '<p class="title left">兑换欢朋币</p>';
                    htmlstr += '<div class="closeBox">';
                    htmlstr += '<span class="personal_icon close"></span>';
                    htmlstr += '<div class="clear"></div>';
                    htmlstr += '</div>'
                    htmlstr += '</div>'
                    return htmlstr ;
                }
                function noticeBody(){
                    var htmlstr ='';
                    htmlstr += '<div class="box_body mt-40" style="padding: 0px 20px; margin-bottom: 30px;">';
                    htmlstr += '<p style="text-align: left;" class="clear">当前可兑换的欢朋币 <a class="hpb fc_red">' + d.balance + '</a>个，转换人民币 <a class="fc_red money">' + d.rmb + '</a>元</p>';
                    htmlstr += '<div class="mt-40">';
                    htmlstr += '<button class="btn left" id="exchange">兑换</button>';
                    htmlstr += '<a class="left fc_red" style="font-size: 14px; line-height: 40px; margin-left: 10px;">兑换欢朋币要大雨 2000 哦～</a>';
                    htmlstr += '<div class="clear"></div>   ';
                    htmlstr += '</div>'
                    htmlstr += '<div class="clear"></div>'
                    htmlstr += '</div>'
                    return htmlstr;
                }
                function noticeFoot(){
                    var htmlstr = '';
                    htmlstr += '<div class="box_foot" style="border-top: 1px solid #e0e0e0; padding: 0px 10px;">';
                    htmlstr += '<p class="title mt-20" style="font-weight: bold;">'
                    htmlstr += '<span class="personal_icon"  style="float: left;margin-top: -5px;margin-right: 5px;background-position:-10px -139px;"></span>'
                    htmlstr += '温馨提示';
                    htmlstr += '</p>';
                    htmlstr += '<p class="fc_red">1.每月只能兑换一次欢朋币</p>';
                    htmlstr += '<p>2.兑换的的比例为2000币＝1元，主播获得的欢朋币全部可以换算成现金提现！</p>';
                    htmlstr += '</div>';

                    return htmlstr;
                }
                var htmlstr = "";
                htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:100px;z-index: 1000;">';
                htmlstr += 'va<div class="theBox" style="padding: 26px 16px">';
                htmlstr = htmlstr + noticeHead() + noticeBody() + noticeFoot() + '</div></div>';
                
                return htmlstr;
            }
			function exchangeRmb(hpcoin){
				$.ajax({
					url:'exchangeRmb_ajax.php',
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						hpcoin:hpcoin
					},
					success:function(d){
						if(d.isSuccess == 1){
							alert('兑换成功');
						}
					}
				});
			}
			$.ajax({
				url:'primaryExchange_ajax.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc')
				},
				success:function(d){
					if(d.balance == 0){
						alert('你没有可兑换的欢朋币');
					}else if(d.balance > 0){
						NoticeBox.create(noticeBoxHtml(d));
						$('#noticeBox .close').bind('click', NoticeBox.remove);
						$('#exchange').bind('click', function(){
							exchangeRmb(d.balance);
						});
					}else if(d.code == -11){
						alert('您本月已经兑换过');
					}
				}
			})

        });
    }());

	(function(){
		//获取欢朋币
		function initIncomeList(d){
			$('.getcointab .table tbody tr').remove();
			var htmlstr = '';
			for(var i in d){
				htmlstr += '<tr> <td class="num">'+ (parseInt(i) + 1) +'</td> <td class="date">'+d[i].time+'</td> <td class="blank"></td> <td class="gcoin">'+d[i].income+'</td> </tr>';
			}
			$('.getcointab .table tbody').html(htmlstr);
		}
		function getIncomeRecord(y, m){
			$.ajax({
				url:'shamApi_getIncome_ajax.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					year:y,
					month:m
				},
				success:function(d){
					if(d.incomeList)
						initIncomeList(d.incomeList);
				}
			});
		}
		var year = $('#selectyear').val() || 0;
		var month = $('#selectmonth').val() || 0;
		getIncomeRecord(year, month);

		$('#query_submit').bind('click', function(){
			var year = $('#selectyear').val() || 0;
			var month = $('#selectmonth').val() || 0;

			getIncomeRecord(year, month);
		})
	}());

	(function(){

		function withdraw_submit(anchorCash){
			var cash = parseInt($('#cashValue').val()) || 0;
			if(!cash || cash > anchorCash)
				alert('您输入的金额有误');

			$.ajax({
				url:'withdrawCash_ajax.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					cash:cash
				},
				success:function(d){
					if(d.isSuccess == 1){
						NoticeBox.remove();
						alert('申请提现成功');
					}
				}
			})
		}

		$('#widthdraw').bind('click', function(){
			$.ajax({
				url:'priWithdrawCash_ajax.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc')
				},
				success:function(d){
					console.log(d);
					NoticeBox.create(withdrawNoticeBoxHtml(d));
					$('#noticeBox .close').bind('click', NoticeBox.remove);
					$('#withdraw_submit').bind('click', function(){
						withdraw_submit(d.anchorCash);
					});
				}
			})
		});


		function withdrawNoticeBoxHtml(d){
			function noticeHead(){
				var htmlstr = '';

				htmlstr += '<div class="box_head">';
				htmlstr += '<p class="title left">申请体现</p>';
				htmlstr += '<div class="closeBox">';
				htmlstr += '<span class="personal_icon close"></span>';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</div>'
				htmlstr += '</div>'
				return htmlstr ;
			}
			function noticeBody(){
				var htmlstr ='';
				htmlstr += '<div class="box_body mt-40" style="padding: 0px 20px; margin-bottom: 30px;">';
				htmlstr += '<div class="form-horizontal">';
				htmlstr += '<div class="control-group">';
				htmlstr += '<div class="control-label" style="padding-top: 3px;width:160px;">可提现余额:</div>';
				htmlstr += '<div class="controls" style="margin-left: 180px;">';
				htmlstr += '<p class="fc_red" style="padding-top: 10px; text-align: left;">¥'+ d.anchorCash+'</p>';
				htmlstr += '</div>';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</div>';
				htmlstr += '<div class="control-group">';
				htmlstr += '<div class="control-label" style="padding-top: 3px;width:160px;">提现金额(元):</div>';
				htmlstr += '<div class="controls" style="margin-left: 180px;">';
				htmlstr += '<input class="w-160 mr-20 left" id="cashValue" type="input" placeholder="请输入金额" style="padding: 13px 10px;font-size: 14px;border-radius: 3px;border: 1px solid #cccccc;" />';
				htmlstr += '</div>';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</div>';
				htmlstr += '<div class="control-group">';
				htmlstr += '<div class="controls" style="margin-left: 180px;">';
				htmlstr += '<button class="btn left" id="withdraw_submit">申请提交</button>';
				htmlstr += '</div>';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</div>';
				htmlstr += '</div>';
				htmlstr += '</div>'
				return htmlstr;
			}
			function noticeFoot(){
				var htmlstr = '';
				htmlstr += '<div class="box_foot" style="border-top: 1px solid #e0e0e0; padding: 0px 10px;">';
				htmlstr += '<p class="title mt-20" style="font-weight: bold;">'
				htmlstr += '<span class="personal_icon"  style="float: left;margin-top: -5px;margin-right: 5px;background-position:-10px -139px;"></span>'
				htmlstr += '温馨提示';
				htmlstr += '</p>';
				htmlstr += '<p class="fc_red">1.每月1-5号提供提现申请，其他日期不可与提现</p>';
				htmlstr += '<p>2.兑换的的比例为2000币＝1元，主播获得的欢朋币全部可以换算成现金提现！</p>';
				htmlstr += '</div>';

				return htmlstr;
			}
			var htmlstr = "";
			htmlstr += '<div id="noticeBox" style="position:fixed;left:50%;top:100px;z-index: 1000;">';
			htmlstr += '<div class="theBox" style="padding: 26px 16px;height:auto;">';
			htmlstr = htmlstr + noticeHead() + noticeBody() + noticeFoot() + '</div></div>';

			return htmlstr;
		}

	}());
});
</script>
</html>