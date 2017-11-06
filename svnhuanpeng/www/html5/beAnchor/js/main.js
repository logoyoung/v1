/**
 * Created by junxiao on 2017/6/28.
 */
var phoneNum = sessionStorage.getItem('_phone');
$(function () {
    $('.page-content').append(beAnchorDom_first);
    $('#get-mobile-code').bind('click',getMobileCode());
});

function getMobileCode() {
    var time = 0;
    var agreeRule = false;

    var requestGetMobileCode = function (doCallBack) {
        var requestUrl = $conf.api + 'code/mobileCode.php';
        var requestData = {
            uid:sessionStorage.getItem('_uid'),
            encpass:sessionStorage.getItem('_enc'),
            from:3
        };

        ajaxRequest({url:requestUrl,data:requestData},function (d) {
            typeof doCallBack =='function' && doCallBack();
        },function(d){
            mobileTips(d.desc);
        });
    }
    $('#get-mobile-code').bind('click', function(){
        if(time > 0)
            return;
        var self = this;
        requestGetMobileCode(function () {
            time = 60;
            var interval = setInterval(function () {
                if(time > 1){
                    $(self).text(time + 's');
                }else{
                    clearInterval(interval);
                    $(self).text('重新发送');
                }
                time --;
            },1000)
        });
    });

    var requestCheckMobileCode = function (code, fn,fn2) {
        if(!code)
            return;
        var requestUrl = $conf.api + 'check/checkCode.php';
        var requestData = {
            uid:sessionStorage.getItem('_uid'),
            encpass:sessionStorage.getItem('_enc'),
            mobileCode:code
        };

        ajaxRequest({url:requestUrl,data:requestData},function (d) {
            typeof fn == 'function' && fn(d);
        },function (d) {
            typeof fn2 == 'function' && fn2(d);
        })
    }
    var failedHandle = function (code) {
        mobileTips(code.desc);
    };

    var succHandle = function (code) {

        return apply_anchor();
    };

    var commitBtn = $('.commit .controls').find('#firstBtn');
    $('.check-box').click(function () {
        if(!$(this).hasClass('checked')){
            $(this).addClass('checked');
            commitBtn.removeClass('disabled');
            agreeRule = true;
        }else{
            $(this).removeClass('checked');
            commitBtn.addClass('disabled');
            agreeRule = false;
        }
    });

    commitBtn.bind('click',function () {

        if($(this).hasClass('disabled')) return;
        if(!agreeRule){
            mobileTips('认证主播需要同意主播协议');
            return;
        }
        var code = $('#pass-code').val();
        if(!code){
            mobileTips('验证码错误');
            return;
        }
        requestCheckMobileCode(code,function(d){succHandle(code)},function(d){failedHandle(d)});
    })
}

