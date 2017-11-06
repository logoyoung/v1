<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/11
 * Time: 下午1:56
 */
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
		.select_group .select_div .name{
			line-height: 16px;
			margin: 0;
		}
		.select_group .select_div .personal_icon{
			width: 16px;
			height: 16px;
		}
		.select_group .select_result{
			position: absolute;
			background-color: #fff;
			height:auto;
			width: 180px;
			margin-top: -44px;
			border: 1px solid #cccccc;
			border-radius: 3px;
		}
		.select_group .select_result li{
			width: 160px;
			padding: 13px 10px;
			cursor:pointer;
			float: left;
		}
		.select_group .select_result li .name{
			margin: 0px;
			float: left;
			line-height: 16px;
			font-size: 14px;
		}
		.select_group .select_result li .personal_icon{
			width: 16px;
			height: 16px;
		}
		#identCodeETime .controls input{
			text-align: center;
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
				<?php include $realpath.'../pdetail.html.php'; ?>
				<div class="pblockdiv">
					<div class="pblock">
						<?php include $realpath.'../titleLink.html.php';?>

						<div class="list cur">
							<div class="form-horizontal mt-20">
								<div id="identName" class="control-group">
									<div class="control-label">真是姓名</div>
									<div class="controls">
										<input class="w-275" type="text" placeholder="请输入您的姓名">
										<span class="errInfo"></span>
									</div>
								</div>
<!--								<div class="control-group">-->
<!--									<div class="control-label">证件类型</div>-->
<!--									<div class="controls select_group">-->
<!--										<div class="select_div  w-160 text">-->
<!--											<p class="left name">工商银行</p>-->
<!--											<span class="personal_icon arrow_bt right"></span>-->
<!--											<div class="clear"></div>-->
<!--										</div>-->
<!--										<div class="select_result none">-->
<!--											<ul>-->
<!--												<li class="selResOne">-->
<!--													<p class="left name">工商银行</p>-->
<!--													<span class="personal_icon arrow_bt right"></span>-->
<!--													<div class="clear"></div>-->
<!--												</li>-->
<!--											</ul>-->
<!--										</div>-->
<!--										<select class='realSelect w-160 text' style="display: none">-->
<!--											<option value="1">身份证</option>-->
<!--											<option value="2">军官证</option>-->
<!--											<option value="3">港澳台身份证</option>-->
<!--										</select>-->
<!--									</div>-->
<!--								</div>-->
								<div id="identCode" class="control-group write-text">
									<div class="control-label">身份证号码</div>
									<div class="controls">
										<input class="w-275" type="text" placeholder="请输入您的身份证号码">
										<span class="errInfo"></span>
									</div>
								</div>
								<div id="identCodeETime" class="control-group write-text" style="margin-bottom: 30px;">
									<div class="control-label">身份证到期时间</div>
									<div class="controls">
										<input class="w-90 mr-20" type="text" placeholder="年">
										<input class="w-70 mr-20" type="text" placeholder="月">
										<input class="w-70 " type="text" placeholder="日">
										<span class="errInfo"></span>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">身份证正面照</div>
									<div class="controls">
										<div class="left photo">
											<span class="pic">
												<img src="../../../static/img/identCard_front.png" alt=""/>
											</span>
											<label>上传身份证正面照及签名</label>
											<div class="clear"></div>
											<form action="" id="upload_front" name="upload_front" enctype="multipart/form-data" method="post">
												<div class="upload">上传
													<input type="file" name="file" id="front" accept="image/*"/>
												</div>
											</form>
<!--											<div class="example">示例</div>-->
										</div>
										<div class="left photo">
                                                <span class="pic">
													<img src="../../../static/img/identCard_back.png" alt=""/>
                                                </span>
											<label>上传身份证背面照及签名</label>
											<div class="clear"></div>
											<form action="" id="upload_back" name="upload_back" enctype="multipart/form-data" method="post">
												<div class="upload">上传
													<input type="file" name="file" id="back" accept="image/*"/>
												</div>
											</form>
