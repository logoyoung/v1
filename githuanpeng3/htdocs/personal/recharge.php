
<?php
include '../../include/init.php';
include_once WEBSITE_PERSON."isLogin.php";
include_once WEBSITE_MAIN . "initCookie.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>充值-欢朋直播-精彩手游直播平台！</title>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <?php include WEBSITE_TPL.'commSource.php';?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH; ?>person.css?v=1.0.5">
    <style type="text/css">
		/*reset.css*/
		body, h1, h2, h3, h4, h5, h6, hr, p, blockquote, dl, dt, dd, ul, ol, li, pre, form, fieldset, legend, button, input, textarea, th, td { margin:0; padding:0; }
		img{margin: 0;padding: 0;}


		h1, h2, h3, h4, h5, h6{ font-size:100%; }
		address, cite, dfn, em, var { font-style:normal; }
		code, kbd, pre, samp { font-family:couriernew, courier, monospace; }
		small{ font-size:12px; }
		ul, ol { list-style:none; }
		a { text-decoration:none; }
		a:hover { text-decoration:none; }
		sup { vertical-align:text-top; }
		sub{ vertical-align:text-bottom; }
		legend { color:#000; }
		fieldset, img { border:0; }
		button, input, select, textarea { font-size:100%; }
		table { border-collapse:collapse; border-spacing:0; }
		/*万能清除浮动*/
		.clearfix:after {
			content: "." ;
			display: block ;
			height: 0 ;
			clear: both ;
			visibility: hidden ;
		}

		html .paySuccess,
		body .paySuccess,
		html .payLoading,
		body .payLoading,
		html .payFailure,
		body .payFailure {
			width: 240px;
			height: 138px;
			padding-top: 42px;
			z-index: 999;
			background-color: #ffffff;
			border-radius: 5px;
			box-shadow: 1px 1px 1px whitesmoke;
		}
		html .paySuccess .right,
		body .paySuccess .right,
		html .payLoading .right,
		body .payLoading .right,
		html .payFailure .right,
		body .payFailure .right,
		html .paySuccess .loading,
		body .paySuccess .loading,
		html .payLoading .loading,
		body .payLoading .loading,
		html .payFailure .loading,
		body .payFailure .loading,
		html .paySuccess .failure,
		body .paySuccess .failure,
		html .payLoading .failure,
		body .payLoading .failure,
		html .payFailure .failure,
		body .payFailure .failure {
			width: 48px;
			height: 48px;
			margin: 0 auto 22px;
			background: url("../static/img/recharge/right.png") no-repeat;
			background-size: 100% 100%;
			float: none;
		}
		html .paySuccess p,
		body .paySuccess p,
		html .payLoading p,
		body .payLoading p,
		html .payFailure p,
		body .payFailure p {
			width: 100%;
			text-align: center;
			font-size: 18px;
		}
		html .payLoading .loading,
		body .payLoading .loading {
			background: url("../static/img/recharge/loading.gif") no-repeat;
			background-size: 100% 100%;
		}
		html .payFailure .failure,
		body .payFailure .failure {
			background: url("../static/img/recharge/failure.png") no-repeat;
			background-size: 100% 100%;
		}
		html .wxScan,
		body .wxScan {
			width: 720px;
			height: 464px;
			background-color: #fff;
		}
		html .wxScan .scanContent,
		body .wxScan .scanContent {
			width: 100%;
			height: 464px;
		}
		html .wxScan .scanContent .contentLeft,
		body .wxScan .scanContent .contentLeft {
			width: 240px;
			height: 464px;
			float: left;
			margin-left: 80px;
		}
		html .wxScan .scanContent .contentLeft .leftMoney,
		body .wxScan .scanContent .contentLeft .leftMoney {
			width: 240px;
			height: 112px;
			line-height: 112px;
			font-weight: 600;
		}
		html .wxScan .scanContent .contentLeft .leftMoney p,
		body .wxScan .scanContent .contentLeft .leftMoney p {
			font-size: 15px;
			color: #b3b3b3;
		}
		html .wxScan .scanContent .contentLeft .leftMoney p .needPay,
		body .wxScan .scanContent .contentLeft .leftMoney p .needPay {
			color: #ff7f00;
			font-size: 25px;
			margin-left: 2px;
			display: inline-block;
			vertical-align: middle;
		}
		html .wxScan .scanContent .contentLeft .Qrcode,
		body .wxScan .scanContent .contentLeft .Qrcode {
			width: 240px;
			height: 240px;
			border: 1px solid whitesmoke;
			position: relative;
		}
		html .wxScan .scanContent .contentLeft .Qrcode .codeImg,
		body .wxScan .scanContent .contentLeft .Qrcode .codeImg {
			position: absolute;
			width: 204px;
			height: 204px;
			left: 50%;
			top: 50%;
			margin-left: -102px;
			margin-top: -102px;
			background-color: black;
		}
		html .wxScan .scanContent .contentLeft .wxDesc,
		body .wxScan .scanContent .contentLeft .wxDesc {
			margin-top: 24px;
		}
		html .wxScan .scanContent .contentLeft .wxDesc p,
		body .wxScan .scanContent .contentLeft .wxDesc p {
			text-indent: 30px;
			font-size: 13px;
			text-align: center;
			color: #929292;
			background: url("../static/img/recharge/scan-code.png") no-repeat 53px center;
			background-size: contain;
		}
		html .wxScan .scanContent .contentRight,
		body .wxScan .scanContent .contentRight {
			float: left;
			width: 290px;
			height: 416px;
			background: url("../static/img/recharge/phone.png") no-repeat;
			margin: 24px 0 0 72px;
		}

        body{
            background-color: #eeeeee;
        }
        #rechargeCon{
            padding: 48px 20px;
        }
        #rechargeCon .pagetitle{
			font-size: 18px;
			color: #333333;
			padding-left: 12px;
			padding-bottom: 12px;
			margin-bottom: 60px;
			border-bottom: 1px solid #e0e0e0;
        }
		#rechargeCon .form-horizontal .control-group{
			margin-bottom: 50px;
		}
		#rechargeCon .form-horizontal .control-label{
			float: left;
			width: 150px;
			text-align: right;
			color: #6e6e6e;
			font-size: 14px;
			padding-top: 16px;
		}

		#rechargeCon .form-horizontal .controls{
			margin-left: 170px;
		}

		.user-desc .controls{
			padding-top: 18px;
			font-size: 14px;
			line-height: 14px;
		}

		.user-desc .controls .username{
			margin-left: 20px;
		}

		.user-desc .controls .balanceDiv{
			margin-left: 70px;
		}

		.user-desc .controls .balanceDiv .balance{
			margin-left: 20px;
		}

		.user-desc .controls .balanceDiv .balance .num{
			color: #ff7800;
			margin-right: 10px;
		}
		.money-selected-div{
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

		.money-num-show .controls .coin-num{
			color: #ff7800;
			margin-right: 4px;
			font-size: 20px;

		}

		.money-num-show .controls{
			padding-top: 10px;
		}
		.money-num-show .controls .recharge-desc{
			color: #999999;
		}

		.pay-channel .controls .pay-channel-one{
			width: 140px;
			height: 52px;
			border: 2px solid #e0e0e0;
			margin-right: 20px;
			position: relative;
			border-radius: 4px;
			float: left;
			position: relative;
			cursor:pointer;
		}

		.pay-channel .controls .pay-channel-one .personal_icon{
			position: absolute;
			right: -2px;
			top: -1px;
			width: 20px;
			height: 20px;
		}

		.pay-channel .controls .pay-channel-one.selected{
			border-color: #ff9e48;
		}

		.bank-selected .bank-selected-ul li{
			float: left;
			margin-right: 20px;
			/*width: 144px;*/
			padding: 12px 11px 12px 11px;
			cursor: pointer;
			border: 1px solid #e0e0e0;
			margin-bottom: 20px;
			/*height: 44px;*/
			border-radius: 4px;
		}

		.bank-selected .bank-selected-ul li .checkbox-div{
			float: left;
			width: 12px;
			height: 12px;
			border-radius: 100%;
			position: relative;
			border: 1px solid #e0e0e0;
			margin-right: 10px;
			margin-top: 2px;
		}
		.bank-selected .bank-selected-ul li .checkbox-div input[type=checkbox]{
			visibility: hidden;
		}

		.bank-selected .bank-selected-ul li .checkbox-div label{
			width: 8px;
			height: 8px;
			border-radius: 100px;

			cursor: pointer;
			position: absolute;
			top: 2px;
			left: 2px;
			z-index: 1;

			background: #fff;
		}

		.bank-selected .bank-selected-ul li .checkbox-div input[type=checkbox]:checked + label{
			display: block;
			background: #ff9e48;
		}

		.bank-selected .bank-selected-ul li .bank-one{
			float: left;
		}

		.bank-selected .bank-selected-ul li .bank-one .bank_icon{
			height: 18px;
			float: left;
			margin-right: 2px;
		}

		.bank-selected .bank-selected-ul li .bank-one .bank_icon + span{
			font-size: 14px;
			line-height: 18px;
		}

		#rechargeCon .recharge-commit{
			margin-bottom: 0px;
			margin-top: 60px;
		}

        #rechargeCon .left_con{
            padding: 0px 20px;
            width: 540px;
        }
        #rechargeCon .selone li{
            float: left;
            width: 100px;
            height: 32px;
            line-height: 32px;
            border:2px solid #e0e0e0;
            margin-right:30px;
            margin-bottom: 30px;
            text-align: center;
            cursor: pointer;
            position: relative;
        }
        #rechargeCon .selone li:hover{
            border-color: #ef5350;
        }
        #rechargeCon .selone li.checked{
            border-color: #ef5350;
        }
        #rechargeCon .selone li .personal_icon{
            position: absolute;
            right: -2px;
            top: -1px;
            width: 20px;
            height: 20px;
        }
        #rechargeCon .sale{
            font-size: 14px;
            color: #333333;
        }
        #rechargeCon .sale .price{
            color: #f55c51;
        }

        #rechargeCon #commit{
            padding: 12px 40px;
            border:1px solid #ff7800;
            background-color: #FF7800;
            font-size: 14px;
            border-radius: 4px;
            margin-left: 0;
            color:#FFFFFF;
        }

        #rechargeCon .right .img{
            height:100px;
            /*border:1px solid;*/
        }

		.ui-dialog.err-notice.light.wx-recharge{
			width:auto;
		}

		.err-notice.wx-recharge .ui-dialog-body{
			padding:0;
		}
		.err-notice.wx-recharge .ui-dialog-footer{
			display: none;
		}

		#rechargeCon .control-group.rule{
			line-height: 14px;
		}

		#rechargeCon .control-group.rule .checkbox-div{
			cursor: pointer;

		}

		#rechargeCon .control-group.rule label{
			margin-right:6px;
		}

		#rechargeCon .control-group.rule a{
			color: #ff7800;
		}

    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
    <div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
        <div class="content">
            <div id="rechargeCon">
                <p class="pagetitle">充值欢朋币</p>
				<div class="form-horizontal user-desc">
					<div class="control-group userdetail">
						<div class="control-label">账号:</div>
						<div class="controls">
							<span class="username"></span>
							<span class="balanceDiv">
								<span class="label">余额:</span>
								<span class="balance">
									<span class="num">0</span>
									<span style="color: #666666">欢朋币</span>
								</span>
							</span>
						</div>
					</div>
				</div>
				<div class="row-fluid form-horizontal money-selected-div">
					<div class="control-group money-select">
						<div class="control-label">请选择充值金额</div>
						<div class="controls">
							<span class="money-select-one selected">10元<span class="personal_icon sel"></span></span>
							<span class="money-select-one">50元</span>
							<span class="money-select-one">100元</span>
							<span class="money-select-one">500元</span>
							<span class="money-select-one">1000元</span>
							<span class="money-select-other">
								<input type="text"/>
								<span>元</span>
							</span>
							<div class="clear"></div>
						</div>
					</div>
					<div class="control-group money-num-show">
						<div class="control-label">兑换数量</div>
						<div class="controls">
							<span class="coin-num">100</span>
							<span style="margin-right: 10px;">欢朋币</span>
							<span class="recharge-desc">(兑换比例 1元:10欢朋币)</span>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="row-fluid form-horizontal">
					<div class="control-group pay-channel">
						<div class="control-label">支付方式</div>
						<div class="controls">
							<span class="pay-channel-one selected"><img src="../static/img/recharge/zfbpayment.png" alt=""/><span class="personal_icon sel"></span></span>
							<span class="pay-channel-one"><img src="../static/img/recharge/wxpayment.png" alt=""/></span>