function apply_anchor() {

    $('.page-body').remove();
    $('.page-head .step:eq(1),.page-head .horizontal-line:eq(0)').addClass('active');
    $('.page-content').addClass('real-name ').append(beAnchorDom_second);
    mobileLoading(0);

    $('.person-code .controls input[type=text]').focus(function(){}).blur(function () {
        var val = $(this).val();
        if(!identityCodeVaild(val)){
            mobileTips('证件号码格式错误');
        }
    });
    //personal etime check
    !function(){
        var group = $('.card-etime .controls .select').get();
        var year = $(group[0]),
            month = $(group[1]),
            day = $(group[2]);

        year.bind({
            /*'input propertychange':function () {
                var real_val = $(this).val();

                if(real_val.length > 4){
                    real_val = value.substr(0, 4);
                    $(this).val(real_val);
                }

                if(real_val.length == 4){
                    if(/^(1[89]|20)\d{2}$/.test(real_val)){
                        month.focus()
                    }else{
                        real_val = real_val.substr(0, 3);
                        $(this).val(real_val);
                    }
                }
                if(real_val.length == 3){
                    if(!/^(1[89]|20)\d{1}$/.test(real_val)){
                        real_val = real_val.substr(0,2);
                        $(this).val(real_val);
                    }
                }
                if(real_val.length == 2){
                    if(!/^(1[89]|20)$/.test(real_val)){
                        real_val = real_val.substr(0,1);
                        $(this).val(real_val);
                    }
                }
                if(real_val.length == 1){
                    if(!/^1|2$/.test(real_val)){
                        real_val = '';
                        $(this).val(real_val);
                    }
                }
            }*/
            'change': function(){
                var real_val = $(this).val();
                var nowYear = new Date.getFullYear();
                if(real_val < nowYear){
                    mobileTips('不能小于当前年份');
                }
            }
        });
        month.bind({
            /*'focus':function () {
                if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val()))
                    year.focus();
            },
            'blur':function () {
                if(/^[1-9]$/.test(month.val()))
                    $(this).val('0' + month.val());
            },
            'input propertychange':function () {
                var real_val = $(this).val();

                if(real_val.length > 2){
                    real_val = real_val.substr(0, 2);
                    $(this).val(real_val);
                }

                if(real_val.length == 2){
                    if(/^0[1-9]|1[0-2]$/.test(real_val)){
                        day.focus()
                    }else{
                        real_val = real_val.substr(0,1);
                        $(this).val(real_val);
                    }
                }
                if(real_val.length == 1){
                    if(!/^[0-9]$/.test(real_val)){
                        real_val = '';
                        $(this).val(real_val);
                    }
                }
            }*/
        });
        day.bind({
            /*'focus':function(){
                if(year.val().length != 4 || !/^(1[89]|20)\d{2}$/.test(year.val())){
                    year.focus();
                    return;
                }
                if(!/^(0[1-9]|1[0-2])/.test(month.val())){
                    month.focus();
                }
            },
            'blur':function () {
                var y = year.val();
                var m = month.val();
                var d = day.val();

                if(/^[1-9]$/.test(d)){
                    d = '0' + d;
                    day.val(d);
                }
                if(!checkDate(y,m,d))
                    mobileTips('日期错误')
            },
            'input propertychange':function () {
                var real_val = $(this).val();
                if(real_val.length > 2){
                    real_val = real_val.substr(0,2);
                    $(this).val(real_val);
                }
                if(real_val.length ==  2){
                    var m = month.val();
                    var reg = '';
                    if(/^0[13578]|1[02]$/.test(m)){
                        reg = /^0[1-9]|[12]\d{1}|3[01]/;
                    }else{
                        if(m == '02'){
                            var leapYear = isLeapYear(parseInt(year.val()));
                            reg = leapYear ? /^0[1-9]|1\d{1}|2[0-9]$/ : /^0[1-9]|1\d{1}|2[0-8]/;
                        }else{
                            reg = /^0[1-9]|[12]\d{1}|30/;
                        }
                    }
                    if(!reg.test(real_val)){
                        real_val = real_val.substr(0,1);
                        $(this).val(real_val);
                    }
                }
                if(real_val.length == 1){
                    if(!/^[0-3]$/.test(real_val)){
                        real_val = '';
                        $(this).val('');
                    }
                }
            }*/
        });
    }();

    $('.upload input[type=file]').change(function () {
        upload_pic($(this).attr('id'));
    })
    function upload_pic(type) {
        var form = $('#upload_' + type);
        if(!form) return;
        var fileUpLoadRes = checkUploadImage($('#'+type)[0], $conf.uploadImgSize);
        if(fileUpLoadRes < 0){
            mobileTips('文件不符合标准');
            return;
        }

        form.ajaxSubmit({
            url:$conf.api + 'user/attested/upload_identPic_ajax.php',
            type:'post',
            dataType:'json',
            data:{
                uid:sessionStorage.getItem('_uid'),
                encpass:sessionStorage.getItem("_enc"),
                type:type
            },
            success:function (d) {

                if(d.status == '1'){
                    var data = d.content;
                    form.parent().find('.pic img').attr('src',data.img);
                }else{
                    console.log(d.content.desc);
                    mobileTips('图片上传失败');
                }
            },
            error:function (e) {
                console.log(e);
                mobileTips('文件不符合标准');
            }
        });
    }

    //submit
    $('.real-name .commit .btn').bind('click',function () {
        var rname = $('input[name=user-name]').val();
        var cardcode = $('input[name=person-code]').val();
        var year = $("#year").val();
        var month = $('#month').val();
        var day = $('#day').val();
        var front = $("#front").val();
        var back = $('#back').val();
        var handheld = $('#handheld').val();

        if(!rname || !checkName(rname)){
            mobileTips('姓名不符合格式');
            return;
        }
        if(!cardcode || !identityCodeVaild(cardcode)){
            mobileTips('身份证号码错误');
            return;
        }
        /*if(!checkNowTime(year, month, day)){
            mobileTips('证件到期时间错误');
            return;
        }*/
        if(!front || !back || !handheld){
            mobileTips('请上传身份证照片');
            return;
        }
        var date = year + '-' + month + '-' + day;
        var url = $conf.api+'user/attested/certIdent_ajax.php';
        var data = {
            uid:sessionStorage.getItem('_uid'),
            encpass:sessionStorage.getItem("_enc"),
            name:rname,
            identID:cardcode,
            outTime:date
        };

        ajaxRequest({url:url,data:data},function () {
            return apply_success();
        },function (d) {
            if(d.type == 1){
                mobileTips('认证失败');
            }else {
                mobileTips(d.desc);
            }
        });
    });

    function checkName(name){
        if(!name || name.length >= 10)
            return false;
        return true;
    }
    function checkNowTime(y, m, d){
        console.log(y);
        console.log(m);
        console.log(d);
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth()+1;
        var day = date.getDate();
        if (y < year){
            return false;
        }else if(m < month){
            return false;
        }else if(d <= day){
            return false;
        }else{
            return true;
        }
    }
}

