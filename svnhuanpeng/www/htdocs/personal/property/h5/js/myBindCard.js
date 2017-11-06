/**
 * Created by junxiao on 2017/5/4.
 */

var postUid = '';
var postEncpass = '';
var postPhone = '';
var identname = '';

$(function () {
    if(sessionStorage){
        var d = JSON.parse(sessionStorage.getItem('coinAll'));
        var u = JSON.parse(sessionStorage.getItem('userInfo'));
        postUid = d.uid;
        postEncpass = d.encpass;
        postPhone = u.phone;
        identname = u.identname;
    }else{
        mobileTips('账号异常,请稍后重试!');
    }
    var bindCard = {
        init : function () {
            bindCard._initCity();
            bindCard._initBind();
            bindCard._initRealName();
            bindCard._initOther();
        },
        _initCity : function () {
            var city_picker = new mui.PopPicker({layer:3});
            city_picker.setData(init_city_picker);
            $("#selectCity").on("tap", function(){
                setTimeout(function(){
                    city_picker.show(function(items){
                        var a = (items[0] || {}).text + " " + (items[1] || {}).text + " " + (items[2] || {}).text;
                        a = a.replace(/undefined/g,'');
                        $("#selectCity").val(a);
                    });
                },200);
            });
        },
        _initBind : function () {
            $('.otherS').change(function () {
                $(this).val() != '请选择银行' ? $(this).css('color','#282828') : $(this).css('color','#929292');
            });
            //银行卡号 分割
            $('#firstCN,#checkNum').on('keyup',function () {
                this.value = this.value.replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
            });
            //检验进度
            $('#bindName,#bankSelect,#selectCity,#firstCN,#checkNum').blur(function () {
                toolCheck();
            });
            $('#firstCN,#checkNum').focus(function () {
                $('.bindCard-container').addClass('toTop');
            });
            $('#bindName,#bankSelect').focus(function () {
                $('.bindCard-container').removeClass('toTop');
            });
        },
        _initRealName : function () {
            if (identname){
                $('#bindName').val(identname).attr('disabled','disabled').blur();
            }else{
                $('#bindName').val('');
            }
        },
        _initDisplay : function () {
            var bindName     = $('#bindName').val();
            var bindBank     = $('#bankSelect').val();
            var bankID       = bankMAP[bindBank] ? bankMAP[bindBank] : null;
            var bindSeleCt   = $('#selectCity').val();
            var cardID       = $('#firstCN').val();
            var againCardID  = $('#checkNum').val();

            if(bindName == ''){
                mobileTips('请输入姓名!');
                $('#bindName').val('');
                return false;
            }else if(bindBank == '请选择银行'){
                mobileTips('请选择银行!');
                $('#bankSelect').val('');
                return false;
            }else if(bankID == null){
                mobileTips('异常错误!');
                return false;
            }else if(bindSeleCt == ''){
                mobileTips('请选择开户地!');
                $('#selectCity').val('');
                return false;
            }else if(cardID.length < 12 || againCardID.length < 12){
                // 10   +2(reason:银行卡号 隔两位 空格)
                mobileTips('银行卡号最低10位!');
                return false;
            }else if(cardID == '' || againCardID == '') {
                mobileTips('请填写银行卡号!');
                return false;
            }else if(typeNumber(cardID) || typeNumber(againCardID)){
                mobileTips('银行卡号错误!');
                $('#firstCN,#checkNum').val('');
                return false;
            }else if(cardID != againCardID){
                mobileTips('两次银行卡号输入不一致!');
                //$('#firstCN,#checkNum').val('');
                return false;
            }else if(cardID == againCardID){
                var nowPerson = {
                    uid          :   postUid,
                    encpass      :   postEncpass,
                    mobile       :   postPhone,
                    name         :   bindName,
                    bank         :   bindBank,
                    bankID       :   bankID,
                    bankLBS      :   bindSeleCt.replace(/-/g,''),
                    cardID       :   cardID,
                    againCardID  :   againCardID
                };

                $('.step-two').removeClass('noAll');
                $('#template-step1').hide();
                $('#template-step2').show();

                $('#bankTitle').text(nowPerson.bank);
                $('#bankLBS').text(nowPerson.bankLBS);
                $('#cardNumber').text(nowPerson.cardID);
                $('#bankName').text(nowPerson.name);

                $('#subTohp')[0].ontouchstart= function (e) {
                    $(this).addClass('tapIt');
                    e.preventDefault();
                };
                $('#subTohp')[0].ontouchend= function () {
                    $(this).removeClass('tapIt');
                    // mobileLoading('show');

                    var requestUrl = $conf.api + 'anchor/bindBankCard.php';
                    var requestData = {
                        uid         :   nowPerson.uid,
                        encpass     :   nowPerson.encpass,
                        name        :   nowPerson.name,
                        bankID      :   nowPerson.bankID, //银行ID
                        mobile      :   nowPerson.mobile,
                        address     :   nowPerson.bankLBS,
                        cardID      :   nowPerson.cardID.replace(/\+/g,''),
                        againCardID :   nowPerson.againCardID.replace(/\+/g,'')
                    };

                    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                        mobileSuccess('绑定成功');
                        if(sessionStorage && sessionStorage.getItem('_bindRefresh')){
                            sessionStorage.removeItem('_bindRefresh');
                            sessionStorage.setItem('_bindRefresh',1);
                        }else{
                            sessionStorage.setItem('_bindRefresh',1);
                        }
                        setTimeout(function () {
                            window.history.back();
                        },1000);
                    },function (responseData) {
                        mobileTips('绑定失败,请联系客服');
                    })

                };
            }
        },
        _initOther : function () {
            $('#nextBtn')[0].ontouchstart= function (e) {
                $(this).addClass('tapIt');
                e.preventDefault();
                mobileLoading('show');
            };
            $('#nextBtn')[0].ontouchend= function () {
                $(this).removeClass('tapIt');
                $('input').blur();
                mobileLoading('hide');
                bindCard._initDisplay();
            };

            $('.step-one')[0].ontouchend = function () {
                $('.step-two').addClass('noAll');
                $('#template-step2').hide();
                $('#template-step1').show();
            };
        }
    };
    bindCard.init();
});
var bankMAP = {
        '招商银行'   : '2',
        '中国工商银行':'5',
        '中国农业银行':'10',
        '中国建设银行':'15',
        '交通银行':'20',
        '上海浦东发展银行':'25',
        '民生银行':'30',
        '光大银行':'35',
        '兴业银行':'40',
        '广东发展银行':'45',
        '平安银行':'50',
        '北京银行':'55',
        '中国银行':'60',
        '中信银行':'65',
        '华夏银行':'70',
        '中国邮政储蓄银行':'75',
        '城市商业银行':'80',
        '农村商业银行':'85',
        '农村合作银行':'90',
        '农村信用合作社':'95',
        '恒丰银行':'100',
        '渤海银行':'105',
        '南京银行':'110',
        '江苏银行':'115',
        '宁波银行':'120',
        '上海银行':'125',
        '杭州银行':'130',
        '中国农业发展银行':'135',
        '花旗银行':'140',
        '渣打银行':'145'
};
function toolCheck() {
    $('.bindCard-container').removeClass('toTop');
    var progress = 0;
    var newHe = $('#bindName,#bankSelect,#selectCity,#firstCN,#checkNum');
    for(var i = 0; i < newHe.length; i++){
        if(newHe[i].value != '' && newHe[i].value.indexOf('请') < 0){
            progress += 1;
        }
    }
    var scaleOfAll = progress * 20 + '%';
    $('.progressStep').css('width',scaleOfAll);
}
function mobileTips(a) {
    $('.modalBox,.modalBox .fail').show();
    $('.fail>p').text(a);
    setTimeout(function () {
        $('.modalBox,.modalBox .fail').hide();
    },1500);
}
function mobileLoading(a) {
    if(a == 'show'){
        $('.modalBox,.modalBox .loading').show();
    }else if(a == 'hide'){
        $('.modalBox,.modalBox .loading').hide();
    }
}
function mobileSuccess(a) {
    $('.modalBox,.modalBox .success').show();
    $('.success>p').text(a);
    setTimeout(function () {
        $('.modalBox,.modalBox .success').hide();
    },1500);
}
function typeNumber(a){
    if(typeof a == 'number' || a.length < 6){
        return false;
    }
}