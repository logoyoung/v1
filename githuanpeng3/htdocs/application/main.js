/**
 * Created by hantong on 16/11/16.
 */

function getTitle(){
	if(isIphoneClient()){
		return pageTitle;
	}
	if(window.phonePlus){
		try {
			window.phonePlus.setTitle(pageTitle);
		}catch(e){
			console.log(e);
		}
	}
}
function alertMsg(msg){
	if(window.phonePlus){
		window.phonePlus.showError(msg);
	}else{
		if(isIphoneClient()){
			tips(msg);
		}else{
            alert(msg);
		}
	}
}


function getParams(uid,encpass){
	setCookie('_uid',uid);
	setCookie('_enc',encpass);
}

function isIphoneClient(){
	var ua = navigator.userAgent.toLowerCase();
	return /(iphone|ipad|ipod)/.test(ua);
}
var app ={};
;(function (uncert,phone,realName,finish) {

	var currentPage = window.currentPage;

	if(!check_user_login() || loginError){
		if(isIphoneClient()){
		    alertMsg('the user is not login');
			window.appLogin && (window.appLogin());
		}
		//todo link to login
		return;
	}


	var pageList = {
		'unCertPhone':uncert,
		'phone':phone,
		'realName':realName,
		'finish':finish
	}

	$('.header .icon.back').bind('click',function () {
		if(window.phonePlus){
			window.phonePlus.turnTo('index');
		}else if(isIphoneClient()){
			window.appEnterUserInfo && (window.appEnterUserInfo());
		}
	})

	app.init = (function(){
		if (currentPage && typeof pageList[currentPage] == 'function')
			return pageList[currentPage];
		else
			return function () {}
	}());

}(function(){
	//uncert
	return function () {
		$('.to-certPhone').bind('click', function(){

			if(isIphoneClient()){
				window.appBindPhone && (window.appBindPhone());
			}else if(window.phonePlus){
				window.phonePlus.turnTo('certPhone');
			}else{
				location.href = $conf.person;//跳转到认证页面
			}
		});
	}
}(), function(){
	//phone
	return function () {
		var time = 0;
		var agreeRule = false;

		var requestGetMobileCode = function (doCallBack) {
			var requestUrl = $conf.api + 'code/mobileCode.php';
			var requestData = {
				uid:getCookie('_uid'),
				encpass:getCookie('_enc'),
				from:3,
				//mobile:''
			};

			ajaxRequest({url:requestUrl,data:requestData},function (d) {
				typeof doCallBack =='function' && doCallBack();
			},function(d){
				alertMsg(d.desc);
			});
		}
		$('#get-mobile-code').bind('click', function(){
			if(time > 0)
				return;
			var self = this;
			requestGetMobileCode(function () {
				time = 60;
				var interval = setInterval(function () {
					if(time > 1){
						$(self).text(time + 's');
					}else{
						clearInterval(interval);
						$(self).text('重新发送');
					}
					time --;
				},1000)
			});
		});

		var requestCheckMobileCode = function (code, fn,fn2) {
			if(!code)
				return;
			var requestUrl = $conf.api + 'check/checkCode.php';
			var requestData = {
				uid:getCookie('_uid'),
				encpass:getCookie('_enc'),
				mobileCode:code
			};

			ajaxRequest({url:requestUrl,data:requestData},function (d) {
				typeof fn == 'function' && fn(d);
			},function (d) {
				typeof fn2 == 'function' && fn2(d);
			})
		}
		var failedHandle = function (code) {
			alertMsg(code.desc);
		}
		var succHandle = function (code) {
			setCookie('apply_anchor_mobile_code', code);
			location.href = location.href;
		}

		$('.agree-rule .check-box-block .check-box, .agree-rule .check-box-block span').bind('click',function () {
			var agreeDom = $('.agree-rule .check-box-block .check-box');
			var isChecked = agreeDom.hasClass('checked');
			if(isChecked){
				agreeDom.removeClass('checked');
				$('.check-phone .commit .btn').addClass('disabled');

				agreeRule = false;
			}else{
				agreeRule = true;
				agreeDom.addClass('checked');
				$('.check-phone .commit .btn').removeClass('disabled');
			}
		})

		$('.check-phone .commit .btn').bind('click',function () {

			// if(isIphoneClient()){
			// 	window.appEnterVideoRoom('2290','http://img.huanpeng.com/live/e/2/e22dd5dabde45eda5a1a67772c8e25dd.jpeg?1484625514');
			// 	return;
			// }

			if($(this).hasClass('disabled')) return;
			if(!agreeRule){
				alertMsg('认证主播需要同意主播协议');
				return;
			}
			var code = $('#pass-code').val();
			if(!code){
				alertMsg('验证码错误');
				return;
			}
			requestCheckMobileCode(code,function(d){succHandle(code)},function(d){failedHandle(d)});
		})
	}
}(),function(){
	//realname
	return function () {
		deleteCookie('apply_anchor_mobile_code');

		/*$('.person-code .controls input[type=text]').bind('blur',function () {
			if(!identityCodeVaild($(this).val())){
				alertMsg('证件号码格式错误');

			}
		})*/
        $('.person-code .controls input[type=text]').blur(function () {
        	var val = $(this).val();
            if(!identityCodeVaild(val)){
                alertMsg('证件号码格式错误');
            }
        });
		//personal etime check
		!function(){
			var group = $('.card-etime .controls input[type=text]').get();
			var year = $(group[0]),
				month = $(group[1]),
				day = $(group[2]);

			year.bind({
				'input propertychange':function () {
					var real_val = $(this).val();

					if(real_val.length > 4){
						real_val = value.substr(0, 4);
						$(this).val(real_val);
					}

					if(real_val.length == 4){
						if(/^(1[89]|20)\d{2}$/.test(real_val)){
							month.focus()
						}else{
							real_val = real_val.substr(0, 3);
							$(this).val(real_val);
						}
					}
					if(real_val.length == 3){
						if(!/^(1[89]|20)\d{1}$/.test(real_val)){
							real_val = real_val.substr(0,2);
							$(this).val(real_val);
						}
					}
					if(real_val.length == 2){
						if(!/^(1[89]|20)$/.test(real_val)){
							real_val = real_val.substr(0,1);
							$(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^1|2$/.test(real_val)){
							real_val = '';
							$(this).val(real_val);
						}
					}
				}
			});
			month.bind({
				'focus':function () {
					if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val()))
						year.focus();
				},
				'blur':function () {
					if(/^[1-9]$/.test(month.val()))
						$(this).val('0' + month.val());
				},
				'input propertychange':function () {
					var real_val = $(this).val();

					if(real_val.length > 2){
						real_val = real_val.substr(0, 2);
						$(this).val(real_val);
					}

					if(real_val.length == 2){
						if(/^0[1-9]|1[0-2]$/.test(real_val)){
							day.focus()
						}else{
							real_val = real_val.substr(0,1);
							$(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^[0-9]$/.test(real_val)){
							real_val = '';
							$(this).val(real_val);
						}
					}
				}
			});
			day.bind({
				'focus':function(){
					if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val())){
						year.focus();
						return;
					}
					if(!/^(0[1-9]|1[0-2])/.test(month.val())){
						month.focus();
					}
				},
				'blur':function () {
					var y = year.val();
					var m = month.val();
					var d = day.val();

					if(/^[1-9]$/.test(d)){
						d = '0' + d;
						day.val(d);
					}
					if(!checkDate(y,m,d))
						alertMsg('日期错误')
				},
				'input propertychange':function () {
					var real_val = $(this).val();
					if(real_val.length > 2){
						real_val = real_val.substr(0,2);
						$(this).val(real_val);
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
							$(this).val(real_val);
						}
					}
					if(real_val.length == 1){
						if(!/^[0-3]$/.test(real_val)){
							real_val = '';
							$(this).val('');
						}
					}
				}
			});
		}();

		$('.upload input[type=file]').change(function () {
			upload_pic($(this).attr('id'));
		})
		function upload_pic(type) {
			var form = $('#upload_' + type);
			if(!form) return;
			var fileUpLoadRes = checkUploadImage($('#'+type).get()[0], $conf.uploadImgSize);
			if(fileUpLoadRes < 0){
				alertMsg('文件不符合标准');
				return;
			}
			form.ajaxSubmit({
				url:$conf.api + 'user/attested/upload_identPic_ajax.php',
				type:'post',
				dataType:'json',
				data:{
					uid:getCookie('_uid'),
					encpass:getCookie("_enc"),
					type:type
				},
				uploadProgress:function(event, position, totla, percentComplete){

				},
				success:function (d) {
					console.log(d);
					console.log(d.status == 1);
					if(d.status == '1'){
						var data = d.content;
						form.parent().find('.pic img').attr('src',data.img);
					}else{
						alertMsg('图片上传失败');
						// todo failed handle;
					}
				},
				error:function (e) {
					console.log(e);
					alertMsg('文件不符合标准');
                }
			});
		}

		//submit
		$('.real-name .commit .btn').bind('click',function () {
			var rname = $('input[name=user-name]').val();
			var cardcode = $('input[name=person-code]').val();
			var year = $("input[name=year]").val();
			var month = $('input[name=month]').val();
			var day = $('input[name=day]').val();
			var front = $("#front").val();
			var back = $('#back').val();
			var handheld = $('#handheld').val();

			if(!rname || !checkName(rname)){
				alertMsg('姓名不符合格式');
				return;
			}
			if(!cardcode || !identityCodeVaild(cardcode)){
				alertMsg('身份证号码错误');
				return;
			}
			if(!checkDate(year, month, day)){
				alertMsg('日期错误');
				return;
			}
			if(!front || !back || !day){
				alertMsg('请上传身份证照片');
				return;
			}
			var date = year + '-' + month + '-' + day;
			var url = $conf.api+'user/attested/certIdent_ajax.php';
			var data = {
				uid:getCookie('_uid'),
				encpass:getCookie('_enc'),
				name:rname,
				identID:cardcode,
				outTime:date
			};

			ajaxRequest({url:url,data:data},function () {
				location.href = location.href;
			},function (d) {
				if(d.type == 1){
					alertMsg('认证失败');
				}else {
					alertMsg(d.desc);
				}
			});
		});

		function checkName(name){
			if(!name || name.length >= 10)
				return false;
			return true;
		}
	}
}(),function(){
	//finish
	return function(){
		$('.commit a').bind('click',function(){
			if(isIphoneClient()){
				//h5 页面
				// location.href =
			}else if(window.phonePlus){
				window.phonePlus.turnTo('anchorPage');
				return false;
			}else{
				location.href = $conf.person+'homepage/';
			}
		});
	}
}()));