<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/21
 * Time: 下午5:06
 */
include_once '../../../include/init.php';
include_once WEBSITE_PERSON."isAnchor.php";
include_once INCLUDE_DIR.'Anchor.class.php';
$db = new DBHelperi_huanpeng();

$uid = $_COOKIE['_uid'];
$enc = $_COOKIE['_enc'];

$anchor = new AnchorHelp($uid, $db);

if(!$uid || !$enc || $anchor->checkStateError($enc)){
	exit(-1);
}

$page = 'withdraw';
//验证主播状态以及银行卡认证状态
if(!$anchor->isAnchor()){
	exit('您还不是主播');
}

$bank = $anchor->getBankCertifyInfo();

if($bank['status'] != BANK_CERT_PASS){
	$page = 'bank:notCertify';
	if($bank['status'] == BANK_CERT_NOT ){
		$page = 'bank:notBind';
	}
}elseif($anchor->isWithdrawed()){
	$withdrawInfo = $anchor->currentWithdrawInfo();
	if($withdrawInfo['status'] == 0){
		$page = 'withdraw:success';
	}else{
		$page = 'withdraw:finish';
	}
}else{
	$page = 'withdraw';
	$property['coin'] = $anchor->getCoin();
	$property['bean'] = $anchor->getBean();
	$bankName = $anchor->getCertifyBankName();
	$array = array(
		'icbc'=>'工商银行',
		'ccb'=>'建设银行',
		'abc'=>'农业银行',
		'bcm'=>'交通银行',
		'boc'=>'中国银行',
		'cmbc'=>'民生银行',
		'cmb'=>'招商银行',
		'psbc'=>'中国邮政储蓄'
	);
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo __CSS__;?>person.css?v=1.0.5">
	<link rel="stylesheet" type="text/css" href="property.css?v=1.0.4"/>
	<style>
		#noticeBox .read-box{
			padding: 20px;
			width: 466px;
			background-color: #ffffff;
			margin-left: -193px;
			box-shadow: 1px 1px 10px #cbcbcb;
			-o-box-shadow: 1px 1px 10px #cbcbcb;
			-webkit-box-shadow: 1px 1px 10px #cbcbcb;
			overflow-y:scroll;
		}
		#noticeBox .read-box .box_head p.title{
			font-size: 18px;
			margin: 10px 0px 10px 10px;
		}
		#noticeBox .read-box .box_head .closeBox .personal_icon{
			width: 20px;
			height: 20px;
			cursor:pointer;
		}
		#noticeBox .read-box .box_body.rule-detail {
			padding: 0px 20px;
			height: 504px;

		}
		#noticeBox .read-box .rule-detail p.top-title{
			font-size: 16px;
			font-weight: bold;
			color: #666666;
			margin-bottom: 20px;
			margin-top: 30px;
		}
		#noticeBox .read-box .rule-detail p.sub-title{
			font-size: 13px;
			color: #666666;
		}
		#noticeBox .read-box .rule-detail p.sub-title .fc-orange {
			color: #FF7800;
		}
	</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="withdrawPage">
			<p class="page-title">
				<a href="index.php">我的收益</a>
				<span class="sub-title">></span>
				<span class="sub-title">申请提现</span>
			</p>
			<div class="withdraw-page-notice">
				<span class="notice-logo">
					<img src="" alt=""/>
				</span>
				<div class="notice-words">
					<p>提现申请已经提交请耐心等待</p>
					<i>预计到账时间4月20号24点前到账</i>
				</div>
				<div class="clear"></div>
				<a href="index.php" class="btn">返回我的收益</a>
				<div class="clear"></div>
			</div>
			<div class="withdraw-page form-horizontal mt-60">
				<div class="control-group bank-info">
					<div class="control-label">已绑定银行卡:</div>
					<div class="controls">
						<span class="bank_icon <?php echo $bankName;?>"></span>
						<span class="bank-name mr-30"><?php echo $array[$bankName]?></span>
						<span>尾号</span>
						<span class="bank-card-no"><?php echo "**".substr($bank['bank'], -4);?></span>
					</div>
				</div>
				<div class="control-group balance-info mt-50">
					<div class="control-label">账户余额:</div>
					<div class="controls">
						<span class="personal_icon hpcoin"></span>
						<span class="mr-20">金币:</span>
						<span class="coin-num num" style="margin-right: 86px"><?php echo (float)$property['coin']; ?></span>
						<span class="personal_icon hpbean"></span>
						<span class="mr-20">金豆:</span>
						<span class="bean-num num"><?php echo (float)$property['bean']; ?></span>
						<div class="clear"></div>
					</div>
				</div>
				<div class="control-group withdraw-input mt-40">
					<div class="control-label">提取金额:</div>
					<div class="controls">
						<input id="withdraw-coin" type="text"/>
						<span class="mr-20 ml-20">+</span>
						<input id="withdraw-bean" type="text"/>
						<p class="mt-15">2 个金币可提现1 元， 2 个金豆可提现一元；金币和金豆总额超过800元才可兑换，金豆数量需要大>= 100金豆</p>
					</div>
				</div>
				<div class="control-group total-money mt-50">
					<div class="control-label">共计:</div>
					<div class="controls">
						<span class="RMB-integer">0</span>
						<span class="dot">.</span>
						<span class="RMB-float">00</span>
						<i>元</i>
						<em>(一周内到账)</em>
					</div>
				</div>
				<div class="withdraw-rule control-group mt-50">
					<div class="controls">
						<div class="agree-rule left">
							<label class="check-box checked" for="">
								<input id="checkbox-rule" type="checkbox"  checked="checked" class="none"/>
							</label>
							<a href="javascript:;" class="mr-20">我已阅读并同意</a>
						</div>
						<a href="javascript:;">提现规则</a>
					</div>
				</div>
				<div class="control-group mt-60">
					<div class="controls">
						<button id="withdraw-commit" class="btn">提交</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<!--	<div class="read-box">-->
