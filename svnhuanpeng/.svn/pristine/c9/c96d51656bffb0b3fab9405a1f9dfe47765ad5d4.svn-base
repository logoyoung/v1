$(function () {


    var $confApi = $conf.api;
    var $domain = $conf.domain;

    var iosLogin = {
        init: function () {
            this._login();
            // this._getCode();
        },
        _login: function () {
            var _this = this;
            $('#loginBtn').on('click', function (e) {
                var formData = $('#loginForm').serialize();
                var loginPhone = $('#loginPhone').val();
                var loginPassword = $('#loginPassword').val();
                if(loginPhone && loginPassword) {
                    loginRequest();
                }else {
                    layer.msg('请填写完整信息')
                }
                function loginRequest() {
                    $.ajax({
                        type: 'POST',
                        url: $confApi + 'user/logIn.php',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            var resData = data.content
                            if (data.status === '0') {
                                layer.msg(resData.desc);
                                if(resData.code === '-4061') {
                                    geetest({product:'popup',append:'#geetest'});
                                }
                                return null;
                            }
                            var uid = resData.uid;
                            var encpass = resData.encpass;
                            setCookie('_uid',uid);
                            setCookie('_enc',encpass);

                            var hrefUrl = $conf.domain + 'mobile';
                            location.assign(hrefUrl);
                        }
                    });
                }
            });
        }
    };
    iosLogin.init();
})