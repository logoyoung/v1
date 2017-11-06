/**
 * Created by junxiao on 2017/5/10.
 */
var modifyNickFree;
function setUserHead(stat, url){
    var picDom = $("#personal .basic .pdetail .personalPic");
    var checkUrl = arguments[2] ? arguments[2] : url;
    stat = parseInt(stat);
    switch (stat){
        case 0:
            picDom.find('.check-pic-block').remove();
            picDom.find('img').attr('src', checkUrl).after('<div class="check-pic-block"><div class="check-icon"></div><div class="text">新头像审核中</div></div>')
            break;
        case 1:
            picDom.find('img').attr('src', url);
            picDom.find('.check-pic-block').remove();
            break;
        case 2:
            picDom.find('.check-pic-block').remove();
            picDom.find('img').attr('src', checkUrl).after('<div class="check-pic-block"><div class="check-icon"></div><div class="text">新头像未通过审核</div></div>')
            break;
    }
}

(function(){
    personalCenter_sidebar('personal');
    var $conf = conf.getConf();
    var personalInfo = $("#personal .basic .pdetail");
    if(!personalInfo.get()[0])
        return;

    var requestUrl = $conf.api + 'user/info/accessPersonalInfo.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc')
    };
    ajaxRequest({url:requestUrl,data:requestData},function(d){
        setUserHead(d.picCheckStat, d.head, d.picCheckUrl);
        // setUserHead(d.picCheckStat, d.pic, d.picCheckUrl);
        personalInfo.find('.personalInfo .nick').text(d.nick);
        personalInfo.find('.levellable .level').addClass('level'+ d.level);

        personalInfo.find('#userID').text(d.uid);

        var between = parseInt(d.levelIntegral - d.integral);
        var levelbar_width = (d.integral / d.levelIntegral) * 100 + '%';
        personalInfo.find('.personalInfo .bar .levelbar').width(levelbar_width);
        personalInfo.find('.personalInfo .bar p').html('距离升级还有 <i style="font-style: normal;color: #FF7800;">' + digitsFormat(d.gapIntegral) + "</i> 经验值");
        personalInfo.find('.payment_block .paytype:eq(0) .num').text(digitsFormat(d.hpcoin));
        personalInfo.find('.payment_block .paytype:eq(1) .num').text(digitsFormat(d.hpbean));

        setCookie('_unick', d.nick);

        address(d.addr,d.pid,d.cid);

        modifyNickFree = d.modifyNickFree;
    });

    function address(addr, pid, cid){

        var selfAddr = addr;
        var selfPid = Number(pid);
        var selfCid = Number(cid);
        normalStatus(selfAddr);

        function normalStatus(addr){

            if(addr){
                var html = '<span class="identifyDetail mt-12 mr-20 left">' + addr + '</span><span class="option mt-12 left">修改</span><div class="clear"></div>';
            }else{
                var html = '<span class="identifyDetail mt-12 mr-20 left">填写您的地址</span><span class="option mt-12 left">添加</span><div class="clear"></div>';
            }

            $("#anchorAddr .controls").html(html).find('.option').click(function(){
                editStatus();
            });

        }

        function editStatus(){
            var html = '<div class="select-container">\
                                <div class="selectCity">\
                                    <input type="text" placeholder="请选择省/市" id="proCity" readonly="readonly">\
                                    <span class="selectTitle">\
                                        <div class="title block-title">\
                                            <div class="title-prov curr">省</div>\
                                            <div class="title-city">市</div>\
                                        </div>\
                                        <div class="contentBox">\
                                            <div class="content">\
                                                <div class="contentLeft" id="contentLeft"></div>\
                                                <div class="contentRight" id="contentRight"></div>\
                                            </div>\
                                        </div>\
                                    </span>\
                                </div>\
                                <div class="resLoc">\
                                    <input type="text" placeholder="请输入您的具体地址" id="conLoc">\
                                </div>\
                                <div class="handle">\
                                    <button class="saveBtn">保存</button>\
                                    <button class="cancelBtn">取消</button>\
                                </div>\
                            </div>';
            $("#anchorAddr .controls").html(html);

            var requestUrl = $conf.api + 'other/addressMap.php';
            var requestData = {
                type:1,
                uid:getCookie('_uid'),
                encpass:getCookie("_enc")
            };
            ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                var resList = responseData.list;
                var proList = [];
                for(var i in resList){
                    proList.push(createPro(resList[i]));
                }
                $("#contentLeft").html(proList);
                bindEvent(resList);


                $('#proCity').focus(function () {
                    $('.selectTitle').css('display','block');
                });

                $('.resLoc,#conLoc').focus(function () {
                    $('.selectTitle').css('display','none');
                });

                $(".select-container .handle .saveBtn").click(function () {
                    var detail = $('.resLoc input').val();
                    var requestUrl = $conf.api + 'user/revise/alterUserAddr.php';
                    var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),pid:selfPid,cid:selfCid,detail:detail};
                    ajaxRequest({url:requestUrl,data:requestData},function(){
                        normalStatus(getAddress(selfPid, selfCid, detail));
                    },function (responseData) {
                        if(responseData.code == '-4081'){
                            tips('详细地址至少输入10位哦!');
                        }else{
                            tips(responseData.desc);
                        }

                    });
                });
                $(".select-container .handle .cancelBtn").click(function () {
                    normalStatus(selfAddr);
                });

                function getAddress(pid,cid,detail){
                    var pro = resList[pid-1].name;
                    var citylist = resList[pid-1].list;
                    var city = '';
                    for(var i in citylist){
                        if(citylist[i].id == cid){
                            city = citylist[i].name;
                        }
                    }

                    return pro + city + detail;
                }
            });

        }

        function createPro(obj) {

            if(obj.pid && obj.name && obj.list){

                var onePro = '<a data-pid="'+obj.pid+'">'+obj.name+'</a>';
                return onePro;
            }

        }
        function bindEvent(resList) {
            $('#contentLeft>a').click(function (e) {
                e.preventDefault();

                var indexT = $(this).attr('data-pid');
                selfPid = indexT;
                if(resList[indexT-1].list){
                    var cityJson = resList[indexT-1].list;
                }
                $('#proCity').val($(this).html());

                var cityList = [];
                for(var j in cityJson){
                    cityList.push(CreateCity(cityJson[j]));
                }

                $('#contentRight').html(cityList);

                //animation
                $('.title-prov').removeClass('curr');
                $('.title-city').addClass('curr');
                $('.content').css('left','-460px');

                $('#contentRight>a').click(function (e) {
                    e.preventDefault();
                    selfCid = $(this).attr('data-id');
                    var oldLoc = $('#proCity').val();
                    $('#proCity').val(oldLoc+'/'+$(this).html());
                    $('.content').css('left','0');
                    $('.title-prov').addClass('curr');
                    $('.title-city').removeClass('curr');

                });

            });

        }

        function CreateCity(obj) {
            if(obj.id && obj.name){
                var oneCity = '<a data-id="'+obj.id+'">'+obj.name+'</a>';
                return oneCity;
            }
        }
    }
}());