<!--							<span class="pay-channel-one"><img src="../static/img/recharge/wangyinpayment.png" alt=""/></span>-->
<!--							<span class="pay-channel-one"><img src="../static/img/recharge/onlinepayment.png" alt=""/></span>-->
							<div class="clear"></div>
						</div>
					</div>
<!--					<div class="control-group bank-selected none">
						<div class="control-label">选择银行</div>
						<div class="controls">
							<ul class="bank-selected-ul">
								<li data-bank="icbc">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-icbc" />
										<label for="check-icbc"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon icbc"></span>
										<span>工商银行</span>
									</span>
								</li>
								<li data-bank="ccb">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-ccb" />
										<label for="check-ccb"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon ccb"></span>
										<span>建设银行</span>
									</span>
								</li>
								<li data-bank="abc">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-abc" />
										<label for="check-abc"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon abc"></span>
										<span>农业银行</span>
									</span>
								</li>
								<li data-bank="bcm">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-bcm" />
										<label for="check-bcm"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon bcm"></span>
										<span>交通银行</span>
									</span>
								</li>
								<li data-bank="boc">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-boc" />
										<label for="check-boc"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon boc"></span>
										<span>中国银行</span>
									</span>
								</li>
								<li data-bank="cmbc">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-cmbc" />
										<label for="check-cmbc"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon cmbc"></span>
										<span>民生银行</span>
									</span>
								</li>
								<li data-bank="cmb">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-cmb" />
										<label for="check-cmb"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon cmb"></span>
										<span>招商银行</span>
									</span>
								</li>
								<li data-bank="psbc">
									<div class="checkbox-div">
										<input type="checkbox" value="1" id="check-psbc" />
										<label for="check-psbc"></label>
									</div>
									<span class="bank-one">
										<span class="bank_icon psbc"></span>
										<span>中国邮政</span>
									</span>
								</li>
							</ul>
							<div class="clear"></div>
						</div>
					</div>-->
					<div class="control-group recharge-commit">
						<div class="controls">
							<button id="commit" class="btn">充值</button>
						</div>
					</div>
					<div class="control-group rule">
						<div class="controls">
							<div class="checkbox-div">
								<label class="checkbox-label checked"></label>
								<span>阅读并同意</span>
								<a target="_blank" rel="noopener"  href="../protocol/cz.php">《欢朋直播充值协议》</a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
                <!--<div class="left left_con">
                    <ul class="selone">
                        <li class="checked" data-id="2">2000欢朋币
                            <span class="personal_icon sel"></span>
                        </li>
                        <li data-id="10">10000欢朋币</li>
                        <li data-id="20">20000欢朋币</li>
                        <li data-id="50">50000欢朋币</li>
                        <li data-id="100">10万欢朋币</li>
                        <li data-id="200">20万欢朋币</li>
                        <li data-id="500">50万欢朋币</li>
                        <li data-id="1000">100万欢朋币</li>
                        <div class="clear"></div>
                    </ul>
                    <div class="clear"></div>
                    <div class="sale mt-10">
                        <span>售价:</span>
                        <span class="price">2</span>
                        <span>元</span>
                    </div>
                    <button class="btn mt-30" id="commit">提交</button>
                </div>
                <div class="right">
                    <div class="w-230 img">
						<img src="../static/img/pcenter_recharge.png" alt=""/>
                    </div>
                </div> -->
                <div class="clear"></div>
            </div>
        </div>
		<div class="clear"></div>
    </div>