<!--		<div class="box_head">-->
<!--			<p class="title left">提现规则</p>-->
<!--			<div class="closeBox">-->
<!--				<span class="personal_icon close right"></span>-->
<!--				<div class="clear"></div>-->
<!--			</div>-->
<!--			<div class="clear"></div>-->
<!--		</div>-->
<!--		<div class="box_body rule-detail">-->
<!--			<p class="top-title">一、主播排名规则</p>-->
<!--			<p class="sub-title">根据每天的直播时长、在线观众人数、互动弹幕数量、礼物赠送数量、粉丝关注数等综合因素进行排名，排名前300的主播可获得当日对应奖励。 </p>-->
<!--			<p class="top-title">二、排名奖励规则</p>-->
<!--			<p class="sub-title">1、奖励以半小时为最小单位进行计算，不满半小时将不计算奖励。</p>-->
<!--			<p class="sub-title">2、每日奖励发放上限时间为4小时，主播排名计算不受此4小时限制。</p>-->
<!--			<p class="sub-title">3、如当日被巡管发现违规并执行禁播操作，则无法获得当日排名奖励。</p>-->
<!--			<p class="sub-title">4、<span class="fc-orange">日薪超过50元一小时的主播，如未与官方签约，排名奖金将按照50%计算。</span></p>-->
<!--			<p class="top-title">三、奖励发放规则</p>-->
<!--			<p class="sub-title">1、待工作人员审核后，该日的奖励 <span class="fc-orange">（当日所有分类收入取单分类最大值计为当日奖励）</span>进入个人账户余额，可进行申请提现操作。每个自然月工作人员将会进行一次审核操作，在月末进行。</p>-->
<!--			<p class="sub-title">2、申请提现时间为每月1-5号，其余时间不可申请提现，最小提现金额为500元。提现申请在工作人员审核通过后，将在当月10号统一进行打款。</p>-->
<!--		</div>-->
<!--	</div>-->
</body>
<script type="text/javascript">
    personalCenter_sidebar('property');

	var page = "<?php echo $page; ?>";
	if(page == 'withdraw'){
		$('.withdraw-page').show();
		$('.withdraw-page-notice').remove();
	}else{
		$('.withdraw-page').remove();
		$('.withdraw-page-notice').show();

		if(page == 'withdraw:success'){
			$('.withdraw-page-notice .notice-logo').addClass('green');
			$('.withdraw-page-notice .notice-logo img').attr('src', '../../static/img/member/withdraw.png');
			$('.withdraw-page-notice .notice-words p').text('提现申请已经提交请耐心等待');

			var dateInfo = "<?php echo date('m-d', strtotime('+5 day',strtotime($withdrawInfo['ctime'])));?>";
			dateInfo = dateInfo.split('-');
			$('.withdraw-page-notice .notice-words i').text('预计到账时间'+parseInt(dateInfo[0])+'月'+parseInt(dateInfo[1])+'号24点前到账');
		}
		if(page == 'bank:notBind'){
			$('.withdraw-page-notice .notice-logo').addClass('red');
			$('.withdraw-page-notice .notice-logo img').attr('src', '../../static/img/member/bindBankCard.png');
			$('.withdraw-page-notice .notice-words p').text('您还未绑定银行卡,绑定后再来哦~').css('margin-top','10px');
			$('.withdraw-page-notice .notice-words i').text('');
			$('.withdraw-page-notice .btn').text('去绑定银行卡').attr('href','../mp/certify_bankcard/');
		}
		if(page == 'bank:notCertify'){
			$('.withdraw-page-notice .notice-logo').addClass('red');
			$('.withdraw-page-notice .notice-logo img').attr('src', '../../static/img/member/bindBankCard.png');
			$('.withdraw-page-notice .notice-words p').text('您的银行卡正在认证中，请稍等').css('margin-top','10px');
			$('.withdraw-page-notice .notice-words i').text('');
			$('.withdraw-page-notice .btn').text('返回我的收益').attr('href','index.php');
		}
		if(page == 'withdraw:finish'){

		}
		if(page == 'withdraw:notTime'){

		}
	}

	$('.agree-rule').click(function(){
		if($(this).find('.check-box').hasClass('checked')){
			$(this).find('.check-box').removeClass('checked');
			document.getElementById('checkbox-rule').checked = false;
		}else{
			$(this).find('.check-box').addClass('checked');
			document.getElementById('checkbox-rule').checked = true;
		}
	});
	!function(){
		var coinMoney = 0;
		var beanMoney = 0;
		var coin = parseInt($('.balance-info .coin-num').text());
		var bean = parseInt($('.balance-info .bean-num').text());

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

		function totalMoneyShow(){
			var total = parseFloat(coinMoney + beanMoney).toFixed(2);
			var money = total.toString().split('.');
			console.log(total);
			$('.total-money .RMB-integer').text(money[0]);
			$('.total-money .RMB-float').text(money[1]);
		}

		$('#withdraw-coin').on('input propertychange', function(){
			console.log('run ');
			if($(this).val() > coin){
				$(this).val(coin);
			}else{
				var len = $(this).val().length - 1;
				check_format_money($(this).val(), len, this);
			}

			coinMoney = parseInt($(this).val()) / 2 || 0;
			totalMoneyShow();
		});

		$("#withdraw-bean").on('input propertychange', function(){
			if($(this).val() > bean){
				$(this).val(bean);
			}else{
				var len = $(this).val().length -1;
				check_format_money($(this).val(), len, this);
			}
			beanMoney = parseInt($(this).val()) / 2 || 0;
			totalMoneyShow();
		});
		$('#withdraw-bean').blur(function(){
			var beanNum = parseInt($(this).val()) || 0;
			if(beanNum > 0 && beanNum < 100){
				alert('金豆提现数量必须大于100');
			}
		});

		$("#withdraw-commit").bind('click', function(){
			var coins = parseInt($('#withdraw-coin').val()) || 0;
			var beans = parseInt($('#withdraw-bean').val()) || 0;

			var totalMoney = beanMoney + coinMoney;
			if(totalMoney < 800){
				alert('提现总金额不能小于800');
				return;
			}
			if(bean && bean < 100){
				alert('金豆提现数量必须大于100');
				return;
			}
			if(!$('#checkbox-rule').is(':checked')){
				alert('服务条款为同意');
			}
			$.ajax({
				url:'api_withdraw.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					coin:coins,
					bean:beans
				},
				success:function(d){
					if(d.isSuccess == 1){
						location.href = location.href;
					}
				}
			});
		});

		$(".withdraw-rule .controls .agree-rule + a").bind('click', function(){
			function NoticeBoxHtml() {
				var htmlstr = '<div class="read-box"> <div class="box_head"> <p class="title left">提现规则</p> <div class="closeBox"> <span class="personal_icon close right"></span> <div class="clear"></div> </div> <div class="clear"></div> </div> <div class="box_body rule-detail"> <p class="top-title">一、主播排名规则</p> <p class="sub-title">根据每天的直播时长、在线观众人数、互动弹幕数量、礼物赠送数量、粉丝关注数等综合因素进行排名，排名前300的主播可获得当日对应奖励。 </p> <p class="top-title">二、排名奖励规则</p> <p class="sub-title">1、奖励以半小时为最小单位进行计算，不满半小时将不计算奖励。</p> <p class="sub-title">2、每日奖励发放上限时间为4小时，主播排名计算不受此4小时限制。</p> <p class="sub-title">3、如当日被巡管发现违规并执行禁播操作，则无法获得当日排名奖励。</p> <p class="sub-title">4、<span class="fc-orange">日薪超过50元一小时的主播，如未与官方签约，排名奖金将按照50%计算。</span></p> <p class="top-title">三、奖励发放规则</p> <p class="sub-title">1、待工作人员审核后，该日的奖励 <span class="fc-orange">（当日所有分类收入取单分类最大值计为当日奖励）</span>进入个人账户余额，可进行申请提现操作。每个自然月工作人员将会进行一次审核操作，在月末进行。</p> <p class="sub-title" style="margin-bottom: 58px;">2、申请提现时间为每月1-5号，其余时间不可申请提现，最小提现金额为500元。提现申请在工作人员审核通过后，将在当月10号统一进行打款。</p> </div> </div>';

				return htmlstr;
			}
			NoticeBox.create(NoticeBoxHtml);
			$('#noticeBox').css('top', '60px');
			$("#noticeBox .close").bind('click', NoticeBox.remove);
			$('#page_Mask').bind('click', NoticeBox.remove);

		});
	}();
</script>
</html>