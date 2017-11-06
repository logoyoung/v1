<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/11
 * Time: 下午1:56
 */
$realpath = realpath(__DIR__)."/";
include_once '../../../../include/init.php';
include WEBSITE_PERSON.'isLogin.php';


$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$userCertifyStatus =  get_userCertifyStatus($_COOKIE['_uid'], $db);

//如果认证成功 返回个人中心主页
if($userCertifyStatus['emailstatus'] == EMAIL_PASS){
	$url = "http://".$conf['domain'].'/main/personal/mp/';
	echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
	exit;
}

if($userCertifyStatus['emailstatus'] == EMAIL_UNPASS){
    $email = $userCertifyStatus['email'];
}
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
		#commit{
			padding: 9px 47px;
		}
	</style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>
<?php
$userCertifyStatus =  get_userCertifyStatus($_COOKIE['_uid'], $db);
?>
<div class="container">
    <?php include WEBSITE_PERSON."sidebar_center.php"; ?>
	<div class="content">
		<div id="personal">
			<div class="basic">
				<?php include $realpath.'../pdetail.html.php' ?>
				<div class="pblockdiv">
					<div class="pblock">
						<?php include $realpath.'../titleLink.html.php' ?>
						<div class="list cur">
							<div class="form-horizontal mt-20">
								<div id="email" class="control-group">
									<div class="control-label">邮箱地址:</div>
									<div class="controls">
										<input class="w-230" type="text" placeholder="请输入您的邮箱地址" class="m-wrap small">
									</div>
								</div>
<!--								<div id="password" class="control-group">-->
<!--									<div class="control-label">确认密码:</div>-->
<!--									<div class="controls">-->
<!--										<input class="w-230" type="password" placeholder="请输入您的登录密码" class="m-wrap small">-->
<!--									</div>-->
<!--								</div>-->
								<div class="control-group mt-40">
									<div class="controls btn-controls">
										<div id="commit" class="button orange" style="width: 48px;">下一步</div>
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
//	console.log(1123);
$('.pblock .title span.emailCert').addClass('cur');
	(function(){
		var $conf = conf.getConf();
		var personalInfo = $("#personal .basic .pdetail");
		if(!personalInfo.get()[0])
			return;

//		$.ajax({
//			url:$conf.api + 'shamApi_accessPersonalInfo.php',
//			type:'post',
//			dataType:'json',
//			data:{
//				uid:getCookie('_uid'),
//				encpass:getCookie('_enc')
//			},
//			success:function(d){
//				if(d.uid){
//					personalInfo.find(".personalPic img").attr('src', d.pic);
//					personalInfo.find('.personalInfo .nick').text(d.nick);
//					personalInfo.find('.levellable level').text("Lv." + d.level);
//
//					var bar_width = personalInfo.find('.personalInfo .bar').width();
//					var between = d.levelIntegral - d.integral;
//					var levelbar_width = (d.integral / d.levelIntegral) * bar_width;
//					personalInfo.find('.personalInfo .bar levelbar').css('width',levelbar_width + 'px');
//					personalInfo.find('.personalInfo .bar p').text('距离升级还有' + between + "欢豆")
//					personalInfo.find('.payment_block .paytype:eq(0) no').text(d.hpcoin);
//					personalInfo.find('.payment_block .paytype:eq(1) no').text(d.hpbean);
//
//					setCookie('_unick', d.nick);
//				}
//			}
//		})
	}());
	function err(text){
		var htmlstr = '<span class="errInfo"> <span class="err_icon">x</span> <span class="err_text">'+text+'</span> </span>';

		return htmlstr;
	}
	$(document).ready(function(){
        $('.pblock .title .personal_info').removeClass('cur');
        $('#email .controls input').attr('placeholder','').val('<?php echo $email;?>');
		(function(a){

			var text = a('.control-group .controls input');
			text.focus(function(){
				a(this).parent().find('.errInfo').remove();
			});
			text.blur(function(){
				if(!a.trim(a(this).val())){
					$(this).parent().find('.errInfo').remove();
					$(this).parent().append(err('内容不能为空'));
				}
			});
			a('#email .controls input').blur(function(){
				var value = a.trim(a(this).val());
				if(!value)
					return;

				var reg = /^\w+@(\w)+((\.\w+)+)$/;
				if(!reg.test(value))
					$(this).parent().append(err('邮箱格式不正确'));
			});

			$("#commit").bind('click', function(){
				var email = a.trim(a('#email .controls input').val());
//				var password = a.trim(a('#password .controls input').val());

				if(!email)
					return;

				var requestUrl = $conf.api +'user/attested/sendMail_ajax.php';
				var requestData = {
					uid:getCookie("_uid"),
					encpass:getCookie('_enc'),
					email:email
				};

				ajaxRequest({url:requestUrl, data:requestData},function (d) {
					function emailCertPage(){
						var mailList = ['qq','163','126','sina'];

						var htmlstr = 	'<div class="control-group mt-60">'
							+'<p style="text-align: center; font-size: 14px;">我们已经向您的邮箱<span></span>发送了一封激活邮件，请点击邮件中的链接完成注册！</p>'
							+'<P style="text-align: center; font-size: 14px">注册成功后，您可以享受更多的服务啦</P>'
							+'<a id="enterEmail" target="_blank" class="button orange mt-40 left" style="margin-left: 356px;">进入邮箱验证</a>'
							+'</div>';
						$('.form-horizontal').append(htmlstr);

						$('.form-horizontal p:eq(0) span').text(email);
						var mailType = email.match(/@\w+\.\w+/)|| false;
						if(mailType[0])
							mailType = mailType[0].replace('@', '');
						var mailUrl = 'http://mail.' + mailType;
						$("#enterEmail").attr('href',mailUrl);
					}
					$('.form-horizontal .control-group').remove();
					emailCertPage();
				},function (d) {
					alert(d.desc);
				})

			});
		}(jQuery));
	});
</script>
</body>

</html>