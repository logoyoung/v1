/**
 * Created by junxiao on 2017/6/28.
 */
//认证 初始 DOM
var statusStart = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-start-logo.png"></div>\
                                <p class="logo-title">认证欢朋主播，起航明星之旅</p>\
                                <a href="./sesame.html?' + new Date().getTime() + '" class="sesame-btn">芝麻快速认证</a>\
                                <a href="./beAnchor.html?' + new Date().getTime() + '" class="normal-btn">普通认证</a>\
                              </div>';
var zhimaStart = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-start-logo.png"></div>\
                                <p class="logo-title">认证欢朋主播，起航明星之旅</p>\
                                <a href="./sesame.html?' + new Date().getTime() + '" class="sesame-btn">芝麻快速认证</a>\
                                </div>';

var normalStart = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-start-logo.png"></div>\
                                <p class="logo-title">认证欢朋主播，起航明星之旅</p>\
                                <a href="./beAnchor.html?' + new Date().getTime() + '" class="normal-btn">普通认证</a>\
                                </div>';


var statusFailed = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-failed-logo.png"></div>\
                                <p class="logo-title">您的审核失败，请重新认证</p>\
                                <a href="./beAnchor.html?' + new Date().getTime() + '" class="btn">开始认证</a>\
                              </div>';

var statusSuccess = '<div class="anchor-container" style="position: absolute;z-index: 1;">\
                            <div class="anchor-header">\
                            <figure>\
                            <div class="anchor-poster">\
                            <img class="anchor-img" src="./image/userface.png">\
                            <div class="anchor-checked"></div>\
                            </div>\
                            <figcaption>\
                            <h2 class="anchor-name">&nbsp;&nbsp;</h2>\
                        <p class="anchor-roomID">&nbsp;&nbsp;</p>\
                        <p class="anchor-desc">\
                            <span class="anchorLvl_icon"></span>距离升级还差<span class="anchor-intergal">0</span>经验值</p>\
                            </figcaption>\
                            </figure>\
                            <div class="economics-btn">\
                            	<a href="managerApply.html?init=true" class="signing-status clearfix" id="applyStatus">\
									<span class="l" style="margin-top:0.09rem">经纪公司</span>\
									<i class="fa fa-chevron-right r"></i>\
									<span class="status r" style="color: #999;"></span>\
								</a>\
                            </div>\
                            </div>\
                            <div class="anchor-content">\
                            <div class="content-header">\
                            <span style="margin-top:0.1rem">直播时长</span>\
                            <button class="btn" data-value="last">上月</button>\
                            <button class="btn sel" data-value="now">本月</button>\
                            </div>\
                            <div class="content-data">\
                            <div id="dataForm" class="data-from"></div>\
                            <div class="data-desc">\
                            <p class="desc">共直播</p>\
                            <p class="data">\
                            <span class="bold">&nbsp;&nbsp;</span>\
                        <span class="bold">&nbsp;&nbsp;</span>\
                        </p>\
                        </div>\
                        </div>\
                        <div class="content-footer">\
                            <div class="footer-content">\
                            <div class="footer-grid">\
                            <p class="hpcolor" id="live_today">\
                            <span class="bold">&nbsp;&nbsp;</span>\
                        <span class="bold">&nbsp;&nbsp;</span>\
                        </p>\
                        <p class="desc">今日直播时长</p>\
                            </div>\
                            <div class="footer-grid">\
                            <p class="hpcolor" id="live_vaild">\
                            <span class="bold">&nbsp;&nbsp;</span>\
                        </p>\
                        <p class="desc">本月有效天数</p>\
                            </div>\
                            <div class="footer-grid">\
                            <p class="hpcolor" id="live_max">\
                            <span class="bold">&nbsp;&nbsp;</span>\
                        </p>\
                        <p class="desc">本月人气峰值</p>\
                            </div>\
                            </div>\
                            <div class="footer-desc">\
                            <p>提示 : 有效天数为每天满足1小时的天数</p>\
                        </div>\
                        </div>\
                        </div>\
                        </div>';

var statusLoading = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-wait-logo.png" alt=""></div>\
                                <p class="notice">主播认证已经提交，请耐心等待</p>\
                                <a id="return_anchor" class="btn">返回个人中心</a>\
                              </div>';