$('.pblock .title span.personal_info').addClass('cur');
(function (a) {
    var c = conf.getConf();
    var $conf = c;
    var index = {
        init: function (d) {
            this.userName();
            this.sex();
            this.certifyEmail(Number(d.emailstatus), d.email);
            this.certifyPhone(Number(d.phonestatus), d.phone);
            this.threePartyBind(d);
        },
        userName: function () {
            var nickEle = $('#p_unick');
            var nickCtr = nickEle.find('.controls');

            normalStatus();

            function editStatus() {
                var uid = getCookie('_uid');
                var enc = getCookie('_enc');
                var nickBlock = $('#p_unick');
                var personalBlock = $('.personalInfo');
                var coinBlock = $('.payment_block .paytype:eq(0)');

                var diaLogs = dialog({
                    title: '修改昵称',
                    skin: 'err-notice person-notice modifyNick-notice',
                    content: '<input type="text" id="nick-input" />' + return_notice_word(),
                    cancelValue: '取消',
                    cancel: function () {
                    },
                    okValue: '确定',
                    ok: function () {
                        requestModifyNick();
                        return false;
                    }
                });

                diaLogs.showModal();

                $('.err-notice.modifyNick-notice').find('.ui-dialog-close').text('');

                $('#nick-input').focus(function () {
                    notice_word_remove();
                    $(this).after(return_notice_word());
                });

                function return_notice_word() {
                    var coin = 0;
                    var coin = $('.payment_block .paytype:eq(0) .num').text();
                    if(modifyNickFree == '1'){
                        return '<p class="ui-state-error-text left">你有一次免费修改昵称机会</p>';
                    }
                    else if (coin < $conf.modifyNickCost) {
                        return "<p class='ui-state-error-text'>修改昵称需要花费" + $conf.modifyNickCost + "欢朋币</p><p class='ui-state-error-text left'>欢朋币不足请充值<a class='right modifyNick-recharge' href='" + $conf.person + "recharge.php'>充值</a></p>";
                    } else {
                        return "<p class='ui-state-error-text'>修改昵称需要花费" + $conf.modifyNickCost + "欢朋币</p>";
                    }
                }

                function requestModifyNick() {

                    var nick = $('#nick-input').val();

                    if (!nick) {
                        err_notice('昵称不能为空');
                        return false;
                    }
                    if(nick.length>=3 && nick.length<=12){
                        var requestUrl = $conf.api + 'user/revise/alterUserNick.php';
                        var requestData = {
                            uid:uid,
                            encpass:enc,
                            nick:nick
                        };
                        ajaxRequest({url:requestUrl,data:requestData},function(responseData){
                            diaLogs.close().remove();
                            nickBlock.find('.identifyDetail').text(nick);
                            personalBlock.find('.nick').text(nick);
                            coinBlock.find('.num').text(digitsFormat(responseData.hpcoin));
                            location.href = location.href;
                        },function(responseData){
                            if(responseData.type==2){
                                err_notice(responseData.desc);
                            }else{
                                err_notice('修改失败');
                            }
                        });
                    }else{
                        err_notice('昵称长度应为3-12位');
                    }

                }


                function err_notice(text) {
                    notice_word_remove();
                    $('#nick-input').after('<p class="ui-state-error-text">' + text + '</p>');
                }

                function notice_word_remove() {
                    $('.modifyNick-notice .ui-state-error-text').remove();
                    $('.modifyNick-notice .modifyNick-recharge').remove();
                }
            }

            function normalStatus() {
                var nickVal = getCookie('_unick');
                var htmlsrt = '<span class="identifyDetail mt-12 mr-20 left">' + nickVal + '</span> <span class="option mt-12 left">修改</span> <div class="clear"></div>';
                nickCtr.html(htmlsrt);

                var option = nickCtr.find('.option');
                option.bind('click', editStatus);
            }
        },
        sex: function () {
            var cookie = decodeURIComponent(getCookie('_uinfo'));
            var sex = 0;

            if (cookie) sex = parseInt(cookie.split(':')[3]);

            var sexSelect = a('#p_usex').find('.controls span');
            var male = a('#p_usex').find('.controls span').eq(0);
            var female = a('#p_usex').find('.controls span').eq(1);


            initSex(sex);

            male.bind('click', function () {
                if(!$(this).hasClass('selected'))
                    changeSex(1);
            });
            female.bind('click', function () {
                if(!$(this).hasClass('selected'))
                    changeSex(0);
            });
            function changeSex(sex) {
                a.ajax({
                    url: c.domain + 'api/user/revise/alterUserSex.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: getCookie('_uid'),
                        encpass: getCookie('_enc'),
                        sex: sex
                    },
                    success: function () {
                        initSex(sex);
                        cookie = cookie.split(':');
                        cookie[3] = sex;
                        cookie = cookie.join(':');
                        setCookie('_uinfo', cookie);
                    }
                });
            }

            function initSex(sex) {
                sexSelect.removeClass('selected');
                if (sex)
                    male.addClass('selected');
                else
                    female.addClass('selected');
            }
        },
        certifyEmail: function (s, d) {
            var ctr = $('#p_umail .controls');
            if (!s) {
                unCertifyStatus();
            } else {
                certifyStatus(s, d);
            }

            function unCertifyStatus() {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">尚未认证</span> <span class="option mt-12 left">立即认证</span> <div class="clear"></div>';
                ctr.html(htmlstr);

                var option = ctr.find('.option');
                option.bind('click', function () {
                    location.href = c.domain + 'personal/mp/certify_email';
                });
            }

            function certifyStatus(s, d) {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">' + d + '</span>';
                if (s == $conf.certStatus.mail.wait) {
                    htmlstr += '<span class="option mt-12 left notCertify"><a href="' + c.domain + 'personal/mp/modify_email' + '"></a>未认证</span>';
                } else if (s == $conf.certStatus.mail.pass) {
                    htmlstr += '<span class="option mt-12 left"><a href="' + c.domain + 'personal/mp/certifiedEmail' + '"></a>已认证</span>'
                }
                htmlstr += '<div class="clear"></div>';

                ctr.html(htmlstr)
            }
        },
        certifyPhone: function (s, d) {
            var ctr = $('#p_uphone .controls');
            if (!s) {
                unCertifyStatus();
            } else {
                certifyStatus(d);
            }

            function unCertifyStatus() {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">尚未认证</span> <span class="option mt-12 left">立即认证</span> <div class="clear"></div>';
                ctr.html(htmlstr);

                var option = ctr.find('.option');
                option.bind('click', function () {
                    loginFast.bindingMobile();
                    //location.href = c.domain + 'personal/mp/certify_phone';
                });
            }

            function certifyStatus(d) {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">' + d + '</span><span class="option mt-12 left">已认证</span><div class="clear"></div>';
                ctr.html(htmlstr);
            }
        },
        address:function(addr, pid, cid){
            var selfAddr = addr;
            var selfPid = Number(pid);
            var selfCid = Number(cid);
            normalStatus(selfAddr);

            function normalStatus(addr){

                if(addr){
                    var html = '<span class="identifyDetail mt-12 mr-20 left">' + addr + '</span><span class="option mt-12 left">修改</span><div class="clear"></div>';
                }else{
                    var html = '<span class="identifyDetail mt-12 mr-20 left">填写您的地址</span><span class="option mt-12 left">添加</span><div class="clear"></div>';
                }

                $("#anchorAddr .controls").html(html).find('.option').click(function(){
                    editStatus();
                });

            }

            function editStatus(){
                var html = '<div class="select-container">\
                                <div class="selectCity">\
                                    <input type="text" placeholder="请选择省/市" id="proCity" readonly="readonly">\
                                    <span class="selectTitle">\
                                        <div class="title block-title">\
                                            <div class="title-prov curr">省</div>\
                                            <div class="title-city">市</div>\
                                        </div>\
                                        <div class="contentBox">\
                                            <div class="content">\
                                                <div class="contentLeft" id="contentLeft"></div>\
                                                <div class="contentRight" id="contentRight"></div>\
                                            </div>\
                                        </div>\
                                    </span>\
                                </div>\
                                <div class="resLoc">\
                                    <input type="text" placeholder="请输入您的具体地址" id="conLoc">\
                                </div>\
                                <div class="handle">\
                                    <button class="saveBtn">保存</button>\
                                    <button class="cancelBtn">取消</button>\
                                </div>\
                            </div>';
                $("#anchorAddr .controls").html(html);

                var requestUrl = $conf.api + 'other/addressMap.php';
                var requestData = {
                    type:1,
                    uid:getCookie('_uid'),
                    encpass:getCookie("_enc")
                };
                ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
                    var resList = responseData.list;
                    var proList = [];
                    for(var i in resList){
                        proList.push(createPro(resList[i]));
                    }
                    $("#contentLeft").html(proList);
                    bindEvent(resList);


                    $('.resLoc,#conLoc').focus(function () {
                        $('.selectTitle').css('display','none');
                    });

                    $(".select-container .handle .saveBtn").click(function () {
                        var detail = $('.resLoc input').val();
                        var requestUrl = $conf.api + 'user/revise/alterUserAddr.php';
                        var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),pid:selfPid,cid:selfCid,detail:detail};
                        ajaxRequest({url:requestUrl,data:requestData},function(){
                            normalStatus(getAddress(selfPid, selfCid, detail));
                        },function(responseData){
                            if(responseData.type ==1){
                                alert('修改失败');
                            }else{
                                alert(d.desc);
                            }
//                            normalStatus(selfPid,selfCid,selfAddr);
                        });
                    });
                    $(".select-container .handle .cancelBtn").click(function () {
                        normalStatus(selfAddr);
                    });

                    function getAddress(pid,cid,detail){
                        var pro = resList[pid-1].name;
                        var citylist = resList[pid-1].list;
                        var city = '';
                        for(var i in citylist){
                            if(citylist[i].id == cid){
                                city = citylist[i].name;
                            }
                        }

                        return pro + city + detail;
                    }
                });

            }




            function createPro(obj) {

                if(obj.pid && obj.name && obj.list){

                    var onePro = '<a data-pid="'+obj.pid+'">'+obj.name+'</a>';
                    return onePro;
                }

            }
            function bindEvent(resList) {
                $('#contentLeft>a').click(function (e) {
                    e.preventDefault();

                    var indexT = $(this).attr('data-pid');
                    selfPid = indexT;
                    if(resList[indexT-1].list){
                        var cityJson = resList[indexT-1].list;
                    }
                    $('#proCity').val($(this).html());

                    var cityList = [];
                    for(var j in cityJson){
                        cityList.push(CreateCity(cityJson[j]));
                    }

                    $('#contentRight').html(cityList);

                    //animation
                    $('.title-prov').removeClass('curr');
                    $('.title-city').addClass('curr');
                    $('.content').css('left','-460px');

                    $('#contentRight>a').click(function (e) {
                        e.preventDefault();
                        selfCid = $(this).attr('data-id');
                        var oldLoc = $('#proCity').val();
                        $('#proCity').val(oldLoc+'/'+$(this).html());
                        $('.content').css('left','0');
                        $('.title-prov').addClass('curr');
                        $('.title-city').removeClass('curr');

                    });

                });

            }

            function CreateCity(obj) {
                if(obj.id && obj.name){
                    var oneCity = '<a data-id="'+obj.id+'">'+obj.name+'</a>';
                    return oneCity;
                }
            }
        },
        certifyBankCard: function (s, d) {
            var ctr = $('#p_ubankcard .controls');
            if (!s) {
                unCertifyStatus();
            } else {
                certifyStatus(s, d)
            }
            function unCertifyStatus() {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">尚未认证</span> <span class="option mt-12 left">立即认证</span> <div class="clear"></div>';
                ctr.html(htmlstr);

                var option = ctr.find('.option');
                option.bind('click', function () {
                    location.href = c.domain + 'personal/mp/certify_bankcard';
                });
            }

            function certifyStatus(s, d) {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">' + d + '</span>';
                if (s == $conf.certStatus.bank.wait) {
                    htmlstr += '<span class="option mt-12 left">审核中</span>';
                } else if (s == $conf.certStatus.bank.pass) {
                    htmlstr += '<span class="option mt-12 left">已认证</span>'
                }
                htmlstr += '<div class="clear"></div>';

                ctr.html(htmlstr)
            }
        },
        certifyRealName: function (s, d) {
            return;
            var ctr = $('#p_urealname .controls');

            if (!s) {
                unCertifyStatus();
            } else {
                certifyStatus(s, d)
            }

            function unCertifyStatus() {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">尚未认证</span> <span class="option mt-12 left">立即认证</span> <div class="clear"></div>';
                ctr.html(htmlstr);

                var option = ctr.find('.option');
                option.bind('click', function () {
//                    location.href = c.domain + 'personal/mp/certify_realname';
                });
            }

            function certifyStatus(s, d) {
                var htmlstr = '<span class="identifyDetail mt-12 mr-20 left">' + d + '</span>';
                if (s == $conf.certStatus.ident.wait) {
                    htmlstr += '<span class="option mt-12 left">审核中</span>';
                } else if (s == $conf.certStatus.ident.pass) {
                    htmlstr += '<span class="option mt-12 left">已认证</span>'
                }
                htmlstr += '<div class="clear"></div>';

                ctr.html(htmlstr)
            }
        },
        threePartyBind:function(d){

            var bindImage = {
                wechat:$conf.domain + 'static/img/threeParty/wechat-60.png',
                qq:$conf.domain + 'static/img/threeParty/qq-60.png',
                weibo:$conf.domain + 'static/img/threeParty/weibo-60.png'
            }
            var unBindImage = {
                wechat: $conf.domain + 'static/img/threeParty/60-wechat-gray.png',
                qq: $conf.domain + 'static/img/threeParty/60-qq-gray.png',
                weibo: $conf.domain + 'static/img/threeParty/60-weibo-gray.png'
            }


            function todoUnbindDialog(channel){
                var list = ['qq','wechat','weibo','phone'];
                var account = 0;
                for( var i in list){
                    var param = list[i]+'status';
                    if(d[param] == 1){
                        account ++;
                    }
                }
                var text = '';
                if(account == 1){
                    text= '您当前只有一个绑定账号，结束绑定后，此账号的信息将被清空，是否要继续？';
                }else{
                    text = '确定要解绑当前账号？';
                }
                var dialogs = dialog({
                    skin: 'err-notice unbind-threeparty',
                    content: '<p>'+text+'</p>',
                    fixed: true,
                    title: '解除绑定',
                    button:[
                        {
                            value:'确定',
                            autofocus:true,
                            callback:function(){
                                todoUnBind(channel);
                                return true;
                            }
                        },
                        {
                            value:'取消',
                            callback:function () {
                                return true;
                            }
                        }
                    ]
                });
                dialogs.showModal();
            }


            function todoBind(channel){
                if(['wechat','qq','weibo'].indexOf(channel) > -1){
                    var params = {order:'bind',channel:channel,ref:'bind'};
                    location.href = $conf.person + 'oauth/index.php?' + $.param(params);
                }
            }
            function todoUnBind(channel){
                if(['wechat','qq','weibo'].indexOf(channel) > -1){
                    var data={
                        uid:getCookie('_uid'),
                        encpass:getCookie('_enc'),
                        channel:channel
                    };
                    var url = $conf.api + 'app/unBindThreeParty.php'
                    ajaxRequest({url:url,data:data},function (data) {
                        //更新相关渠道动作以及指令
                        d[channel+'status'] = 0;
                        unbindStatus(channel);
                        if(!parseInt(data['validBind']))
                            logout_submit();
                    },function(){
                        //
                    });
                }
            }

            function unbindStatus(channel){
                var text = {
                    wechat:'微信',
                    qq:"QQ",
                    weibo:'微博'
                }
                var dom = $('.'+channel+'-bind');
                dom.find('img').attr('src',unBindImage[channel]);
                dom.removeClass('binded').find('.option').html('<span class="nick"></span> <a class="todo">绑定'+text[channel]+'账号</a>');
                dom.find('.option .todo').on('click', function(){
                    todoBind(channel);
                });
            }
            function bindStatus(channel,nick){
                var dom = $('.'+channel+'-bind');
                dom.find('img').attr('src',bindImage[channel]);
                dom.addClass('binded').find('.option').html('<span class="nick">'+nick+'</span> <a class="todo">［解绑］</a>')
                dom.find('.option .todo').on('click', function(){
                    todoUnbindDialog(channel);
                });
            }

            if(d.weibostatus == 1){
                bindStatus('weibo', d.weibonick);
            }else{
                unbindStatus('weibo');
            }
            if(d.wechatstatus == 1){
                bindStatus('wechat', d.wechatnick);
            }else{
                unbindStatus('wechat');
            }
            if(d.qqstatus == 1){
                bindStatus('qq', d.qqnick);
            }else{
                unbindStatus('qq');
            }
        }
    };
    var url = $conf.api + "user/attested/rpc_ajax.php";
    var data = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc'),
    };
    ajaxRequest({url:url,data:data},function (d) {
        index.init(d);
    });
}(jQuery));