<script type="text/html" id="jsTemplate-wxpay">
	<div class="wxScan">

		<div class="scanContent clearfix">

			<div class="contentLeft">

				<div class="leftMoney">

					<p>金额 (元) : <span class="needPay"> <%=price%> </span></p>

				</div>

				<div class="Qrcode">

					<div id="wxpay-qrcode" class="codeImg" style=""></div>

				</div>

				<div class="wxDesc">
					<p>打开微信扫一扫支付</p>
				</div>

			</div>
			<div class="contentRight"></div>
		</div>
	</div>
</script>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
</body>
<script type="text/javascript">
	personalCenter_sidebar('recharge')
	var recharge_obj;
    $(document).ready(function(){
		$conf = conf.getConf();

		recharge_obj = {
			money:10,
			channel:1,//1:alipay, 2:wx,3:网银, 4在线支付
			bank:'',
			init:function(){
                var nick = getCookie('_unick');
//                var property = getProperty();
                var property = {};
                var requestUrl = $conf.api+'property/api_myProperty.php';
                var requestData = {
                    uid:getCookie('_uid'),
                    encpass:getCookie('_enc'),
                    type:1
                };
                ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                    $('.userdetail').find('.balance .num').text(digitsFormat(responseData.hpcoin));
                });

                $('.userdetail').find('.username').text(nick);
//                $('.userdetail').find('.balance .num').text(digitsFormat(property.coin));

				this.selectMoney();
				this.selectMoneyShow();
				this.selectPayChannel();
				this.selectBank();
				this.commitEvent();
				this.agrees();
			},
			agrees:function(){
				var self = this;
				$(".checkbox-div .checkbox-label, .checkbox-div span").click(function () {
					if(self.agree){
						$('.checkbox-div .checkbox-label').removeClass('checked');
						self.agree = false;
					}else{
						$('.checkbox-div .checkbox-label').addClass('checked');
						self.agree = true;
					}
				});
			},
			selectMoney:function(){
				$('.money-select .money-select-one').bind('click', function(){
					$('.money-select .money-select-one .personal_icon').remove();
					$('.money-select .money-select-other .personal_icon').remove();

					$('.money-select .money-select-one.selected').removeClass('selected');
					$('.money-select .money-select-other.selected').removeClass('selected');
					$('.money-select .money-select-other input').val('');

					$(this).addClass('selected').append('<span class="personal_icon sel"></span>');

					recharge_obj.money = parseInt($(this).text()) || 0;
					recharge_obj.selectMoneyShow();
				});

				$('.money-select .money-select-other input').focus(function(){
					$('.money-select .money-select-one .personal_icon').remove();
					$('.money-select .money-select-other .personal_icon').remove();

					$('.money-select .money-select-one.selected').removeClass('selected');
					$('.money-select .money-select-other.selected').removeClass('selected');
					recharge_obj.money = 0;
					recharge_obj.selectMoneyShow();
					$(this).parent().addClass('selected').append('<span class="personal_icon sel"></span>');
				});

				$('.money-select .money-select-other input').bind('input propertychange', function(){
					if($(this).val().length > 6){
						$(this).val($(this).val().substr(0,6));
					}else{
						var len = $(this).val().length - 1;
						check_format_money($(this).val(), len, this);
					}

					recharge_obj.money = parseInt($(this).val()) || 0;
					recharge_obj.selectMoneyShow();

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
				/*$('.money-select .money-select-other input').blur(function(){
					if(recharge_obj.money < 0 || recharge_obj.money == ''){
                        tips('请输入正确的充值金额哦');
                        return false;
					}
//
//					if(recharge_obj.money > 999999){
//						alert('金额不能大于999999元');

//						return;
//					}
				});*/
			},
			selectMoneyShow:function(){
				var coin = recharge_obj.money * 10;
				$('.money-num-show .coin-num').text(digitsFormat(coin));
			},
			selectPayChannel:function(){
				$('.pay-channel .pay-channel-one').bind('click', function(){
					$('.pay-channel .pay-channel-one').removeClass('selected');
					$('.pay-channel .pay-channel-one .personal_icon').remove();

					$(this).addClass('selected');
					$(this).append('<span class="personal_icon sel"></span>');
					recharge_obj.channel = parseInt($(this).index()) + 1;
					if($(this).index() == 2){
						$('.bank-selected').show();
					}else{
						$('.bank-selected').hide();
					}
				});
			},
			selectBank:function(){
				$(".bank-selected-ul li").bind('click', function(){
					$('.bank-selected-ul li input').attr('checked',false);
					var bank = $(this).data('bank');
					var bankselect = document.getElementById('check-'+bank);
					console.log(bankselect);
					if(!bankselect){
						recharge_obj.bank = '';
						return;
					}else{
						recharge_obj.bank = bank;
						bankselect.checked = true;
					}
				})
			},
			wxpay:function(quantity,ref_url,promationID){
				var self = this;
				var wx_order=1; // 1:start, 2:success,3:failed,4:api failed
				var wx_interval=false;
				var wx_dialog = '';
				var get_wx_status = function(orderid){
					var url = $conf.api + 'wxpay/status.php?_t='+ new Date().getTime();
					var data = {
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						orderid:orderid
					}
					ajaxRequest({url:url,data:data},function(d){
						if(d.step == 'finish'){
							wx_order = 2;
                            $('.userdetail').find('.balance .num').text(digitsFormat(d.hpcoin));
						}
					},function(d){
						wx_order = 4;
					});
				}

				var stop_wx_status = function () {
					clearInterval(wx_interval);
					wx_order = 1;
				}

				var out_close_wxDialog = function () {
					setTimeout(function(){
						wx_dialog.close().remove();
					},2000);
				}

				var start_wx_status = function (orderid) {
					stop_wx_status();
					wx_interval = setInterval(function(){
						if(wx_order == 2){
							stop_wx_status();
							wx_dialog.content('<div class="paySuccess"> <div class="right"></div> <p>充值成功!</p> </div>');
							out_close_wxDialog();
						}else if(wx_order == 3){
							stop_wx_status();
							wx_dialog.content('<div class="payFailure"> <div class="failure"></div> <p>充值失败,请稍后尝试</p> </div>')
							out_close_wxDialog();
						}else if(wx_order == 4){
							stop_wx_status();
							wx_dialog.close().remove();
						}else{
							get_wx_status(orderid);
						}
					},1000);
				}

				wx_dialog = dialog({
					skin:'err-notice light wx-recharge',
					content:'<div class="payLoading"><div class="loading"></div><p>请稍候...</p></div>'
				});

				wx_dialog.showModal();
				var url = $conf.api + 'wxpay/unifiedorder.php?_t='+new Date().getTime();
				var data = {
					uid:getCookie('_uid'),
					encpass:getCookie('_enc'),
					quantity:quantity,
					productID:5,
					channel:'wechat',
					client:'web',
					ref_url:ref_url,
					promationID:promationID
				};
				self.wxPayLock = true;
				ajaxRequest({url:url,data:data},function(d){
					self.wxPayLock = false;
					var htmlfunc = huanpeng.template('jsTemplate-wxpay');
					var totalPrice = d.totalPrice/100;
					var wxdialog = dialog({
						skin:'err-notice light wx-recharge',
						content:htmlfunc({price:totalPrice.toFixed(2)}),
						title:'微信支付',
						cancel:function(){
							stop_wx_status();
							return true;
						}

					});
					if(wx_dialog){
						wx_dialog.close().remove();
					}
					wx_dialog = wxdialog;
					wxdialog.showModal();
					console.log(d.code_url);
					$("#wxpay-qrcode canvas").remove();
					$("#wxpay-qrcode").qrcode({
						render:'canvas',
						text:d.code_url,
						width:204,
						height:204
					});
					start_wx_status(d.orderid);
				},function(d){
					self.wxPayLock = false;
				});
			},
			wxPayLock:false,
			agree:true,
			commitEvent:function(){
				var self = this;
				$('#commit').bind('click', function(){

					if(!self.agree){
						alert('请先同意欢朋直播充值协议');
						return;
					}

					var hpcoin = parseInt($('.money-select-one.selected').text()) * 10;
					if(!hpcoin)
						hpcoin = parseInt($('.money-select-other.selected input').val()) * 10;

					if(!hpcoin || hpcoin < 0 || hpcoin == ''){
                        tips('请输入有效金额');
                        return;
                    }

                    if(hpcoin && (hpcoin/10) < 10){
                        tips('金额不能少于10元');
                        return;
                    }
                    if((hpcoin /10 )> 5000){
                        tips('金额不能大于5000元');
                        return;
                    }
                    if(self.channel == 1){
						location.href = self.getAlipayUrl(hpcoin);
						return;
					}

					else if(self.channel == 2){
						if(self.wxPayLock){
							return;
						}
						self.wxpay(hpcoin,'',0);
						return;
					}
				})
			},
			getAlipayUrl:function(hpcoin){
				var data = {
					quantity:hpcoin
				};
				return $conf.domain + 'payment/alipay/pay.php?'+$.param(data);
			}
		}
		recharge_obj.init();
    });
</script>
</html>