var phoneNobind = '<div class="block-insert">\
                                <div class="logo"><img src="image/check-failed-logo.png"></div>\
                                <p class="logo-title">您手机还没有绑定账号哦</p>\
                                <a id="certPhone" class="btn">绑定手机</a>\
                              </div>';

var pageFooter = '<div class="show-block">\
                            <span class="item-1"></span>\
                            <p>游戏炫技，展现才华</p>\
                        </div>\
                       <div class="show-block">\
                            <span class="item-2"></span>\
                            <p>成名机会，万千粉丝</p>\
                        </div>\
                       <div class="show-block">\
                            <span class="item-3"></span>\
                            <p>额外收入，月入百万</p>\
                        </div>';
var pageTips = '<p>提示：</p>\
                <p>1.根据相关规定开播需要实名认证，认证信息欢朋会严格保密；</p>\
                <p>2.“芝麻认证”由芝麻信用提供，安全快捷；</p>\
                <p>3.“普通认证”由官方审核，1~3个工作日完成认证。</p>';

function statusCheck() {

    var uid = sessionStorage.getItem('_uid');
    var encpass = sessionStorage.getItem('_enc');
    if (!uid || !encpass) {
        if (isIphoneClient()) {
            window.appLogin();
        } else {
            window.phonePlus.turnTo('unlogin')
        }
        return false;
    }

    var requesetUrl = $conf.api + 'user/attested/rpc_ajax.php';
    var requestData = {
        uid: uid,
        encpass: encpass
    };
    ajaxRequest({ url: requesetUrl, data: requestData }, function(responseData) {

        var phone = responseData.phone;
        var phoneStatus = responseData.phonestatus;
        if (!phone || !phoneStatus || phoneStatus == '0') {
            $('.page-body').html(phoneNobind);
            $('.page-foot').html(pageFooter);
            $('#certPhone').click(function() {
                if (isIphoneClient()) {
                    window.appBindPhone && (window.appBindPhone());
                } else if (window.phonePlus) {
                    window.phonePlus.turnTo('certPhone');
                }
            });
            return;
        } else {
            sessionStorage.setItem('_phone', phone);
        }

        var isAnchor = responseData.isAnchor;
        var identstatus = responseData.identstatus;

        switch (parseInt(identstatus)) {
            case 0:
                //未提交信息
                //display_cert_channel  : 1  普通认证    
                //                      : 2  芝麻认证
                //                      : 3  显示所有
                //                      : 0  关闭所有
                if (responseData.display_cert_channel == 1) {
                    $('.page-body').html(normalStart);
                } else if (responseData.display_cert_channel == 2) {
                    $('.page-body').html(zhimaStart);
                } else if (responseData.display_cert_channel == 3) {
                    $('.page-body').html(statusStart);
                } else if (responseData.display_cert_channel == 0) {

                }

                $('.page-foot').html(pageFooter);
                $('.page-tips').html(pageTips);
                break;
            case 1:
                //审核中
                $('.page-body').html(statusLoading);
                $('.page-foot').html(pageFooter);
                //返回个人中心
                $('#return_anchor').click(function() {
                    if (window.phonePlus) {
                        window.phonePlus.turnTo('index');
                    } else if (isIphoneClient()) {
                        window.appCloseWebPage();
                    }
                });
                break;
            case 100:
                //审核失败
                $('.page-body').html(statusFailed);
                $('.page-foot').html(pageFooter);
                break;
            case 101:
                //审核成功
                $('body').html(statusSuccess);
                dataforMonth('now', 'init');
                break;
            default:
                //未提交信息
                //display_cert_channel  : 1  普通认证    
                //                      : 2  芝麻认证
                //                      : 3  显示所有
                //                      : 0  关闭所有
                if (responseData.display_cert_channel == 1) {
                    $('.page-body').html(normalStart);
                } else if (responseData.display_cert_channel == 2) {
                    $('.page-body').html(zhimaStart);
                } else if (responseData.display_cert_channel == 3) {
                    $('.page-body').html(statusStart);
                } else if (responseData.display_cert_channel == 0) {

                }
                $('.page-body').html(statusStart);
                $('.page-foot').html(pageFooter);
                $('.page-tips').html(pageTips);
                break;
        }

    })

}