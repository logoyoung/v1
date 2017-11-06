<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/30
 * Time: 下午3:57
 */
include '../../../include/init.php';
include WEBSITE_PERSON."isAnchor.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>兑换欢朋币-欢朋直播-精彩手游直播</title>
	<?php include WEBSITE_TPL."commSource.php"; ?>
	<link rel="stylesheet" href="../../static/css/person.css?v=1.0.5">
	<link rel="stylesheet" href="./property.css?v=1.0.4">
	<style>
		.exchange{
			position: relative;
		}
		.exchange .control-group{
			margin-bottom: 60px;
		}
		.exchange .control-label{
			float: left;
			width: 150px;
			color: #6e6e6e;
			font-size: 14px;
			padding-top:16px;
			text-align: right;
		}

		.exchange .controls{
			margin-left: 170px;
		}

		.exchange .hpbean-desc{
			margin-top:60px;
		}
		.exchange .hpbean-desc .control-group{
			float: left;
			width: 270px;
		}

		.exchange .hpbean-desc .controls{
			padding-top: 10px;
			font-size:20px;
			color: #ff7800;
		}


		.exchange .control-group.rule{
			line-height: 14px;
		}

		.exchange .control-group.rule .checkbox-div{
			cursor: pointer;

		}

		.exchange .control-group.rule label{
			margin-right:6px;
		}

		.exchange .control-group.rule a{
			color: #ff7800;
		}


		.money-select-div{
			padding-bottom: 1px;
			border-bottom: 1px dotted #e0e0e0;
			margin-bottom: 50px;
		}

		.money-select .controls span.money-select-one{
			float: left;
			margin-right:20px;
			width: 84px;
			height: 40px;
			border: 2px solid #e0e0e0;
			border-radius: 4px;
			text-align: center;
			line-height: 40px;
			position: relative;
			cursor:pointer;
		}

		.money-select .controls span.money-select-one .personal_icon{
			position: absolute;
			right: -2px;
			top: -1px;
			width: 20px;
			height: 20px;
		}

		.money-select .controls span.money-select-one.selected{
			border-color: #ff9e48;
		}

		.money-select .controls span.money-select-other{
			float: left;
			border: 2px solid #e0e0e0;
			border-radius:4px;
			position:relative;
		}

		.money-select .controls span.money-select-other.selected{
			border-color: #ff9e48;
		}

		.money-select .controls span.money-select-other .personal_icon{
			top: -1px;
			right: -2px;
			width: 20px;
			height: 20px;
			position: absolute;
		}
		.money-select .controls span.money-select-other input{
			margin-top: 0px;
			border:0px solid #e0e0e0;
			/*border: 4px;*/
			padding-right: 45px;
			width: 120px;
			outline: 0;
		}
		.money-select .controls span.money-select-other span{
			position: absolute;
			right: 18px;
			top: 11px;
			font-size: 14px;
		}

		.money-num-show .controls{
			padding-top:10px;
			color: #aaa;
		}

		.money-num-show .controls .coin-num{
			color: #ff7800;
			margin-right: 14px;
			font-size:20px;
		}


		.submit #commit{
			background: #ff7800;
			color: #fff;
			padding: 10px 36px;
			border-color: #ff7800;
			border-radius: 4px;
			font-size: 14px;
		}

		.page-title span{
			font-size: 16px;
			color: #666;
		}

	</style>
</head>
<body>
<?php include WEBSITE_MAIN.'head.php'; ?>
<script type="application/javascript">new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="property" class="exchange form-horizontal">
			<p class="page-title"><a href="index.php">我的收益</a><span>>兑换欢朋币</span></p>
			<!--<div class="withdraw-record right">
				<span class="personal_icon"></span>
				<span>兑换纪录</span>
			</div>-->
			<div class="hpbean-desc">
				<div class="control-group gold-coin">
					<div class="control-label">金币余额:</div>
					<div class="controls"></div>
				</div>
				<div class="control-group hp-coin">
					<div class="control-label">欢朋币余额:</div>
					<div class="controls"></div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="row-fluid money-select-div">
				<div class="control-group money-select">
					<div class="control-label">请选择兑换数额:</div>
					<div class="controls">
						<span class="money-select-one"><span class="num">30</span>金币</span>
						<span class="money-select-one"><span class="num">50</span>金币</span>
						<span class="money-select-one"><span class="num">100</span>金币</span>
						<span class="money-select-one"><span class="num">200</span>金币</span>
                        <span class="money-select-one"><span class="num">500</span>金币</span>
						<span class="money-select-other"><input type="text"></span>
						<div class="clear"></div>
					</div>
				</div>
				<div class="control-group money-num-show">
					<div class="control-label">兑换欢朋币:</div>
					<div class="controls">
						<span class="coin-num">100</span>
						<span class="recharge-desc">(兑换比例 1金币:10欢朋币)</span>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="row-fluid form-horizontal">
				<div class="control-group rule">
					<div class="controls">
						<div class="checkbox-div">
