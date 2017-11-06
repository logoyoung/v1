$(function() {

	var $confApi = $conf.api;
	var $domain = $conf.domain;
	var geetestLock = 0;

	var register = {
		init: function() {
			this._register();
			this._getCode();
		},
		_register: function() {
			$('#agreement').on('change',function() {
				var checked = $(this).prop('checked');
				if(checked) {
					$('#registerBtn').prop('disabled',false);
				}else {
					$('#registerBtn').prop('disabled',true);
				}
			});
			$('#registerBtn').on('click',function() {
				var formData = $('#registerForm').serialize();
				var registerName = $('#registerName').val();
				var registerPassword = $('#registerPassword').val();
				if(registerName && registerPassword) {
					registerRequest();
				}else {
					layer.msg('请填写完整注册信息');
				}
				function registerRequest() {
                    $.ajax({
                        type: 'POST',
                        url: $confApi + 'user/registered.php',
                        data: formData,
                        success: function(data) {
                            if(data.status === '0') {
                                layer.msg(data.content.desc);
                            }else {
                                $('#register').hide();
                                $('#approveCheck').show();
                            }
                        }
                    });
				}
			});
		},
		_getCode: function() {
			var num = 60;
			$('#mobileCode').on('click',function(e) {
                e.preventDefault();

				var _this = $(this);
				var mobile = $('#registerPhone').val();
				geetest2({product:'popup',append:'#geetest'},function(data) {

					geetestLock = 0;

					var data = $.extend({mobile:mobile,type:'gt',from:'0'},data);
					$.ajax({
						type:'POST',
						url: $confApi + 'code/mobileCode.php',
						data: data,
						success: function(data) {
							console.log(data);
							if(data.status === '0') {
								layer.msg(data.content.desc);
							}else {
								loop(_this);
							}			
						}
					});
				},function() {

				});

			});
			
			function loop($dom) {
				console.log('success');
				if(num === 0) {
					$dom.prop('disabled',false).text('获取验证码');
					num = 60;
				}else {
					$dom.prop('disabled',true).text(num + 's');
					num--;
					setTimeout(function(){loop($dom)},1000);
				}
			}
		}
	};

	register.init();

    
});