<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/11
 * Time: 下午1:56
 */
$realpath = realpath( __DIR__ ) . "/";
include '../../../../include/init.php';
include WEBSITE_PERSON . 'isLogin.php';

$db   = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>个人中心-欢朋直播-精彩手游直播平台！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<?php include WEBSITE_TPL . 'commSource.php'; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo STATIC_CSS_PATH; ?>person.css?v=1.0.5">
    <script type="text/javascript" src="<?php echo STATIC_JS_PATH; ?>jquery.form.js"></script>
    <style>
        body {
            background-color: #eeeeee;
        }

        .content {
            min-height: 820px;
        }

    </style>
</head>
<body>
<?php include WEBSITE_MAIN . 'head.php' ?>
<script>new head(null);</script>

<?php
$userCertifyStatus = get_userCertifyStatus( $_COOKIE['_uid'], $db );
?>
<div class="container">
	<?php include WEBSITE_PERSON . "sidebar_center.php"; ?>
    <div class="content">
        <div id="personal">
            <div class="basic">
				<?php include $realpath . '../pdetail.html.php'; ?>
                <div class="pblockdiv">
                    <div class="pblock">
						<?php include $realpath . '../titleLink.html.php'; ?>

                        <div class="list cur">
                            <div class="form-horizontal mt-20">
                                <!--								<div class="control-group ">-->
                                <!--									<div class="control-label">手机认证：</div>-->
                                <!--									<div class="controls">-->
                                <!--										<span class="identifyDetail mt-16 mr-20 left">您认证的邮箱为</span>-->
                                <!--										<span class="option mt-16 left">立即认证</span>-->
                                <!--										<div class="clear"></div>-->
                                <!--									</div>-->
                                <!--								</div>-->
                                <div id="cur_pw" class="control-group">
                                    <div class="control-label">当前密码:</div>
                                    <div class="controls">
                                        <input class="w-230" type="password" placeholder="请输入当前密码" class="m-wrap small">
                                    </div>
                                </div>
                                <div id="new_pw" class="control-group">
                                    <div class="control-label">新密码:</div>
                                    <div class="controls">
                                        <input class="w-230" type="password" placeholder="请输入6-12位的新密码"
                                               class="m-wrap small">
                                        </span>
                                    </div>
                                </div>
                                <div id="new_pw2" class="control-group">
                                    <div class="control-label">确认密码:</div>
                                    <div class="controls">
                                        <input class="w-230" type="password" placeholder="请再次输入6-12位的新密码"
                                               class="m-wrap small">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="controls btn-controls">
                                        <div id="commit" class="button orange" style="width: 36px;">提交</div>
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
<?php include_once WEBSITE_MAIN . 'footerSub.php'; ?>
<script src="<?php echo STATIC_JS_PATH; ?>personal.js" type="text/javascript"></script>

<script type="text/javascript">
	$('.pblock .title .personal_info').removeClass('cur');
	$('.pblock .title span.changePassword').addClass('cur');

	inputPlaceholder();
	(function (a) {
		var c = conf.getConf();

		function err(text) {
			var htmlstr = '<span class="errInfo"> <span class="err_text">' + text + '</span> </span>';//<span class="err_icon">x</span>

			return htmlstr;
		}

		var curpwCtr = a('#cur_pw .controls');
		var npwCtr = a('#new_pw .controls');
		var npw2Ctr = a('#new_pw2 .controls');


		npw2Ctr.find('input').blur(function () {
			var npw1 = npwCtr.find('input').val();
			var npw2 = npw2Ctr.find('input').val();

			if (npw2 != npw1) {
				npw2Ctr.append(err('两次输入的密码不一致'));
			}
		});

		a('.control-group .controls input').blur(function () {
			if (!$(this).val()) {
				$(this).parent().find('.errInfo').remove();
				$(this).parent().append(err('内容不能为空'));
			}

		});
		a('.control-group .controls input').focus(function () {
			var ctr = $(this).parent();
			ctr.find('.errInfo').remove();
		});

		$("#commit").bind('click', function () {
			var curpw = curpwCtr.find('input').val();
			var npw1 = npwCtr.find('input').val();
			var npw2 = npw2Ctr.find('input').val();

			if (!curpw || !npw1 || !npw2 || npw1 !== npw2) return;
			var postdata = {};

			postdata.uid = getCookie('_uid');
			postdata.encpass = getCookie('_enc');
			postdata.password = curpw;
			postdata.newPassword = npw1;
			console.log(postdata);
			var requestUrl = c.domain + 'api/user/revise/modifyPassword.php';
			var requestData = postdata;

			ajaxRequest({url: requestUrl, data: requestData}, function (d) {
				a('.control-group .controls input').val('');
				tips('修改成功');
				logout_submit();
			},function (d) {
                tips(d.desc);
                return 0;
			});
//			a.ajax({
//				url: c.domain + 'a/modifyPassword.php',
//				type: 'post',
//				dataType: 'json',
//				data: postdata,
//				success: function (d) {
//					if (d.isSuccess == 1) {
//
//					}
//					if (d.code) {
//						tips(d.desc);
//						return 0;
//					}
//				}
//			})
		});
	}(jQuery))
</script>
</body>
</html>