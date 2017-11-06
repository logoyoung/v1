<?php

//如果未登录，跳转到登录提示页面
//如果已经认证成功，返回个人资料页面
$realpath = realpath(__DIR__)."/";
include '../../../../include/init.php';
include WEBSITE_PERSON.'isLogin.php';


$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

?>

<!DOCTYPE html>
<html>
<head>
	<title>个人中心-欢朋直播-精彩手游直播平台！</title>
	<meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?php include WEBSITE_TPL.'commSource.php';?>
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH;?>person.css?v=1.0.5">
	<script type="text/javascript" src="<?php echo STATIC_JS_PATH;?>jquery.form.js"></script>
	<style>
		body{
			background-color: #eeeeee;
		}
		.content{
			min-height:820px;
		}

	</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<?php
$userCertifyStatus = get_userCertifyStatus($_COOKIE['_uid'], $db);
?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="personal">
			<div class="basic">
				<?php include $realpath.'../pdetail.html.php'; ?>
				<div class="pblockdiv">
					<div class="pblock">
						<?php include $realpath.'../titleLink.html.php';?>

						<div class="list cur">
							<div class="form-horizontal mt-20">
								<div id="mobile_cg" class="control-group">
									<div class="control-label">手机号码</div>
									<div class="controls">
										<input class="w-230" type="text" placeholder="请输入您的手机号">
										<span class="errInfo"></span>
									</div>
								</div>
								<div id="vcode_cg" class="control-group">
									<div class="control-label">输入验证码</div>
									<div class="controls">
										<input class="w-155 mr-20 left" type="text" placeholder="请输入验证码">
											<span class="identifyCode mr-20">
												<img src="vcode.php" alt=""/>
											</span>
										<span class="changeOne mr-20 mt-15 left">换一张</span>
										<script>
											(function(){
//													newgdcode(obj,url){
//														obj.src = url + '?nowtime=' + new Date().getTime();
//													}
												var src = $('.identifyCode img').attr('src');
												$('.changeOne').bind('click',function(){
													var obj = $('.identifyCode img');
													obj.attr('src',src + '?nowtime' + new Date().getTime());
												});
                                                $('.identifyCode').bind('click',function(){
                                                    var obj = $('.identifyCode img');
                                                    obj.attr('src',src + '?nowtime' + new Date().getTime());
                                                });
											}())
										</script>
										<span class="errInfo left w-170" style=" margin-top:15px;"></span>
										<div class="clear"></div>
									</div>
								</div>
								<div id="mcode_cg" class="control-group">
									<div class="control-label">验证码</div>
									<div class="controls">
										<input class="w-155 mr-20" type="text" placeholder="请输入短信验证码">
										<!-- CLASS="wait" 30s后重发-->
										<input id="getMessCode" class="button blue "type="button"  value="获取验证码">
										<span class="errInfo"></span>
									</div>
								</div>
								<div id="submit" class="control-group">
									<div class="control-label"></div>
									<div class="controls btn-controls" >
										<div class="button orange mt-20" style="width: 36px">提交</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<script src="<?php echo STATIC_JS_PATH; ?>personal.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.pblock .title span.phoneCert').addClass('cur');
	$(document).ready(function(){
		function err(text){
			var htmlstr = '<span class="errInfo"> <span class="err_icon">x</span> <span class="err_text">'+text+'</span> </span>';

			return htmlstr;
		}
		(function(a){
			var c =  conf.getConf();


			var mobileCtr = a('#mobile_cg .controls');
			var vcodeCtr = a('#vcode_cg .controls');
			var mcodeCtr = a('#mcode_cg .controls');


			a('.control-group .controls input[type=text]').focus(function(){
				$(this).parent().find('.errInfo').remove();
			});
			a('.control-group .controls input[type=text]').blur(function(){
				var val = a.trim($(this).val());
				if(!val)
					$(this).parent().append(err('内容不能为空'));
			});

			mobileCtr.find('input[type=text]').blur(function(){
				var mobile = a.trim(a(this).val());

				if(!checkMobile(mobile)){
					mobileCtr.append(err('手机格式错误'));
				}
			});

			a('#getMessCode').bind('click',function(){
				var mobile = mobileCtr.find('input').val();
				mobile = a.trim(mobile);

				var vcode = vcodeCtr.find('input').val();
				vcode = a.trim(vcode);

				console.log(vcode + '   ' + mobile);

				if(!mobile || !checkMobile(mobile) || !vcode)
					return;

				a.ajax({
					url:'getMobileCode.php',
					type:'post',
					dataType:'json',
					data:{
						mobile:mobile,
						send_code:vcode
					},
					success:function(msg){
						if(msg.code == 1){
							RemainTime();
						}
					}
				});

				var iTime = 29;
				var Account;
				function RemainTime(){
					var btn = $('#getMessCode');
					btn.attr('disabled', 'disabled');
					btn.addClass('wait');

					var iSecond, iMinute, sSecond = "", sTime = "";
					if(iTime >= 0){
						iSecond = parseInt(iTime % 60);
						iMinute = parseInt(iTime / 60);

						if(iSecond > 0) {
							if (iMinute > 0) {
								sSecond = iMinute + "分钟" + iSecond + "s"
							} else {
								sSecond = iSecond + "s后重发";
							}
						}
						sTime = sSecond;
						if(iTime == 0){
							clearTimeout(Account);
							sTime = "点击获取验证码";
							iTime = 29;
							btn.removeAttr('disabled');
							btn.removeClass('wait');
						}else{
							Account = setTimeout(RemainTime, 1000);
							iTime = iTime - 1;
						}
					}else{
						sTime = '点击获取验证码';
					}
					btn.val(sTime);
				}
			});
			a('#submit .controls .button').bind('click', function(){
				var mobile = mobileCtr.find('input[type=text]').val() || '';
				var mcode = mcodeCtr.find('input[type=text]').val() || '';
				var vcode = vcodeCtr.find('input[type=text]').val() || '';

				if(!mobile || !mcode || !vcode)
					return;

				var postdata = {};
				postdata.uid = getCookie('_uid');
				postdata.encpass = getCookie('_enc');
				postdata. mobile = mobile;
				postdata.mcode = mcode;


				a.ajax({
					url: '../mp_ajax/certMobile_ajax.php',
					type: 'post',
					dataType: 'json',
					data:postdata,
					success:function(d){
						console.log(d);
						if(d.isSuccess == 1){
							alert('认证成功');
							location.href = $conf.person + 'mp';
						}
					}
				});
			});

		}(jQuery));
	});
</script>
</body>
</html>