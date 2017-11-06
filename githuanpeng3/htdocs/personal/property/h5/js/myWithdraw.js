/**
 * Created by junxiao on 2017/5/4.
 */

var postUid = '';
var postEncpass = '';
var jsonCoin = null;
var bankCardNum = '';
var withDrawData = null;

var stepStatus = {
    '0'     :   '_noWithDraw',    //没有提现
    '2'     :   '_withDrawProgress',    //提现审核中
    '3'     :   '_withDrawSuccess',    //提现成功
    '4'     :   '_withDrawFail',    //提现失败
    ''      :   '_tipsError'     //空值判断
};
var _errorCode = {
    '-4013'          :   '提现失败,请联系客服',
    '-4086'          :   '您还没有绑定银行卡',
    '-4102'          :   '提现时间是每月25日以后',
    '-4107'          :   '兑换6月1日正式启用',
    '-5023'          :   '您的账户余额不足!',
    '-4103'          :   '每月只能提现一次哦',
    '-4097'          :   '经纪公司签约主播暂不支持兑换哦',
    '-5030'          :   '悲剧咯,你被关进小黑屋咯',
    ''               :   '提现失败,请联系客服'
};

$(function () {
    mobileLoading(1);
    if(sessionStorage && sessionStorage.getItem('coinAll')){
        var stringCoin = sessionStorage.getItem('coinAll');
        jsonCoin = JSON.parse(stringCoin);
        postUid = jsonCoin.uid;
        postEncpass = jsonCoin.encpass;
    }
    //轮询更改状态
    var timer = setInterval(function () {
        if (sessionStorage && sessionStorage.getItem('_bindRefresh')){
            sessionStorage.removeItem('_bindRefresh');
            location.reload();
        }
    },50);
    var withdraw = {
        init : function () {
            withdraw._initProgress();
        },
        _initProgress : function () {
            var rqUrl = $conf.api + 'user/revise/cashAdvance.php';
            var rqData = {uid:postUid,encpass:postEncpass};
            ajaxRequest({url:rqUrl,data:rqData},function (responseData) {
                var index = responseData.step;
                withDrawData = responseData;
                if(stepStatus[index]){
                    eval('withdraw.'+ stepStatus[index] +'()');
                }
            },function (responseData) {
                var code = responseData.code;
                if(_errorCode[code]){
                    mobileTips(_errorCode[code]);
                    setTimeout(function () {
                        if(window.history){
                            window.history.go(-1);
                        }else{
                            history.go(-1);
                        }
                    },3000);
                }else{
                    mobileTips('账号异常,请稍后重试!');
                    setTimeout(function () {
                        if(window.history){
                            window.history.go(-1);
                        }else{
                            history.go(-1);
                        }
                    },3000);
                }
            })
        },

        _noWithDraw : function () {
            withdraw._initNoWithDrawDom();
            withdraw._initBankStatus();
            withdraw._initBindBank();
            mobileLoading(0);
            withdraw._initWithDraw();
        },
        _initNoWithDrawDom : function () {
            var tpl = '<div class="withdraw-container">\
            <div class="withdraw-Loca">\
                <div class="row">\
                <p class="text-inlineBlock">金币数额</p>\
                <input id="money-withdraw" type="number" min="0" max="1200" maxlength="4" placeholder="" class="noBorder">\
                </div>\
                <!--<p class="text-inlineBlock">金豆数额</p>-->\
                <!--<input type="number" min="0" max="1200" placeholder="最多可提取1,200金豆" class="noBorder">-->\
                <!--</div>-->\
                <!--<span class="downline"></span>-->\
                </div>\
                <!--兑换提示区-->\
                <div class="withdraw-tip">\
                <p class="desc">1个金币可提现1元,金币超过 100 个才可兑换。</p>\
            <p class="withTo">提现到</p>\
                </div>\
                <div class="withdraw-bind clearfix">\
                <div class="cardIco"></div>\
                <div class="bindDesc">\
                <p class="cardTitle">银行卡</p>\
                <p class="cardDesc"></p>\
                </div>\
                <div class="arrow"></div>\
                </div>\
                <div class="withdraw-show clearfix" style="visibility: hidden;">\
                <p class="moneyDesc">共计:<span id="needMoney"></span>\
                <span class="mitl">元</span>(本月10～20 日到账)</p>\
            </div>\
            <!--提现btn-->\
            <div class="withdraw-btn">\
                <button id="wdBtn" class="withDrawBtn">提现</button>\
                </div>\
                <!--protocol-->\
                <div class="withdraw-protocol">\
                <p class="protocol">查看<a href="http://www.huanpeng.com/protocol/mobileReciveProtocol.html">《欢朋直播收益兑换规则》</a></p>\
            </div>\
            </div>';
            $('.withdraw-box').html(tpl);
        },
        _initBankStatus : function () {
            document.title = '申请提现';
            var gold = parseFloat(jsonCoin.gold);
            var placeholderVal = '';
            if(gold <= 800){
                placeholderVal = '最多可提取' + digitsFormat(gold) + '金币';
            }else if(gold > 800){
                placeholderVal = '最多可提取 800 金币';
            }

            $('#money-withdraw').attr('placeholder',placeholderVal);

            var requestData = {uid:postUid,encpass:postEncpass};
            var requestUrl = $conf.api + 'user/attested/rpc_ajax.php';
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var bankNum = responseData.bank;
                if(bankNum == ''){
                    //no bind
                    $('.withdraw-bind').data('loaded',0);
                    $('.cardDesc').text('未绑定');
                }else if(bankNum != '' && responseData.bankstatus == '0'){
                    //binded
                    $('.withdraw-bind').data('loaded',1);
                    if(bankNum.length >= 4){
                        bankNum = bankNum.replace(/ /g,'').slice(-4);
                        var bank = responseData.bankname + ' 卡号尾4位:<span style="color: #ff7800;">' + bankNum + '</span>';
                        //var bank = '银行卡号 : ' + bankNum;
                        bankCardNum = bankNum;
                        $('.cardDesc').html(bank);
                        $('.withdraw-bind').find('.arrow').hide();
                    }
                }
                sessionStorage.setItem('userInfo',JSON.stringify(responseData));
            });
        },
        _initBindBank : function () {
            var withdrawBtn = $('.withdraw-bind');
            withdrawBtn[0].ontouchstart = function (e) {
                e.preventDefault();
            };
            if(withdrawBtn.data('loaded') != 1){
                withdrawBtn[0].ontouchend = function () {
                    // mobileLoading(1);
                    if($(this).data('loaded') == 0){
                        setTimeout(function () {
                            location.href = 'myBindCard.php';
                        },300);
                    }else{
                        // mobileLoading(0);
                    }
                }
            }else{
                withdrawBtn[0].ontouchend = function () {
                    mobileTips('如需更换银行卡,请联系客服');
                }

            }
        },
        _initWithDraw : function () {
            $('#money-withdraw').bind('input prooertychange',function () {
                var val = parseInt($('#money-withdraw').val());
                if(!isNaN(val)){
                    $('#needMoney').text(digitsFormat(val));
                    $('.withdraw-show').css('visibility','visible');
                }else{
                    $('.withdraw-show').css('visibility','hidden');
                }
            });
            $('#wdBtn')[0].ontouchstart = function (e) {
                $(this).addClass('tapIt');
                e.preventDefault();
            };
            $('.withDrawBtn')[0].ontouchend = function () {
                $(this).removeClass('tapIt');
                // mobileLoading(1);

                if($('.withdraw-bind').data('loaded') == 1){
                    var withMoney = parseInt($('#money-withdraw').val());

                    if(withMoney == '' || isNaN(withMoney)){
                        mobileTips('请填写提现的金币数额~');
                        return;
                    }
                    if(withMoney < 100){
                        mobileTips('至少提现100金币哦!');
                        $('.withdraw-show').css('visibility','hidden');
                        return;
                    }else if(withMoney > 800){
                        mobileTips('最多提现800金币哦!');
                        $('#money-withdraw').val(800);
                        $('.withdraw-show').css('visibility','hidden');
                        return;
                    }else if(withMoney >= 100 && withMoney <= 800){
                        //提现 =>
                        var requestUrl = $conf.api + 'user/revise/coinToCny.php';
                        var requestData = {
                            uid : postUid,
                            encpass : postEncpass,
                            number : withMoney
                        };
                        ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                            mobileLoading(0);
                            jsonCoin.gold = responseData.coin;
                            var placeholderVal = '最多可提取' + digitsFormat(parseFloat(jsonCoin.gold)) + '金币';
                            $('#money-withdraw').attr('placeholder',placeholderVal);

                            // mobileSuccess('提现成功');
                            if(sessionStorage && sessionStorage.getItem('_refresh')){
                                sessionStorage.removeItem('_refresh');
                                sessionStorage.setItem('_refresh',1);
                            }else{
                                sessionStorage.setItem('_refresh',1);
                            }

                            // withdraw._initProgress();
                            //需确认 是否提交后 实时更新状态

                            location.href = location.href;

                        },function (responData){
                            var code = responData.code;
                            if(_errorCode[code]){
                                mobileTips(_errorCode[code]);
                            }else{
                                mobileTips('提现失败,请联系客服!');
                            }
                        })
                    }else{
                        mobileTips('提现金额不对哦!');
                        $('.withdraw-show').css('visibility','hidden');
                        return;
                    }
                }else{
                    mobileTips('银行卡未绑定');
                    $('.withdraw-show').css('visibility','hidden');
                }
            };
        },

        _withDrawProgress : function () {
            withdraw._initWithDrawProgressDom();
            withdraw._initWithDrawProgressST();
            mobileLoading(0);
        },
        _initWithDrawProgressDom : function () {
            var tpl = '<figure class="progress-show clearfix">\
                <div class="fiLeft">\
                <img src="img/waitTime.png" class="proTop picStyle">\
                </div>\
                <figcaption class="fiRight">\
                <section class="rowLine">\
                <p class="mediumText">提现申请已提交<span class="desc">(本月10～20 日到账)</span>\
                </p>\
                <p class="smallText">金币 <span id="coinNum">0</span>,合计 <span id="getMoney">0 元</span>\
                </p>\
                </section>\
                </figcaption>\
                </figure>\
                <div class="backTo">\
                <button id="backBtn">返回我的收益</button>\
                </div>';
            $('.withdraw-box').html(tpl);
        },
        _initWithDrawProgressST : function () {
            document.title = '提现进度';
            var coin = numberFormat(withDrawData.coin);
            var cny  = numberFormat(withDrawData.cny);
            $('#coinNum').text(coin);
            $('#getMoney').text(cny + ' 元');
            $('#backBtn')[0].ontouchend = function () {
                // mobileLoading(1);
                historyBack();
            }
        },

        _withDrawSuccess : function () {
            withdraw._initWithDrawSuccessDom();
            withdraw._initWithDrawSuccessST();
            mobileLoading(0);
        },
        _initWithDrawSuccessDom : function () {
            var tpl = '<figure class="progress-show clearfix">\
                <div class="fiLeft" style="height: 6.625rem;">\
                <img src="img/waitTime.png" class="proTop picStyle">\
                <span class="proBG"></span>\
                <img src="img/check_right.png" class="proBottom picStyle">\
                </div>\
                <figcaption class="fiRight">\
                <section class="rowLine">\
                <p class="mediumText">提现申请已提交<span class="desc">(本月10～20 日到账)</span>\
                </p>\
                <p class="smallText">兑换金币<span id="coinNum">0</span>个,提现合计<span id="getMoney">0 元</span>\
                </p>\
                </section>\
                <section>\
                <p class="mediumText">提现成功!</p>\
                <p class="smallText">到账时间:<span id="finishTime"></span></p>\
                </section>\
                </figcaption>\
                </figure>\
                <div class="backTo">\
                <button id="backBtn">完成</button>\
                </div>';
            $('.withdraw-box').html(tpl);
        },
        _initWithDrawSuccessST : function () {
            withdraw._initWithDrawProgressST();
            document.title = '提现进度';
            if(withDrawData.finishTime && withDrawData.finishTime != ''){
                var finishTime = withDrawData.finishTime;
                $('#finishTime').text(finishTime);
            }else{
                $('.smallText').html('<span style="color: #ff7800;word-break: break-all;">已到账,请及时查看!</span>');
            }


        },

        _withDrawFail : function () {
            withdraw._initWithDrawFailDom();
            withdraw._initWithDrawFailST();
            mobileLoading(0);
        },
        _initWithDrawFailDom : function () {
            var tpl = '<figure class="progress-show clearfix">\
                <div class="fiLeft" style="height: 6.625rem;">\
                <img src="img/waitTime.png" class="proTop picStyle">\
                <span class="proBG"></span>\
                <img src="img/check_error.png" class="proBottom picStyle">\
                </div>\
                <figcaption class="fiRight">\
                <section class="rowLine">\
                <p class="mediumText">提现申请已提交<span class="desc">(本月10～20 日到账)</span>\
                </p>\
                <p class="smallText">金币<span id="coinNum">0</span>,合计<span id="getMoney">0 元</span>\
                </p>\
                </section>\
                <section>\
                <p class="mediumText">提现失败!</p>\
                <p class="smallText">抱歉哦,提现失败。请联系客服!</p>\
                </section>\
                </figcaption>\
                </figure>\
                <div class="backTo">\
                <button id="backBtn">返回我的收益</button>\
                </div>';
            $('.withdraw-box').html(tpl);
        },
        _initWithDrawFailST : function () {
            document.title = '提现进度';
            withdraw._initWithDrawProgressST();
        },

        _tipsError : function () {
            mobileTips('服务器繁忙,请稍后尝试!');
        }
        
    };
    withdraw.init();
});

function mobileLoading(a) {
    if(a == 1){
        $('.modal-box,.modal-loading').show();
    }else{
        $('.modal-box,.modal-loading').hide();
    }
}
function mobileTips(a) {
    $('.modal-loading').hide();
    $('#error-content').text(a);
    $('.modal-box,.error-modal').show();
    setTimeout(function () {
        $('.modal-box,.error-modal').hide();
        $('#money-withdraw').val('');
    },1500);
}
function mobileSuccess(a) {
    $('.modal-loading,.error-modal').hide();
    $('.modal-success>p').text(a);
    $('.modal-box,.modal-success').show();
    setTimeout(function () {
        $('.modal-box,.modal-success').hide();
    },1500)
}
function historyBack() {
    setTimeout(function () {
        // mobileLoading(1);
    },1500);
    setTimeout(function () {
        if(window.history){
            window.history.back();
        }else{
            history.go(-1);
        }
    },500);
}