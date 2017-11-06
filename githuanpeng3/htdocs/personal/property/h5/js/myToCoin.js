/**
 * Created by junxiao on 2017/5/8.
 */

var postUid = '';
var postEncpass = '';
$(function () {
    if(sessionStorage && sessionStorage.getItem('beanAll')){
            var stringCoin = sessionStorage.getItem('beanAll');
            var jsonCoin = JSON.parse(stringCoin);
            postUid = jsonCoin.uid;
            postEncpass = jsonCoin.encpass;
            $('.goldMoney').text(numberFormat(jsonCoin.gold));
            $('.hpMoney').text(numberFormat(jsonCoin.bean));
    }
    var toCoin = {
        init : function () {
            toCoin._initSelect();
            toCoin._initExchange();
        },
        _initSelect : function () {
            $('.myExchange-select li').on('click',function () {

                var hasClass = $(this)[0].className;

                $('.myExchange-select li').removeClass('selectThis');

                if(hasClass != 'undefined'){

                    hasClass == '' ? $(this).addClass('selectThis') : $(this).removeClass('selectThis')

                }

            });
        },
        _initExchange : function () {
            $('.exchangeBtn')[0].onclick = function () {
                if(postUid == '' || postEncpass == ''){
                    window.phonePlus.turnTo('unlogin');
                    return;
                }
                if($('.selectThis').length != 0){
                    $('.alertError').hide();
                    $('.exchangeBtn')[0].disabled = true;
                    mobileLoading(1);

                    var oldSelect = $('.selectThis').find('.gold').text();
                    var needSelect = oldSelect.replace(/金币/g,'').replace(/,/g,'');
                    var requestUrl = $conf.api + 'user/revise/beanToCoin.php';
                    var requestData = {
                        uid:postUid,
                        encpass:postEncpass,
                        number:parseInt(needSelect)
                    };
                    var oldBEAN = $('.hpMoney').text();
                    if(requestData.number > oldBEAN){
                        mobileTips('金豆余额不足!');
                        return;
                    }
                    if(requestData.number <= oldBEAN){
                        ajaxRequest({url:requestUrl, data:requestData}, function(responseData){
                            var newCoin = responseData.coin;
                            var newBean = responseData.bean;

                            mobileLoading(0);
                            mobileSuccess('兑换成功!');
                            $('.exchangeBtn')[0].disabled = false;
                            $('.goldMoney').text(numberFormat(newCoin));
                            $('.hpMoney').text(numberFormat(newBean));

                            if(sessionStorage && sessionStorage.getItem('_toCoinRefresh')){
                                sessionStorage.removeItem('_toCoinRefresh');
                                sessionStorage.setItem('_toCoinRefresh',0);
                            }else{
                                sessionStorage.setItem('_toCoinRefresh',0);
                            }

                        },function(responseData){
                            if(responseData.code == '-4097'){
                                mobileTips('经纪公司签约主播暂不支持兑换哦');
                            }else if(responseData.code == '-4107' || responseData.code == '-4108'){
                                mobileTips('兑换服务 6月1日恢复!');
                            }else{
                                mobileTips('服务器繁忙,请稍后重试~');
                            };
                            setTimeout("location.href='myRecive.php'",2000);
//                          location.href="myRecive.php";
                        })
                    }

                }else{
                    $('.alertError').css('display','inline-block').text('请选择一项兑换数额');
                    mobileTips('请选择一项兑换数额');
                }
            };
        }
    };
    toCoin.init();
});
function mobileSuccess(a){
    $('.modal-fail,.modal-loading').hide();
    $('.modal-success>p').text(a);
    $('.myExchange-modalBox,.modal-success').show();
    setTimeout(function () {
        $('.myExchange-modalBox,.modal-success').hide();
    },1500);
}
function mobileTips(a) {
    $('.modal-success,.modal-loading').hide();
    $('.modal-fail>p').text(a);
    $('.myExchange-modalBox,.modal-fail').show();
    setTimeout(function () {
        $('.myExchange-modalBox,.modal-fail').hide();
    },1500);
}
function mobileLoading(a){
    $('.modal-success,.modal-fail').hide();
    if(a == 1){
        $('.myExchange-modalBox,.modal-loading').show();
    }else{
        $('.myExchange-modalBox,.modal-loading').hide();
    }
}