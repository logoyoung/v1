$(function () {


    var $confApi = $conf.api;
    var $domain = $conf.domain;

    var iosLogin = {
        init: function () {
            this._login();
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
                        dataType:'json',
                        success: function (data) {
                            var resData = data.content;
                            if (data.status === '0') {
                                layer.msg(resData.desc);

                                if(resData.code === '-4061') {
                                    geetest({product:'popup',append:'#geetest'});
                                }

                            } else {
                                $('#login').hide();
                                var isAnchor = resData.isAnchor;
                                if (isAnchor === '1') {
                                    _this._goDownLoad();
                                } else {
                                    _this._goApproveCheck();
                                }
                            }
                        }
                    });
                }
            });
        },
        _goApproveCheck: function () {
            $('#approveCheck').show();
            $('#approveCheck').find('.btn').on('click', function () {
                var approveSrc = $domain + 'application/index.php?page=phone';
                window.location.href = approveSrc;
            });
        },
        _goDownLoad: function () {
            var downLoadSrc = $domain + 'mobile/ios_download/index.html';
            window.location.href = downLoadSrc;
        }
    };

    iosLogin.init();


})