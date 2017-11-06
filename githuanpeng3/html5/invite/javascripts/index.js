$(function() {
    var inAppDom = '<section>\
                        <button id="fast-request"></button>\
                    </section>';
    var outAppDom = '<section>\
                        <input id="phoneNum" type="number" maxlength="11" placeholder="请输入手机号" onfocus="this.placeholder=""" onblur="this.placeholder="请输入手机号"">\
                        </section>\
                        <section>\
                        <button id="getAward-btn"></button>\
                        </section>';

    function successDom(a) {
        return '<section>\
                    <button id="fast-do"></button>\
                    <p class="awrad-number">奖励已放入账号: ' + a + '</p>\
                </section>';
    }
    var invite = {
        init: function() {
            this._initStatus();
            this._initCreateInviteReceive();
        },
        _initStatus: function() {
            //如果在应用内
            if (window.huanpengShare || window.phonePlus || window.appCloseWebPage || window.appLogin) {
                $('.hp-content').html(inAppDom);
            } else {
                $('.hp-content').html(outAppDom);
            }

            invite._initDOMEvent();
        },
        _initCreateInviteReceive: function() {
            //预处理, 统计
            var code = QueryLocation().code || '';
            if (!code || code == '') {
                return;
            }
            var requestUrl = $conf.api + 'activity/preCreateInviteReceive.php';
            var requestData = {
                code: code
            }
            ajaxRequest({ url: requestUrl, data: requestData }, function(responseData) {
                console.log(responseData);
            })
        },
        _initDOMEvent: function() {
            //立即分享按钮
            $('#fast-request').click(function() {
                if (window.phonePlus || phonePlus || window.huanpengShare) {
                    phonePlus.turnTo('share');

                } else if (isIphoneClient()) {
                    //iOS分享
                    hpturnTo('share');
                }
            });

            //立即领取 按钮
            $('#getAward-btn').click(function() {
                var phoneNum = $('#phoneNum').val();
                if (!phoneNum || phoneNum == '' || phoneNum.length < 11 || !(/^1[34578]\d{9}$/.test(phoneNum))) {
                    mobileTips('手机号错误');
                    return;
                }

                var requestUrl = $conf.api + 'activity/getReward.php';
                var requestData = {
                    code: QueryLocation().code || '',
                    phone: phoneNum
                };
                ajaxRequest({ url: requestUrl, data: requestData }, function(responseData) {
                    mobileTips(responseData);
                    $('.hp-content').html(successDom(phoneNum));
                    $('#fast-do').click(invite._initLinkToH5);
                    return;
                }, function(responseData) {
                    mobileTips(responseData.desc);
                })
            });


        },
        _initLinkToH5: function() {
            location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.mizhi.huanpeng';
        }
    };
    invite.init();
});

function QueryLocation() {
    var b = location.href;
    var c = new Object();
    if (b.indexOf('?') > -1) {
        var str = b.substr(b.indexOf('?') + 1);
        var strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            c[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
        }
    }
    return c;
}

function isIphoneClient() {
    var ua = navigator.userAgent.toLowerCase();
    return /(iphone|ipad|ipod)/.test(ua);
}

function mobileTips(a) {
    $('#error-content').text(a);
    $('.modal-box, .error-modal').show();
    setTimeout(function() {
        $('.modal-box, .error-modal').hide();
    }, 1000)
}