function apply_success(){
    $('.page-body, .control-group, .real-name-notice-word').remove();
    $('.page-head .step:eq(2),.page-head .horizontal-line:eq(1)').addClass('active');
    $('.page-content').append(beAnchorDom_third);
}

function mobileTips(a){
    $('#error-content').text(a);
    $('.modal-box, .error-modal').show();
    setTimeout(function () {
        $('.modal-box, .error-modal').hide();
    },1000)
}

function mobileLoading(a) {
    if(a == 1){
        $('.modal-box, .modal-loading').show();
    }else{
        $('.modal-box, .modal-loading').hide();
    }
}

var beAnchorDom_first   = '<div class="page-body check-phone">\
                                    <div class="form-horizontal" >\
                                    <div class="white-block" >\
                                    <div class="control-group phone-number label-hide">\
                                    <div class="controls">\
                                    <span class="icon"></span>\
                                    <span class="number">'+phoneNum+'</span>\
                                    <a href="javascript:;" class="btn" id="get-mobile-code">获取验证码</a>\
                                    </div>\
                                    <div class="clear"></div>\
                                    </div>\
                                    <div class="control-group pass-code label-hide">\
                                    <div class="controls">\
                                    <span class="icon"></span>\
                                    <input id="pass-code" type="number" placeholder="请输入验证码">\
                                    </div>\
                                    <div class="clear"></div>\
                                    </div>\
                                    </div>\
                                    <div class="control-group agree-rule label-hide">\
                                    <div class="controls">\
                                    <div class="check-box-block">\
                                    <label class="check-box"></label>\
                                    <span>我已阅读并同意<a href="http://www.huanpeng.com/protocol/anchorLiveProtocol.html" class="protocol">《欢朋直播主播协议》</a></span>\
                                    <div class="clear"></div>\
                                    </div>\
                                    </div>\
                                    <div class="clear"></div>\
                                    </div>\
                                    </div>\
                                    <div class="control-group commit">\
                                    <div class="controls"> <a id="firstBtn" href="javascript:;" class="btn disabled">下一步</a>\
                                    </div>\
                                    </div>\
                                    </div>';

