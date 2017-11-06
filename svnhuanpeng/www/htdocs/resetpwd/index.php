<?php
include '../../include/init.php';
include INCLUDE_DIR."User.class.php";
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/29
 * Time: 下午1:49
 */
$uid = isset($_COOKIE['uid'])  ? (int) $_COOKIE['uid']  : 0;
$enc = isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
if($uid && $enc) {
	$userHelp = new UserHelp($uid);
	if(!$userHelp->checkStateError($enc)) {
		header("Location:".WEB_ROOT_URL);
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>找回密码</title>
	<meta content="width=device-width,initial-scale=1.0" name="viewport" />
	<meta name="format-detection" content="telephone=no"/>
	<meta content="email=no" name="format-detection" />
	<meta http-equiv="x-ua-compatible" content="IE=edge" >
	<title>找回密码-欢朋直播-精彩手游直播</title>
	<link rel="stylesheet" href="css/retrieve.css?v=1.0.4">
	<!--[if lte IE 8]><link rel="stylesheet" href="css/IE8.css?v=1.0.4"><![endif]-->
	<?php include WEBSITE_TPL."commSource.php";?>
    <style>
        button{cursor: pointer;}
    </style>
</head>
<body>
<?php include WEBSITE_MAIN."head.php"; ?>
<div class="retrieve-container">
<!--	<div class="retrieve-header"></div>-->
	<div class="retrieve-content">

		<div class="content-title">
			<h2>找回密码</h2>
			<hr>
		</div>

		<div class="content-middle">

			<div class="middle-step">

				<div class="stepFirst">
					<p>1</p>
					<i>确认账号</i>
				</div>
				<div class="progress-First"></div>
				<div class="stepTwo missActive">
					<p>2</p>
					<i>安全验证</i>
				</div>
				<div class="progress-Two"></div>
				<div class="stepThird missActive">
					<p>3</p>
					<i>重置密码</i>
				</div>

			</div>

		</div>

		<div class="content-success">

			<div class="successIcon"></div>
			<p>密码修改成功 ! 是否重新登录 ?</p>

			<div class="logReturn">
				<a href="javascript:;" onclick="loginFast.login(0)" id="login">立即登录</a>
				<a href="../index.php" id="backHome">返回首页</a>
			</div>

		</div>

		<div class="content-bottom">

			<div class="content-first">

				<span id="phoneDesc">手机号:</span><input type="text" placeholder="请输入您的手机号码" id="inputPhone"  style="width: 290px;"><div class="checkThis"></div><div class="wrongCheck">您的电话输入有误<div class="arrow"></div></div>


				<span id="phoneNum"></span>
				<span id="getCurrent">获取验证码</span>

			</div>

			<div id="geetest" style="display: none;" class="content-Qrcode"><p></p></div>

			<div class="authCode">
				<span class="authDesc">验证码:</span><input id="mobileCode" type="text" placeholder="请输入手机验证码">
			</div>

			<div class="content-next">
				<div class="passwordLoc">
					<div><span>新密码：</span><input id="pwd_new" type="password" placeholder="请输入新密码"></div>
					<div><span>确认密码：</span><input id="pwd_continue" type="password" placeholder="请确认新密码"></div>
				</div>
				<button id="retrieveBtn">下一步</button>
			</div>

		</div>

		<div class="updateIE">
			<p>您的浏览器版本过低,请<a href="http://chrome.360.cn/">更新浏览器</a>后再尝试该操作</p>
		</div>

	</div>

<!--	<div class="retrieve-footer"></div>-->

</div>
<?php include WEBSITE_MAIN."footer.php"?>
</body>
<script>
    var head = new head(null,false);
</script>

<script>

	$(function () {

	    function noticeError(msg) {
            alert(msg);
        }

		function setError(msg){
			error = true;
			errorMsg = msg;
			return errorMsg;
		}

		function clearError(){
			errorMsg = '';
			error = false;
		}

		var error = false;
		var errorMsg = '';
		var userMobile = '';
		var unsubMsg = true;

		var geeData = false;

		$('#inputPhone').focus(function () {
			clearError();
			$('.content-first .checkThis').hide();
		});

		$('#inputPhone').blur(function () {
			var mobile = $(this).val();
			if(!checkMobile(mobile)){
				tips('手机号格式错误');
				unsubMsg = '手机号格式错误';
				return false;
			}else{
                var requestUrl = $conf.api + 'check/checkMobileIsUsed.php';
                var requestData = {mobile:mobile};
                ajaxRequest({url:requestUrl, data:requestData},function(responseData){
                    var isUsed = Number(responseData.isUsed);
                    if(!isUsed){
                        tips('账号不存在');
                        unsubMsg = '账号不存在';
                    }else{
                        userMobile = mobile;
                        $('.content-first .checkThis').show();
                        unsubMsg = true;
                    }
                },function(e){
                    console.log(e);
                });
            }

		});

		geetest({product:'embed',append:"#geetest"}, function (data) {
			geeData = data;

		});
        $('#geetest').show();

		$('#retrieveBtn').click(function () {
			if(!geeData){
				return;
			}
			if($('#inputPhone').val() == ''){
                tips('请填写手机号码!');
                return;
            }
			if(error){
				noticeError(errorMsg);
			}else{
				if(unsubMsg!=true){tips(unsubMsg);return false;}
                                userMobile = $('#inputPhone').val();

				$('.progress-First').addClass('progress-active');
				$('.stepTwo').removeClass('missActive');
				$('.content-Qrcode,#inputPhone,.checkThis,.wrongCheck').css('display','none').remove();
                                
				if(userMobile){
                    $('.content-first #phoneNum').text(userMobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2'));
                }else{
                    $('.content-first #phoneNum').text('1**********');
                }
				$('#phoneNum,#getCurrent').css('display','inline-block');
				$('.authCode').show();
				$(this).parent().append('<button id="next">下一步</button>');
				$(this).remove();

				var getCodeTime = 0;
				//获取验证码
				$("#getCurrent").click(function () {
					if(getCodeTime > 0) {
                        return;
                    }
					var self = this;
					var requestUrl = $conf.api + 'code/mobileCode.php';
					var requestData = {
						mobile:userMobile,
						from:2
					};
					ajaxRequest({url:requestUrl,data:requestData}, function(responseData){
						getCodeTime = 60;
						var interval = setInterval(function(){
							getCodeTime --;
							if(getCodeTime <= 0){
								clearInterval(interval);
								$(self).text('获取验证码');
							}else{
								$(self).text(getCodeTime+' s');
								$('.search').text('');
							}
						}, 1000);
					},function (responseData) {
                        if(responseData.type == 1){
                            tips('验证错误');
                        }else if(responseData.type == 2){
                            tips(responseData.desc);
                        }
					});
				});
                $("#next").click(function () {
                    var mobileCode = $("#mobileCode").val();
                    if(!mobileCode){
                        noticeError('验证码不能为空');
                        return;
                    }
                    var requestUrl = $conf.api + 'check/checkMobileCode.php';
                    var requestData = {mobile:userMobile, mcode:mobileCode};
                    var self = this;

                    ajaxRequest({url:requestUrl, data:requestData}, function (responseData) {
                        $('.stepThird').removeClass('missActive');
                        $('.progress-Two').addClass('progress-active');

                        $('.content-first,.authCode').hide().remove();
                        $('.passwordLoc').css('display','block');
                        $(self).parent().append('<button id="success">完成</button>');
                        $(self).remove();
                        $("#success").click(function () {
                            var password_1 = $('#pwd_new').val();
                            var password_2 = $('#pwd_continue').val();
                            if(!password_1){
                                noticeError('密码不能为空');
                                return;
                            }

                            if(password_1 != password_2){
                                noticeError('输入密码不一致');
                                return;
                            }
                            $('.search').val('');

                            var requestUrl = $conf.api + 'user/revise/forgetPassword.php';
                            var requestData = {
                                mobile:userMobile,
                                password:password_1,
                                password2:password_2
                            };

                            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                                $("#login").attr('href', $conf.domain + 'personal/login.php');
                                $("#backHome").attr("href", $conf.domain);
                                $('.content-success').show();
                                $('.content-bottom').hide().remove();

                            },function (responseData) {
                                if(responseData.type == 1){
                                    noticeError('修改失败');
                                }else if(responseData.type == 2){
                                    noticeError(responseData.desc);
                                }
                            });
                        });

                    },function(responseData){
                        if(responseData.type == 1){
                            noticeError('验证错误');
                        }else if(responseData.type == 2){
                            tips(responseData.desc);
                        }
                    });
                });


			}
		});


	});

</script>

</html>