<!--							<label class="checkbox-label checked"></label>-->
							<span>兑换即同意</span>
							<a href="<?php echo WEB_ROOT_URL; ?>protocol/dh.php" target="_blank">《欢朋直播兑换协议》</a>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="control-group submit">
					<div class="controls">
						<buttion id="commit" class="btn">立即兑换</buttion>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<script type="application/javascript">
	function digitsFormat(num, float){
		var fixed = arguments[2] ? arguments[2] : 2;
		num = float ? parseFloat(num).toFixed(fixed) + '' : num + '';
		var tmp = num;
		var num = tmp.split('.')[0];
		var decimals = tmp.split('.')[1];
		//console.log(decimals);
		var out = num.length > 3 ? num.length % 3 : 0;
		var pre = num.slice(0, out);
		var num = num.slice(out);

		pre = out ? pre + ',' : '';
		decimals = float ? "." + decimals : '';
		//console.log(decimals);
		return pre + num.replace(/\d{1,3}(?=(\d{3})+(\.\d*)?)/g, '$&,') + decimals;
	}

	var exchange_obj;
	$(document).ready(function () {
		exchange_obj = {
			money:10,
			agree:true,
			max:0,
			init:function (max) {
				this.max = max;
				this.selectMoney();
				this.selectMoneyShow();
				this.agree();
				this.commit();
			},
			selectMoney:function(){
				$('.money-select .money-select-one').bind('click', function(){
					$('.money-select .money-select-one .personal_icon').remove();
					$('.money-select .money-select-other .personal_icon').remove();

					$('.money-select .money-select-one.selected').removeClass('selected');
					$('.money-select .money-select-other.selected').removeClass('selected');
					$('.money-select .money-select-other input').val('');

					$(this).addClass('selected').append('<span class="personal_icon sel"></span>');

					exchange_obj.money = parseInt($(this).text()) || 0;
					exchange_obj.selectMoneyShow();
				});
                $('.money-select .money-select-one').eq(0).click();

				$('.money-select .money-select-other input').focus(function(){
					$('.money-select .money-select-one .personal_icon').remove();
					$('.money-select .money-select-other .personal_icon').remove();

					$('.money-select .money-select-one.selected').removeClass('selected');
					$('.money-select .money-select-other.selected').removeClass('selected');

					if($(this).val()){
						exchange_obj.money = Number($(this).val());
					}else{
						exchange_obj.money = 0;
					}
					exchange_obj.selectMoneyShow();
					$(this).parent().addClass('selected').append('<span class="personal_icon sel"></span>');
				});

				$('.money-select .money-select-other input').bind('input propertychange', function(){

					if(Number($(this).val()) > Number(exchange_obj.max))
						$(this).val(exchange_obj.max);

					if($(this).val().length > exchange_obj.max.length){
						$(this).val($(this).val().substr(0,exchange_obj.max.length));
					}else{
						var len = $(this).val().length - 1;
						check_format_money($(this).val(), len, this);
					}

					exchange_obj.money = parseInt($(this).val()) || 0;
					exchange_obj.selectMoneyShow();

					function check_format_money(val, len, obj){
						var pre = new RegExp("[1-9]\\d{"+len+"}");
						if(len == 0){
							if(!pre.test(val)){
								$(obj).val('');
								return;
							}
						}
						if(len < 0) return;
						if(pre.test(val)){
							$(obj).val(val);
						}else{
							val = val.substr(0,len);
							len--;
							check_format_money(val, len, obj);
						}
					}
				});
				$('.money-select .money-select-other input').blur(function(){
					if(recharge_obj.money < 10 ){
                        tips('金额不能少于10元');
						return;
					}

					if(recharge_obj.money > 1000){
						tips('金额不能大于1000元');
						return;
					}
				});
			},
			selectMoneyShow:function(){
				var coin = exchange_obj.money * 10;
				$('.money-num-show .coin-num').text(digitsFormat(coin));
			},
			agree:function () {
//				var self = this;
//				$(".checkbox-div .checkbox-label, .checkbox-div span").click(function () {
//					if(self.agree){
//						$('.checkbox-div .checkbox-label').removeClass('checked');
//						self.agree = false;
//					}else{
//						$('.checkbox-div .checkbox-label').addClass('checked');
//						self.agree = true;
//					}
//				});
			},
			commit:function(){
				var self = this;
				$("#commit").click(function () {

					if(!self.money){
						alert('请输入有效金额');
					}

					var requestUrl = $conf.api + 'user/revise/coinToHpCoin.php';
					var requestData = {
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						number:Number(self.money)
					};

					ajaxRequest({url:requestUrl,data:requestData},function (responseData) {

						var coin = responseData.coin;
						var hpcoin = responseData.hpcoin;

						$('.gold-coin .controls').text(parseFloat(coin).toFixed(1));
						$('.hp-coin .controls').text(parseFloat(hpcoin).toFixed(1));

						exchange_obj.max = coin;
						tips('兑换成功');
					},function(responseData){
                        if(responseData.code == '-4108'){
                            tips('欢朋移动端提现，6月1日开启');
                        }else if(responseData.code == '-4107'){
                            tips('兑换6月1日正式启用');
                        }else if(responseData.code == '-4097'){
                            tips('经纪公司主播不支持提现和兑换');
                        }else if(responseData.code == '-5023'){
                            tips('您的账户余额不足!');
                        }else{
                            tips('兑换失败!');
                        }
					});
				});
			}
		};

		var requestUrl = $conf.api + 'property/api_myProperty.php';
		var requestData = {
			uid:getCookie('_uid'),
			encpass:getCookie('_enc'),
			type:1
		};
		ajaxRequest({url:requestUrl,data:requestData},function(responseData){
			var coin = responseData.coin;
			var hpcoin = responseData.hpcoin;

			$('.gold-coin .controls').text(parseFloat(coin).toFixed(1));
			$('.hp-coin .controls').text(parseFloat(hpcoin).toFixed(1));

			exchange_obj.init(coin);
		});
		$('.li-property').addClass('currentpage');
	});
</script>
</body>
</html>