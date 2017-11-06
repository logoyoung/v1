$(function () {
    


    var $confApi = $conf.api;
    var $domain = $conf.domain;

    var iosLogin = {
        init: function () {
            this._login();
            // this._getCode();
            this._encodeLink();
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

                            var hrefUrl = '';
                            var sourceUrl = getCookie('ref_url');
                            console.log('sourceUrl',sourceUrl);
                            if(sourceUrl && sourceUrl !== '') {
                                hrefUrl = sourceUrl;
                            }else {
                                hrefUrl = $domain + 'mobile';
                            }
                            location.href = decodeURIComponent(hrefUrl);
                        }
                    });
                }
            });
        },
        // encode第三方连接
        _encodeLink() {
            var sourceUrl = getCookie('ref_url');
            var encodeRef = '';
            var weixinUrl = $domain + '/personal/oauth/index.php?isWxClient=1&channel=wechat&order=login';
            var qqUrl = $domain + '/personal/oauth/index.php?channel=qq&order=login';
            var weiboUrl = $domain + '/personal/oauth/index.php?channel=weibo&order=login';
            if(sourceUrl && sourceUrl !=='') {
                encodeRef = encodeURIComponent(sourceUrl);
                weixinUrl += '&ref_url='+encodeRef+'&client=h5';
                qqUrl += '&ref_url='+encodeRef+'&client=h5';
                weiboUrl += '&ref_url='+encodeRef+'&client=h5'; 
            }
            $('#linkWeixin')[0].href = weixinUrl;
            $('#linkQQ')[0].href = qqUrl;
            $('#linkWeibo')[0].href = weiboUrl;
        }
    };
    iosLogin.init();


    
})