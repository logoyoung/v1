/**
 * Created by dell on 2016/12/19.
 */

var postUid = null;
var postEncpass = null;

$(function () {

    if(isIphoneClient()){
        getParams(appLoginUid,appEncpass);
    }
    //轮询更改状态
    var timer1 = setInterval(function () {
        if (sessionStorage && sessionStorage.getItem('_refresh')){
            sessionStorage.removeItem('_refresh');
            location.reload();
        }
    },50);
    var timer2 = setInterval(function () {
        if(sessionStorage && sessionStorage.getItem('_toCoinRefresh')){
            sessionStorage.removeItem('_toCoinRefresh');
            location.reload();
        }
    },50);
    var recive = {
        init : function () {
            recive._initBind();
        },
        _initBind : function () {
            $('#toHPCoin')[0].ontouchstart = function(e){
                this.className = 'exchangeCurrent';
                e.preventDefault();
                // mobileLoading();
            };
            $('#toHPCoin')[0].ontouchend = function (){
                this.className = 'exchange';
                setSessionToHref('myExchange.php');
            };
            $('#toCoin')[0].ontouchstart = function(e){
                this.className = 'exchangeCurrent';
                e.preventDefault();
                // mobileLoading();
            };
            $('#toCoin')[0].ontouchend = function (){
                this.className = 'exchange';
                setSessionToBean('myToCoin.php');
            };


            $('#withdraw')[0].ontouchstart = function (e){
                this.className = 'withdrawCurrent';
                e.preventDefault();
                // mobileLoading();
            };
            $('#withdraw')[0].ontouchend = function (){
                this.className = 'withdraw';

                setSessionToHref('myWithdraw.php');

            };
        }
    };
    recive.init();
});

function getParams(uid, encpass){

    postUid = uid;
    postEncpass = encpass;

    if(sessionStorage && sessionStorage.getItem('coinAll')){
        sessionStorage.removeItem('coinAll');
    }
    var options = {
        useEasing : true,
        useGrouping : true,
        separator : ',',
        decimal : '.',
        prefix : '',
        suffix : ''
    };

    var requestUrl = $conf.api + 'property/api_myProperty.php';
    var requestData = {uid:postUid,encpass:postEncpass,type:0};

    ajaxRequest({url:requestUrl, data:requestData}, function(responseData){
        //收益余额
        var allCoin = parseFloat(responseData.coin);
        var allBean = parseFloat(responseData.bean);
        //月收益
        var monthCoin = parseFloat(responseData.monthCoin);
        var monthBean = parseFloat(responseData.monthBean);
        //本月金币奖励
        //var monthReward = parseFloat(responseData.reward);
        //奖励比例
        var myRatio = responseData.ratio;

        //Count-animation
        var goldAll  = new CountUp("goldAll", 0, allCoin, 2, 1.5, options);
        var beanAll  = new CountUp("beanAll", 0, allBean, 2, 1.5, options);

        var goldMonth = new CountUp("moneyCount", 0, monthCoin, 2, 1.5, options);
        var beanMonth = new CountUp("beanCount", 0, monthBean, 2, 1.5, options);

        goldAll.start();
        beanAll.start();

        goldMonth.start();
        beanMonth.start();

        $('.awardGold').text(myRatio);

    },function(responseData){
        mobileTips('账号异常,请稍后重试!');
    });


}

function mobileLoading(){
    $('.error-modal').hide();
    $('.modal-box,.modal-loading').show();
}
function mobileLoadCLOSE() {
    $('.modal-box,.modal-loading').hide();
}
function mobileTips(a){
    $('.modal-loading').hide();
    $('#error-content').text(a);
    $('.modal-box,.error-modal').show();
    setTimeout(function(){
        $('.modal-box').hide();
    },1000);
}
function setSessionToHref(a){
    if(postUid == null || postEncpass == null){
        window.phonePlus.turnTo('unlogin');
        return;
    }

    var requestUrl = $conf.api + 'property/api_myProperty.php';
    var requestData = {uid:postUid,encpass:postEncpass,type:1};
    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {

        var coin = parseFloat(responseData.coin).toFixed(1);
        var hpcoin = parseFloat(responseData.hpcoin).toFixed(1);

        if(a != 'myWithdraw.php'){
            if(coin > 0){
                if(sessionStorage){
                        var coinAll = {
                            uid:postUid,
                            encpass:postEncpass,
                            gold:coin,
                            hpGold:hpcoin
                        };
                        sessionStorage.setItem('coinAll',JSON.stringify(coinAll));
                        setTimeout(function(){
                            mobileLoadCLOSE();
                            location.href = a;
                        },300);}
            }else{
                mobileTips('您的余额不足');
                return;
            }
        }else{
            if(sessionStorage){
                    var coinAll = {
                        uid:postUid,
                        encpass:postEncpass,
                        gold:coin,
                        hpGold:hpcoin
                    };
                    sessionStorage.setItem('coinAll',JSON.stringify(coinAll));
                    setTimeout(function(){
                        mobileLoadCLOSE();
                        location.href = a;
                    },300);
                }
            // if(coin > 0){
            //     if(sessionStorage){
            //         var coinAll = {
            //             uid:postUid,
            //             encpass:postEncpass,
            //             gold:coin,
            //             hpGold:hpcoin
            //         };
            //         sessionStorage.setItem('coinAll',JSON.stringify(coinAll));
            //         setTimeout(function(){
            //             mobileLoadCLOSE();
            //             location.href = a;
            //         },300);
            //     }
            // }else{
            //     mobileTips('您的金币不足');
            //     return false;
            // }
        }

    },function(responseData){
        mobileTips('账号异常，请稍后重试！');
    });
}
function setSessionToBean(a) {
    if(postUid == null || postEncpass == null){
        window.phonePlus.turnTo('unlogin');
        return;
    }
    var requestUrl = $conf.api + 'property/api_myProperty.php';
    var requestData = {uid:postUid,encpass:postEncpass,type:0};
    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {

        var bean = parseFloat(responseData.bean).toFixed(2);
        var coin = parseFloat(responseData.coin).toFixed(2);
        if(bean <= 0){
            mobileTips('您的金豆不足');
        }else if(bean > 0){
            if(sessionStorage){
                var beanAll = {
                    uid:postUid,
                    encpass:postEncpass,
                    gold:coin,
                    bean:bean
                };
                sessionStorage.setItem('beanAll',JSON.stringify(beanAll));
                setTimeout(function(){
                    mobileLoadCLOSE();
                    location.href = a;
                },300);
            }
        }
    },function(responseData){
        mobileTips('账号异常，请稍后重试！');
    });
}