<!--											<div class="example">示例</div>-->
										</div>
										<div class="left photo">
                                                <span class="pic">
													<img src="../../../static/img/identCard_handheld.png" alt=""/>
                                                </span>
											<label>上传手持身份证正面照及签名</label>
											<div class="clear"></div>
											<form action="" id="upload_handheld" name="upload_handheld" enctype="multipart/form-data" method="post">
												<div class="upload">上传
													<input type="file" name="file" id="handheld" accept="image/*"/>
												</div>
											</form>
<!--											<div class="example">示例</div>-->
										</div>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
								<div class="control-group">
									<div class="controls">
										<div class="text_notice">身份证及身份照将会进行人工审核，照片无比做到以下几点：</div>
										<div class="text_notice">1.您需要年满16岁</div>
										<div class="text_notice">2.全面照片哪包含以下内容：</div>
										<ul class="text_notice_sub">
											<li class="">您的面部、身份证正面和你的姓名签名</li>
											<li class="">身份证证面和你的姓名签名</li>
											<li class="">身份证反面和您姓名签名</li>
										</ul>
										<div class="text_notice">3.清晰：身份证照片文字以及图片清晰可见</div>
										<div class="text_notice">4.真实：身份证照片无PS</div>
										<div class="text_notice">5.有效：身份证为二代身份证，在有效期内，正反内容相符</div>
										<div class="text_notice notice">＊如果以上信息有所不符，实名认证将驳回</div>
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
<?php include_once WEBSITE_MAIN . 'footerSub.php';?>
<script src="<?php echo STATIC_JS_PATH; ?>personal.js" type="text/javascript"></script>
<script type="text/javascript">
$conf = conf.getConf();
inputPlaceholder();
$('.pblock .title span.realnameCert').addClass('cur');
	function err(text){
		var htmlstr = '<span class="errInfo">  <span class="err_text">'+text+'</span> </span>';//<span class="err_icon">x</span>

		return htmlstr;
	}
	function isLeapYear(year){
		if(((year % 4) == 0) && ((year % 100) != 0 || (year % 400) == 0)){
			return true
		}
		return false;
	}
	function identityCodeVaild(code){
		var tip = '';
		var pass = true;
		var city = [11,12,13,14,15,
			21,22,23,
			31,32,33,34,35,36,37,
			41,42,43,44,45,46,
			50,51,52,53,54,
			61,62,63,64,65,
			71,81,82,91
		];
		var identReg = /^[1-9]\d{5}((18|19|20)\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dx]$/i;

		if(!code || !identReg.test(code)){
			pass = false;
			tip = '格式错误';
			return false;
		}else if(city.indexOf(parseInt(code.substr(0,2))) == -1 ){
			pass = false;
			tip = '地区编码错误';
            return false;
		}else{
			//检查校验位
			code = code.split('');

			//∑(ai×Wi)(mod 11)
			//加权因子
			var factor = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
			//校验位
			var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
			var sum = 0,
				ai = 0,
				wi = 0;
			for(var i = 0; i < 17; i++){
				ai = code[i];
				wi = factor[i];
				sum += ai * wi;
			}
			if(parity[sum % 11] != code[17]){
				pass =  false;
				tip = '校验位错误';
			}
		}
		if(!pass) console.log(tip);
		return pass;
	}
	function checkDate(y, m, d){

		var year_reg = /^(1[89]|20)\d{2}$/;
		var month_reg = /^0[1-9]|1[0-2]$/;

		if(!year_reg.test(y))
			return false;

		if(!month_reg.test(m))
			return false;

		var reg = '';
		if(/^0[13578]|1[02]$/.test(m)){
			reg = /^0[1-9]|[12]\d{1}|3[01]/;
		}else{
			if(m == '02'){
				var leapYear = isLeapYear(parseInt(y));
				reg = leapYear ? /^0[1-9]|1\d{1}|2[0-9]$/ : /^0[1-9]|1\d{1}|2[0-8]/;
			}else{
				reg = /^0[1-9]|[12]\d{1}|30/;
			}
		}

		return reg.test(d);
	}
	(function(){
		var select = $('.select_group .realSelect');
		var option = select.find('option');

		if(!option.get()[0]) return;

		var selectList = [];

		option.each(function(){
			selectList[$(this).val()] = $(this).text();
		});

		var curSel = select.val();
		initCurrentSelect(curSel);

		function initCurrentSelect(curSel){
			//init current select
			if($('.select_group .select_div').get()[0])
				$('.select_group .select_div').remove();

			function thehtml(val){
				var htmlstr = '';
				htmlstr = '<div class="select_div  w-160 text" data-val="'+val+'">';
				htmlstr = htmlstr + selOneHtml(selectList[val]) + "</div>";

				return htmlstr;
			}

			$('.select_group').append(thehtml(curSel));


			var sel = $('.select_group .select_div');
			sel.bind('click', function(){
				console.log('click');
				initSelectResult();
				$('.select_group .select_result li').click(function(){
					var b = $(this).attr('data-val');
					initCurrentSelect(b);
					$('.select_group .select_result').remove();
				});
			});
		}

		function initSelectResult(){
			//init select-result
			var curSel = select.val();
			if($('.select_group .select_result').get()[0])
				$('.select_group .select_result').remove();

			$('.select_group').append(thehtml(curSel));

			function thehtml(val){
				var htmlstr = '';
				htmlstr += '<div class="select_result"> <ul>';
				htmlstr += resSelOneHtml(val, true);
				for(var i in selectList){
					if(i !=  val)
						htmlstr += resSelOneHtml(i,false);
				}
				return htmlstr + '</ul></div>';
			}
			function resSelOneHtml(val,flag){
				var htmlstr = '';
				htmlstr += '<li class="selResOne" data-val="'+val+ '">';
				htmlstr += '<p class="left name">'+ selectList[val]+'</p>';
				htmlstr += flag ? '<span class="personal_icon arrow_up right"></span>' :'';
				htmlstr += '<div class="clear"></div>';
				htmlstr += '</li>';

				return htmlstr
			}
		};

		function selOneHtml(text){
			var htmlstr = '';
			htmlstr += '<p class="left name">'+ text+'</p>';
			htmlstr += '<span class="personal_icon arrow_bt right"></span>';
			htmlstr += '<div class="clear"></div>';

			return htmlstr;
		}

	}())
	$(document).ready(function(){
		(function(a){
			var ic = $('#identCode');
			var icet = $('#identCodeETime');

			a('.control-group.write-text').find('input[type=text]').focus(function(){
				$(this).parent().find('.errInfo').remove();
			});
			a('.control-group.write-text').find('input[type=text]').blur(function(){
				if(!a.trim(a(this).val())){
					$(this).parent().find('.errInfo').remove();
					$(this).parent().append(err('内容不能为空'));
				}
			});
			ic.find('input[type=text]').blur(function(){
				if(!identityCodeVaild(a(this).val())){
					$(this).parent().find('.errInfo').remove();
					$(this).parent().append(err('证件号码格式错误'));
				}
			});

			(function(){
				//年月日匹配
				var group = icet.find('.controls input[type=text]').get();
				var year = a(group[0]);
				var month = a(group[1]);
				var day = a(group[2]);
				var i = 0;
				year.bind('input propertychange', function(){
//					var value =  a(this).val();
					var real_val = a(this).val();

					if(real_val.length > 4){
						 real_val = value.substr(0, 4);
						a(this).val(real_val);
					}

					if(real_val.length == 4){
						if(/^(1[89]|20)\d{2}$/.test(real_val)){
							month.focus()
						}else{
							real_val = real_val.substr(0, 3);
							a(this).val(real_val);
						}
					}
					if(real_val.length == 3){
						if(!/^(1[89]|20)\d{1}$/.test(real_val)){
							real_val = real_val.substr(0,2);
							a(this).val(real_val);
						}
					}
					if(real_val.length == 2){
						if(!/^(1[89]|20)$/.test(real_val)){
							real_val = real_val.substr(0,1);
							a(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^1|2$/.test(real_val)){
							real_val = '';
							a(this).val(real_val);
						}
					}
					console.log(real_val);
				});
				month.bind('focus', function(){
					if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val()))
						year.focus();
				});
				month.bind('blur', function(){
					if(/^[1-9]$/.test(month.val()))
						a(this).val('0' + month.val());
				});
				month.bind('input propertychange', function(){
					var real_val = a(this).val();

					if(real_val.length > 2){
						real_val = real_val.substr(0, 2);
						a(this).val(real_val);
					}

					if(real_val.length == 2){
						if(/^0[1-9]|1[0-2]$/.test(real_val)){
							day.focus()
						}else{
							real_val = real_val.substr(0,1);
							a(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^[0-9]$/.test(real_val)){
							real_val = '';
							a(this).val(real_val);
						}
					}

				});
				day.bind('focus', function(){
					if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val())){
						year.focus();
						return;
					}
					if(!/^(0[1-9]|1[0-2])/.test(month.val())){
						month.focus();
					}
				});
				day.bind('blur', function(){
					var y = year.val();
					var m = month.val();
					var d = day.val();

					if(/^[1-9]$/.test(d)){
						d = '0' + d;
						day.val(d);
					}

					if(!checkDate(y,m,d))
						a(this).parent().append(err('日期错误'));
				});
				day.bind('input propertychange', function(){
					var real_val = a(this).val();
					if(real_val.length > 2){
						real_val = real_val.substr(0,2);
						a(this).val(real_val);
					}
					if(real_val.length ==  2){
						var m = month.val();
						var reg = '';
						if(/^0[13578]|1[02]$/.test(m)){
							reg = /^0[1-9]|[12]\d{1}|3[01]/;
						}else{
							if(m == '02'){
								var leapYear = isLeapYear(parseInt(year.val()));
								reg = leapYear ? /^0[1-9]|1\d{1}|2[0-9]$/ : /^0[1-9]|1\d{1}|2[0-8]/;
							}else{
								reg = /^0[1-9]|[12]\d{1}|30/;
							}
						}
						if(!reg.test(real_val)){
							real_val = real_val.substr(0,1);
							a(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^[0-3]$/.test(real_val)){
							real_val = '';
							a(this).val('');
						}
					}
				});


			}());
		}(jQuery));

		(function(a){
			$('.upload input[type=file]').change(function(){
				upload_pic(a(this).attr('id'));
			});

			function upload_pic(type){
				var form = null;

				form = $("#upload_" +type);
				if(!form) return;

                var fileUploadRes = checkUploadImage($('#'+type).get()[0], $conf.uploadImgSize);
                if(fileUploadRes < 0){
                    console.log(fileUploadRes);
					//todo failed handle
                    return;
                }

				var option = {
					url:'upload_identPic_ajax.php',
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						type:type
					},
					uploadProgress:function(event, position, totla, percentComplete){
						console.log(percentComplete);
					},
					success:function(d){
						if(d.isSuccess == 1){
							form.parent().find('.pic').html('<img src="'+d.img+'">');
						}
						//todo failed handle
					}
				}
				form.ajaxSubmit(option);
			}
		}(jQuery));

		(function(a){

			var group = $('#identCodeETime').find('.controls input[type=text]').get();
			var year = a(group[0]);
			var month = a(group[1]);
			var day = a(group[2]);

			$('#commit').bind('click', function(){
				var rname = $('#identName .controls input').val();
				var identId = $('#identCode .controls input').val();

				if(!rname || !checkName(rname)){
					console.log('姓名出错');
					return;
				}
				if(!identId || !identityCodeVaild(identId)){
					console.log('身份证号码出错');
					return;
				}
				if(!checkDate(year.val(), month.val(), day.val())){
					console.log('日期错误')
					return;
				}
				var front = $('#front').val();
				var back = $('#back').val();
				var handheld = $("#handheld").val();

				if(!front || !back || !handheld){
					console.log('请上传文件')
					return;
				}
				var date = year.val() + '-' + month.val() + '-' + day.val();

				var option = {
					url:'../mp_ajax/certIdent_ajax.php',
					type:'post',
					dataType:'json',
					data:{
						uid:getCookie('_uid'),
						encpass:getCookie('_enc'),
						name:rname,
						identID:identId,
						outTime:date
					},
					success:function(d){
						location.href = $conf.person + 'mp';
					}
				}
				a.ajax(option);
				function checkName(name){
					if(!name || name.length >= 10)
						return false;
					return true;
				}

			});
		}(jQuery));
	});
</script>
</body>
</html>