var beAnchorDom_second  = '<div class="page-body cert-to-anchor">\
                                <div class="form-horizontal">\
                                <div class="white-block">\
                                <div class="control-group user-name">\
                                <div class="control-label">真实姓名</div>\
                                <div class="controls">\
                                <input name="user-name" type="text" placeholder="请填写真实姓名">\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                <div class="control-group person-code">\
                                <div class="control-label">身份证号</div>\
                                <div class="controls">\
                                <input name="person-code" type="text" placeholder="请填写身份证号">\
                                </div>\
                                <div class="clear">\
                                </div>\
                                </div>\
                                <div class="control-group card-etime">\
                                <div class="control-label">身份证到期时间</div>\
                                <div class="controls">\
                                <select name="year" id="year" class="select">\
                                <option value="2017">2017</option>\
                                <option value="2018">2018</option>\
                                <option value="2019">2019</option>\
                                <option value="2020">2020</option>\
                                <option value="2021">2021</option>\
                                <option value="2022">2022</option>\
                                <option value="2023">2023</option>\
                                <option value="2024">2024</option>\
                                <option value="2025">2025</option>\
                                <option value="2026">2026</option>\
                                <option value="2027">2027</option>\
                                <option value="2028">2028</option>\
                                <option value="2029">2029</option>\
                                <option value="2030">2030</option>\
                                <option value="2031">2031</option>\
                                <option value="2032">2032</option>\
                                <option value="2033">2033</option>\
                                <option value="2034">2034</option>\
                                <option value="2035">2035</option>\
                                <option value="2036">2036</option>\
                                <option value="2037">2037</option>\
                                </select>\
                                <select name="month" id="month" class="select">\
                                <option value="01">01</option>\
                                <option value="02">02</option>\
                                <option value="03">03</option>\
                                <option value="04">04</option>\
                                <option value="05">05</option>\
                                <option value="06">06</option>\
                                <option value="07">07</option>\
                                <option value="08">08</option>\
                                <option value="09">09</option>\
                                <option value="10">10</option>\
                                <option value="11">11</option>\
                                <option value="12">12</option>\
                                </select>\
                                <select name="day" id="day" class="select">\
                                <option value="01">01</option>\
                                <option value="02">02</option>\
                                <option value="03">03</option>\
                                <option value="04">04</option>\
                                <option value="05">05</option>\
                                <option value="06">06</option>\
                                <option value="07">07</option>\
                                <option value="08">08</option>\
                                <option value="09">09</option>\
                                <option value="10">10</option>\
                                <option value="11">11</option>\
                                <option value="12">12</option>\
                                <option value="13">13</option>\
                                <option value="14">14</option>\
                                <option value="15">15</option>\
                                <option value="16">16</option>\
                                <option value="17">17</option>\
                                <option value="18">18</option>\
                                <option value="19">19</option>\
                                <option value="20">20</option>\
                                <option value="21">21</option>\
                                <option value="22">22</option>\
                                <option value="23">23</option>\
                                <option value="24">24</option>\
                                <option value="25">25</option>\
                                <option value="26">26</option>\
                                <option value="27">27</option>\
                                <option value="28">28</option>\
                                <option value="29">29</option>\
                                <option value="30">30</option>\
                                <option value="31">31</option>\
                                </select>\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                <div class="control-group  h5-person-card">\
                                <div class="person-card card-pic-handheld">\
                                <div class="controls">\
                                <form action="" id="upload_handheld" name="upload_handheld" method="post">\
                                <span class="pic">\
                                <img src="image/identCard_handheld.png">\
                                <div class="upload">\
                                <input type="file" name="file" id="handheld" accept="image/*" style="">\
                                </div>\
                                </span>\
                                <div class="control-label">手持身份证</div>\
                                </form>\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                <div class="person-card card-pic-front">\
                                <div class="controls">\
                                <form action="" id="upload_front" name="upload_front" method="post"> <span class="pic">\
                                <img src="image/identCard_front.png">\
                                <div class="upload">\
                                <input type="file" name="file" id="front" accept="image/*" style="">\
                                </div>\
                                </span>\
                                <div class="control-label">身份证正面</div>\
                                </form>\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                <div class="person-card card-pic-back">\
                                <div class="controls">\
                                <form action="" id="upload_back" name="upload_back" method="post"> <span class="pic">\
                                <img src="image/identCard_back.png">\
                                <div class="upload">\
                                <input type="file" name="file" id="back" accept="image/*" style="">\
                                </div>\
                                </span>\
                                <div class="control-label">身份证反面</div>\
                                </form>\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                <div class="clear"></div>\
                                </div>\
                                </div>\
                                </div>\
                                </div>\
                                <div class="control-group commit">\
                                <div class="controls"> <a href="javascript:;" class="btn">下一步</a>\
                                </div>\
                                </div>\
                                <div class="real-name-notice-word">\
                                <div class="controls">\
                                <p>身份证照将会进行人工审核，务必做到以下几点：</p>\
                                <p>【 1 】您需要年满18周岁；</p>\
                                <p>【 2 】双手手持纸张与身份证同时拍照（请摄像头聚焦于身份证上，保证身份证信息清晰）；</p>\
                                <p>【 3 】纸上写明“申请欢朋主播”；</p>\
                                <p>【 4 】清晰：五官头像以及身份证照片文字清晰可见；</p>\
                                <p>【 5 】真实：本人与身份证照片无PS；</p>\
                                <p>【 6 】大小：照片大小需在2M以内。</p>\
                                <p>如申请信息不合格，实名认证将会被驳回。</p>\
                                </div>\
                                </div>';

var beAnchorDom_third   = '<div class="page-body">\
                                        <div class="block-insert" style="margin: 0;">\
                                        <div class="logo"><img src="image/check-wait-logo.png"></div>\
                                        <p class="notice">主播认证已经提交，请耐心等待</p>\
                                    </div>\
                                    </div>';