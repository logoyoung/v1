var gift_switch = 1;
var Gift;
var gBean_obj;

//鱼丸领取机制

!function(a){
    a('#get_rem').on('click','a.receive',function(){
        hb_receive();
    });
    function initGetHpBean(lvl,time,isVip){

        a('#box_show').data('lvl',lvl);
        if(isVip == 1){
            //for the vip user
        }else{
            if(lvl > 5){
                a.each(a('#get_rem .lw_list li'), function(index,element){
                    a(element).find('span img').attr('src','static/img/get_bean/already.png');
                    a(element).find('a').removeClass().addClass('already').text('已领取');
                });
                $('#get_time').html('你真无聊');
                return false;
            }
        }

        var curr = a('#get_rem .lw_list li:eq('+(lvl-1)+')');
        if(lvl > 1){
            a.each(curr.prevAll(), function(index, element){
                a(element).find('span img').attr('src','static/img/get_bean/already.png');
                a(element).find('a').removeClass().addClass('already').text('已领取');
            });
        }

        if(time <= 0){
            //设置可领取状态
            curr.find('span img').attr('src', 'static/img/get_bean/receive.png');
            curr.find('a').removeClass().addClass('receive').text('可领取');
            $('#get_time').html('可领取');
        }else{
            curr.find('span img').attr('src','static/img/get_bean/grow.git');
            curr.find('a').removeClass().addClass('grow').text('培育中...');
            var sCount = time + 60;
            var minute = parseInt(sCount/60);
            var second = parseInt(sCount%60);

            minute = minute < 10 ? '0'+minute : minute;
            second = second < 10 ? '0'+second : second;

            a('#get_time').html(minute + ':' +second);
            setTimeout(setTime,1000);
        }
    }
    function setTime(){
        var time = a('#get_time').text();
        if(!time || time == "" || time.length <= 0 || time.indexOf(':') < 0){
            return false;
        }
        var tArray = time.split(':');
        var minute = parseInt(tArray[0]);
        var second = parseInt(tArray[1]);

        var sCount = minute*60+second;

        if(sCount > 1){
            sCount--;

            var m = parseInt(sCount/60);
            var s = parseInt(sCount%60);

            m = m < 10 ? ('0'+m) : m;
            s = s < 10 ? ('0'+s) : s;

            a('#get_time').html(m + ':' + s);
            setTimeout(setTime, 1000);
        }else{
            //可领取阶段
            var lvl = Number(a("#box_show").data('lvl'));
            var curr = a('#get_rem .lw_list li:eq('+(lvl - 1)+')');
            curr.find('span img').attr('src', 'static/img/get_bean/receive.png');
            curr.find('a').removeClass().addClass('receive').text('可领取');
            $('#get_time').html('可领取');
        }
    }

    function hb_receive(){
        $conf = conf.getConf();
        a.ajax({
            url:$conf.api + 'shamApi_gb_enter.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                lvl:a('#box_show').data('lvl'),
                luid:$ROOM.anchorUserID,
                event:'pick'
            },
            success:function(d){
                if(d.isSuccess == 1){
                    var lvl = parseInt(d.lvl);
                    var time = parseInt(d.time);
                    var isVip = parseInt(d.isVip);

                    initGetHpBean(lvl,time,isVip);
                }else{
                    if(d.ret == 1){
                        var lvl = parseInt(d.lvl);
                        var time = parseInt(d.time);
                        var isVip = parseInt(d.isVip);
                        alert('你已经领取过了');
                        initGetHpBean(lvl,time,isVip);
                    }
                }
            }
        });
    }
    gBean_obj = {
        show_time:initGetHpBean
    }
}(jQuery);

(function(a){
    var $conf = conf.getConf();
    var ajaxDefault = {
        url:'',
        type:'post',
        dataType:'json',
        success:function(){}
    }
    Gift = {
        send_gift:function(c){
            var b = {
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                luid:$ROOM.anchorUserID,
                liveid:$ROOM.liveID,
                gid:c.giftid,
                num:c.num,
                type: c.gtype
            };
            gift_switch = 0;
            var errorFn = function(){
                alert('赠送礼物失败');
                gift_switch = 1;
                return false;
            }
            var successFn = function(d){
                if(d.isSuccess == 1){
                    //alert('赠送成功');
                    gift_switch = 1;
                }else{
                    gift_switch = 1;
                    if(d.code == -4){
                        alert('您的欢朋币不足，请充值');
                    }else{
                        alert(d.msg);
                    }
                }
            }
            var ajaxOption = {
                url:$conf.api + 'shamApi_send_gift.php',
                data:b,
                success:successFn,
                error:errorFn
            }
            ajaxOption = $.extend(ajaxDefault, ajaxOption);
            a.ajax(ajaxOption);
        },
        send_bean:function(c){
            var b = {
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                luid:$ROOM.anchorUserID,
                liveid:$ROOM.liveID,
                gid:c.giftid,
                num: c.num
            };
            gift_switch = 0;
            var errorFn = function(){
                alert('赠送礼物失败');
                gift_switch = 1;
                return false;
            }
            var successFn = function(d){
                if(d.isSuccess == 1){
                    //alert('赠送成功');
                    gift_switch = 1;
                }else{
                    gift_switch = 1;
                    if(d.code == -4){
                        alert('您的欢朋豆不足，请充值');
                    }else{
                        alert(d.msg);
                    }
                }
            }
            var ajaxOption = {
                url:$conf.api + 'shamApi_send_bean.php',
                data:b,
                success:successFn,
                error:errorFn
            }
            ajaxOption = $.extend(ajaxDefault, ajaxOption);
            a.ajax(ajaxOption);
        }
    }

}(jQuery));
var room = function(){
    var Room;
    var $conf = conf.getConf();
    var a = jQuery;
    var _roomDetail = $ROOM;
    var _chatMsgList = _roomDetail.chatMessageList;
    var _cip = _roomDetail.chatServer[0].split(":")[0] || '';
    var _cport = _roomDetail.chatServer[0].split(":")[1] || '';
    var _videoList = _roomDetail.videolist;
    var _isLogin = pageUser.isLogin;
    var _isFollow = _roomDetail.isFollow == '1' ? true : false;
    var _user = _isLogin ? pageUser.user : {};

    var global_gift = {};

    //room new
    var clickuid;
    var gustid = 0;
    var click_chatid = null; //msgid 为举报弹幕准备
    var clicknickuser;
    var user_black_list = new Array();
    var roomgrouplist = new Array();

    var screen_width = 0;

    if(pageUser.isLogin){
        if(pageUser.user.userID == $ROOM.anchorUserID){
            roomgrouplist[pageUser.user.userID] = [];
            roomgrouplist[pageUser.user.userID]['roomgroup'] = 5;
        }else{
            roomgrouplist[pageUser.user.userID] = [];
            roomgrouplist[pageUser.user.userID]['roomgroup'] = 1;
        }
    }

    function menu_show(e){
        var d = pageUser.isLogin ? pageUser.user.userID : gustid;
        clickuid = $(e).data('uid');
        clicknickuser = $(e).html();

        //举报弹幕需要msgid
        click_chatid = $(e).next().data('msgid');
        if(click_chatid == null || click_chatid == ''){
            //||Number(gid) > 5
            $('#user_report').hide();
        }else{
            $('#user_report').show();
        }
        if((roomgrouplist[d]['roomgroup'] >= 4 && roomgrouplist[clickuid]['roomgroup'] < 4) || (roomgrouplist[clickuid]['roomgroup'] < 4 && pageUser.user.groupid == 5 ) || (roomgrouplist[clickuid]['roomgroup'] < 4 && pageUser.user.groupid == 2) ){
            $('#black_img').show();

        }else{
            $('#black_img').hide();
        }
        if(roomgrouplist[clickuid]['pg'] == 5){
            $("#black_img").hide();
        }
        $('#adminsetup').hide();
        if(roomgrouplist[d]['roomgroup'] == 5 || (pageUser.user.groupid == 5 && roomgouolist[clickuid]['roomgroup']) < 5){
            $('#adminsetup').show();
            console.log('run the adminsetup');
            if(roomgrouplist[clickuid]['roomgroup'] == 4){
                $("#adminsetup").html('解除管理员');
                $('#adminsetup').attr('rel', 1);
            }else{
                $('#adminsetup').html('任命管理员');
                $('#adminsetup').attr('rel', 4);
            }
        }

        if(user_black_list.indexOf(parseInt(clickuid)) > -1){
            $('#user_black').html('取消屏蔽');
            $('#user_black').attr('rel', 1);
        }else{
            if(user_black_list.length >= 10){
                $('#user_black').html('屏蔽人数已满');
                $('#user_black').attr('rel', 3);
            }else{
                $('#user_black').html('屏蔽该用户');
                $('#user_black').attr('rel', 0);
            }
        }
    }
    function adminreg(){
        var data = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
            adminNick:clicknickuser
        }
        $.ajax({
            url:$conf.api + 'addHomeAdmin.php',
            type:'post',
            dataType:'json',
            data:data,
            success:function(d){
                if(d.isSUccess == 1){
                    alert('设置管理员成功');
                }
            }
        });
    }
    function admindel(){
        var data = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
            adminID:clickuid
        }
        $.ajax({
            url:$conf.api + 'cancelHomeAdmin.php',
            type:'post',
            dataType:'json',
            data:data,
            success:function(d){
                if(d.isSuccess == 1){
                    alert('取消管理员成功');
                }
            }
        })
    }
    var black_obj = {
        black_show:function(){
            //
            var c = !pageUser.isLogin ? gustid : pageUser.user.userID;
            if(pageUser.user.groupid != 2){
                if((roomgrouplist[c]['roomgroup'] < 4 && pageUser.user.groupid != 5) || c == clickuid){
                    return false;
                }
            }
        },
        black_myblacklist_add:function(){
            //添加屏蔽用户
            if(user_black_list.indexOf(parseInt(clickuid)) > -1 || user_black_list.length > 10){
                return false;
            }
            $('#user_black').html('取消屏蔽');
            $('#user_black').attr('rel', 1);
            user_black_list.push(parseInt(clickuid));
            //send to the rtmpplayer?
        },
        black_myblacklist_del:function(){
            if(user_black_list.indexOf(parseInt(clickuid)) == -1){
                return false;
            }
            $('#user_black').html('屏蔽该用户');
            $('#user_black').attr('rel', 0);
            var c = user_black_list.indexOf(parseInt(clickuid));
            user_black_list.splice(c, 1);//从屏蔽列表删除该用户
            //send to the rtmpplayer?
        },
        black_myblacklist_send:function(c){
            //向播放器发送我的屏蔽列表
        },
        chat_report:function(){
            var d = !pageUser.isLogin ? gustid : pageUser.user.userID;
            if(d == clickuid) {
                alert('不能举报自己');
                return false;
            }
            if(click_chatid == '' || click_chatid.length <= 1){
                $('#itemopen').hide();
                return false;
            }
            //handle the event;
        }
    }

    //
    $('#user_black').on('click', function(){
        if($(this).attr('rel') == 0){
            black_obj.black_myblacklist_add();
        }else{
            if($(this).attr('rel') == 1){
                black_obj.black_myblacklist_del();
            }
        }
        return false;
    });
    $("#user_report").on('click', function(){
        black_obj.chat_report();
    });

    $('body').append($('#itemopen'));
    $('#lr_chat_ul').delegate('li span.lr_username','click',function(t){
        // alert($(this).data('uid'));
        clickuid = $(this).data('uid');
        var d = pageUser.isLogin ? pageUser.user.userID : gustid;
        if(!pageUser.user.userID){
            return false;
        }

        if(d != clickuid){
            var c = $(this).offset();
            menu_show(this);
            $('#itemopen').show().css({
                top:c.top - $('#itemopen').outerHeight(true),
                left: c.left
            });
        }

        return false;
    });
    $('.user_wrap').on('mouseleave', function(){
        $('#itemopen').hide();
    });
    $('#adminsetup').on('click',function(){
        if($(this).attr('rel') == 1){
            admindel()
        }else{
            if($(this).attr('rel') == 4){
                adminreg();
            }
        }
        $('#itemopen').hide();
        return false;
    });

    //gb_enter
    function gb_enter(luid){
        $.ajax({
            url:$conf.api + 'shamApi_gb_enter.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                luid:luid,
                event:'enter'
            },
            success:function(d){
                if(d.lvl){
                    gBean_obj.show_time(d.lvl, d.time, d.isVip);
                }
            }
        })
    }
    function gb_updateTime(luid){
        $.ajax({
            url:$conf.api + 'shamApi_gb_enter.php',
            type:'post',
            dataType:'json',
            data:{
                uid:getCookie('_uid'),
                encpass:getCookie('_enc'),
                luid:luid,
                event:'uptime'
            },
            success:function(d){
                console.log(d);
            }
        });
    }

    //var damoo = Damoo('liveRoom_video', 'dm-canvas', 20);
    //damoo.start();

    _user.enc = getCookie("_enc") || '';

    Room = {
        PageHtml:{
            pageHeaderStr:function(d,u){
                var htmlstr = '';
                if(d == true){
                    htmlstr += '<li class="ch_user_block"><div class="ch_user"><div class="ch_user_facediv"><a class="ch_user_face" href="personal/mp/"><img src="'+u.pic+'"><span>'+u.nickName+'</span></a></div></div></li>';
                    htmlstr += '<li><a href="personal/pm/">消息</a></li><li><a href="personal/recharge.php">充值</a></li><li><a class="h_logout">退出</a></li>';
                }
                else{
                    htmlstr += '<li class="h_login">登陆</li><li class="h_reg">注册</li><li><a class="h_login">消息</a></li><li class="h_login"><a>充值</a>'
                }
                htmlstr = '<ul class="ch_navbar_ul left">'+htmlstr+'</ul>';
                return '    <ul class="ch_navbar_ul left"><li><a href="index.php">首页</a></li><li><a href="LiveHall.php">直播</a></li></ul>' + htmlstr;


            },
            navPlayerStr:function(){
                var d = _roomDetail;
                var htmlstr = '<div class="player_face"><img src="'+d.anchorUserPicURL+'"></div><div class="player_info"><p class="player_gamedesc">'+d.liveTitle+'</p><div class="anchor_report"><span class="anchor_icon"></span><span class="notice_word">举报房间</span></div><div class="clear"></div>'
                    //+'<div class="live_nav_tag"><span>相声演员</span><span>颜值爆表</span><span>手法一流</span></div>'
                        +'<div class="anchor_desc"><div class="anchorLevel"><span class="anchor_icon lv1 left"></span><span class="levelBarSpan"><strong width="0%"></strong></span><div class="clear"></div></div><div class="anchor_income"><span class="anchor_icon hpbean"></span><span class="income">5000</span><div class="clear"></div></div></div>'
                    +'<div class="clear"></div><p class="player_otherdesc"><span class="anchor_icon anchor"></span><span class="anchor_name">'+d.anchorNickName+'</span><span class="anchor_icon gamename"></span><span class="game_name">'+d.gameName+'</span><span class="anchor_icon viewerIcon2"></span><span class="viewer_count">'+d.viewerCount+'人在观看</span><div class="clear"></div></p></div>';

                return htmlstr;
            },
            navAttentionStr:function(f,c){
                var htmlstr = '';
                htmlstr = f == true ? '<div id="followbtn" class="nav_attention_right followed" style="text-align: center;">已关注</div><div class="nav_attention_left">'+c+'</div>' : '<div id="followbtn" class="nav_attention_right"><span class="anchor_icon followIcon3"></span>关注</div><div class="nav_attention_left">'+c+'</div>';

                return ''+htmlstr+'<div class="clear"></div>' ;//<div class="nav_attention"></div>
            }
        },
        init:function(){
            var n = this;
            // n.chat.init(_cip, _cport);
            // if(_isLogin) n.chat.login();

            if(_isLogin){
                $('.lr_insend').before('<textarea id="inwrite" placeholder="这里是输入内容" class="lr_incon" maxlength="50"></textarea>');
            }else{
                $('.lr_insend').before('<div class="lr_incon" style="height: 18px;width: 238px;"><p style="margin: 0;color:#969798;">直播间发言请先 <a style="color: #0099ff;cursor: pointer;" onclick="loginFast.login(0)">登录</a>或<a style="color: #0099ff;cursor: pointer;"  onclick="loginFast.login(1)">注册</a></p></div>');
            }

            n.initHeader();
            n.initRoomContent_nav();
            n.initRoomContent_bottom();

            //n.initChatMsgList();
            n.initRankList();
            n.initChatOpt();

            var r = a(".enlargeblock");
            r.bind('click',function(){
                if($(this).hasClass('addEnlargeBlock'))
                    n.lrRightShow();
                else
                    n.lrRightHidden()
            });

            screen_width = parseInt($('#liveRoom_video').width());
            var bannerClose = $('.banner .close');
            bannerClose.bind('click',function(){
                $('.banner').remove();
                n.resize();
            });

            a(window).bind('resize', n.resize);
            n.resize();
            n.rankList(1);
        },
        initHeader:function(){//定义头部导航栏
            var u = _isLogin && pageUser.user;
            a('.ch_navbar.right').html(this.PageHtml.pageHeaderStr(_isLogin, u));
            if(_isLogin){
                var lout = a('.h_logout');
                lout.bind('click',function(){
                    logout_submit();
                });
            }else{
                var lin = a('.h_login'),
                    reg = a('.h_reg');
                lin.bind('click',function(){ loginFast.login(0); });
                reg.bind('click',function(){ loginFast.login(1); });
            }
            return this;
        },
        initRoomContent_nav:function(){
            var self = this;
            var count = parseInt(_roomDetail.fansCount);
            var i = 0 ;
            a('.live_nav_player').html(self.PageHtml.navPlayerStr());

            (function initFollow(fC){
                console.log(i++);
                if(_isLogin && _isFollow) {
                    a('.nav_attention').html(self.PageHtml.navAttentionStr(true, fC));
                    a('.nav_attention_right').hover(function(){
                        $(this).text('取消关注')
                    }, function(){
                        $(this).text('已关注');
                    })
                }else {
                    a('.nav_attention').html(self.PageHtml.navAttentionStr(false, fC));
                }

                var fbtn = a('#followbtn');
                fbtn.bind('click',handleFollowEvent);
                function handleFollowEvent(){
                    if(!_isLogin){
                        loginFast.login(0);
                        return;
                    }
                    var uid = getCookie('_uid');
                    var enc = getCookie('_enc');
                    (function(){
                        var url = _isFollow ? 'followUserCancel.php' : 'followUser.php';
                        a.ajax({
                            url:"http://" + document.domain + '/a/' + url,
                            type:'post',
                            dataType:'json',
                            data:{
                                uid:uid,
                                encpass: enc,
                                targetUserID:_roomDetail.anchorUserID
                            },
                            success:function(d){
                                if(d.isSuccess == '1'){
                                    _isFollow = !_isFollow;
                                    //小心循环调用的多次执行
                                    initFollow(_isFollow ? ++count : --count );
                                }else{
                                    alert('登陆异常');
                                }
                            }
                        });
                    }());
                }
            }(count))
            return false;
        },
        initRoomContent_bottom:function(){
            var self = this;
            (function(){ //定义直播列表
                a.ajax({
                    url:'http://dev.huanpeng.com/a/getLList.php',
                    type:'post',
                    dataType:'json',
                    data:{
                        size:8
                    },
                    success:function(d){
                        console.log(d);
                        var ll = d.liveList;
                        for(var i in ll)
                            a('.liveRoomOther .videolist:eq(0)').append(vl(ll[i],1));

                        self.tabConResize();
                    }
                });
            }());
            (function(){//定义录像列表
                console.log(_videoList);
                var v = _videoList.videoList;
                for(var i in v)
                    a('.liveRoomOther .videolist:eq(1)').append(vl(v[i],0));

                self.tabConResize();
            }());
            (function(){//定义直播公告
                a.ajax({
                    url:$conf.api + 'shamApi_getLiveBulletin.php',
                    type:'post',
                    dataType:'json',
                    data:{
                        uid:_roomDetail.anchorUserID
                        //encpass: _user.enc,
                        //targetUserID:_roomDetail.anchorUserID
                    },
                    success:function(d){
                        var liveRoomOther = document.getElementsByClassName('liveRoomOther')[0];
                        var bulletin = liveRoomOther.getElementsByClassName('tab_con')[2].getElementsByClassName('bulletin')[0];
                        bulletin.textContent = d.message;
                    }
                });
            }());

            function vl(d,t){
                //t = 1 为直播
                var url = '';
                var htmlstr = '';

                url = t == 1 ? 'liveRoom.php?luid=' + d.anchorUserID : 'videoRoom.php?videoid=' + d.videoID;
                htmlstr +=  '<div class="liveOne">';
                htmlstr +=      '<a href="'+url+'">';
                htmlstr +=          '<div class="imagecontainer">';
                htmlstr +=              '<img src="'+ d.posterURL+'">';
                //htmlstr +=              '<div class="live_anchor_name">';
                //htmlstr +=  t == 1 ?    '<span>'+ d.anchorNickName+'</span>' : '<span>'+ d.publisherNickName+'</span>';
                //htmlstr +=              '</div>';
                htmlstr +=              '<div class="playopt"></div>'
                htmlstr +=          '</div>';
                htmlstr +=          '<div class="liveInfo">';
                htmlstr +=  t == 1 ?    '<div class="videoName">'+d.liveTitle+'</div>':'<div class="videoName">'+d.videoTitle+'</div>';
                htmlstr +=              '<div class="clear"></div>';
                htmlstr +=              '<div class="liveDetail">';
                htmlstr +=                  '<span class="anchor_icon anchor"></span>'
                htmlstr +=  t==1?           '<span class="anchor_nick">'+d.anchorNickName+'</span>' : '<span class="anchor_nick">'+d.publisherNickName+'</span>';
                htmlstr +=                  '<span class="anchor_icon viewerIcon"></span>'
                htmlstr +=  t == 1 ?        '<span>'+ d.viewerCount+'</span>' : '<span>'+ d.totalViewCount+'</span>';
                //htmlstr +=                  '<span class="anchor_icon commentIcon"></span>'
                //htmlstr +=  t == 1 ?        '<span>'+ d.upCount+'</span>':'<span>'+ d.collectCount+'</span>';
                htmlstr +=                  '<span class="game_name">'+d.gameName+'</span>'
                htmlstr +=                  '<div class="clear"></div>'
                htmlstr +=              '</div>'
                htmlstr +=          '</div>'
                htmlstr +=      '</a>';
                htmlstr +=  '</div>';

                return htmlstr;
            }
            var tab = a('.liveRoomOther_tab li');
            var tabCon = a('.liveRoomOther .tab_con');

            tab.bind('click',function(){
                tab.removeClass('selected')
                $(this).addClass('selected');

                tabCon.addClass('none');
                tabCon.eq($(this).index()).removeClass('none');
            });
        },
        initRankList:function(){
            var self = this;
            var ranktab = a('.lr_contribution_tab li');
            ranktab.bind('click', function(){
                ranktab.removeClass('selected');
                a(this).addClass('selected');

                a('.lr_contribution .tabCon').addClass('none');
                a('.lr_contribution .tabCon').eq(a(this).index()).removeClass('none');

                var type = parseInt(a(this).index()) + 1;
                self.rankList(type);
            });
        },
        rankList:function(type){
            $.ajax({
                url:$conf.api + 'LiveRoomRanking.php',
                type:'post',
                dataType:'json',
                data:{
                    timeType:type,
                    luid:_roomDetail.anchorUserID
                },
                success:function(d){
                    if(d.rankList){
                        var htmlstr = '<ul>';
                        for(var i in d.rankList){
                            var num = parseInt(i) + 1;
                            htmlstr += '<li> <span class="orderIcon numThird">'+ num +'.</span> <span class="icon icon_money anchor_icon hpcoin"></span> <span class="point">'+d.rankList[i].money+'</span> <span class="uNickname">'+d.rankList[i].nick+'</span> <div class="clear"></div></li>';
                        }
                        htmlstr += '</ul>';
                        a('.lr_contribution .tabCon').eq(type - 1).html(htmlstr);
                    }
                }
            })
        },
        initChatMsgList:function(){
            var self = this;
            for(var i in _chatMsgList){
                var m = _chatMsgList[i];
                var chatMsgLiStr = '';
                chatMsgLiStr += '<span class="lr_username" data-uid="'+ m.uid +'">' + m.nick + '</span>';
                chatMsgLiStr += '<span class="lr_userwords">'+ replace_em(m.msg)+'</span><div class="clear"></div>';
                chatMsgLiStr  = '<li>' + chatMsgLiStr + '</li>';
                a('.lr_chat_ul').append(chatMsgLiStr);
            }
            self.scroll();
        },
        initChatOpt:function(){
            var self = this;

            (function(){//表情按钮
                var options = {
                    id : 'facebox',
                    path : 'static/img/emoji/',
                    assign : 'inwrite',
                    tip : 'em_',
                    position:'top'
                };
                var selector = '.emoji';
                Emoji.init(selector, options);
            }());

            var clearMsg = a('.chatopt .opt_left .clearMsg');
            (function(){
                clearMsg.bind('click',function(){
                    var chatList = a('#lr_chat_ul li');
                    chatList.remove();
                })
            }());

            var lockScreen = a('.chatopt .opt_left .blockScreen');
            (function(){
                lockScreen.bind('click',function(){
                    if(lockScreen.hasClass('locked')){
                        lockScreen.removeClass('locked');
                    } else{
                        lockScreen.addClass('locked');
                    }
                });
            }())

            var sMes = a('.lr_insend');
            (function(){
                sMes.bind('click',function(){
                    var msg = a('.lr_incon').val();
                    if(!_isLogin) {
                        loginFast.login(0);
                        return;
                    }
                    self.chat.sendMess(msg);
                })
            }());
            a('#inwrite').keypress(function(event){
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    var msg = a('.lr_incon').val();
                    if(!_isLogin) {
                        loginFast.login(0);
                        return;
                    }
                    self.chat.sendMess(msg);
                }
            })
        },

        scroll:function(){
            var lockScreen = a('.chatopt .opt_left .blockScreen');
            if(lockScreen.hasClass('locked')) return false;

            var scrolltop = $('.lr_chat li:last').position().top -  $('.lr_chat').position().top + $('.lr_chat').scrollTop();
            $('.lr_chat').animate({scrollTop:scrolltop},100);
        },
        resize:function(){
            console.log('resize');

            Room.tabConResize();
            Room.lrChatResize();
            (function resizes(){
                var width = a(window).width();
                if(width <= 1100)
                    Room.lrRightHidden();
                else
                    Room.lrRightShow();
            }());
            $('.background-img').height($('.liveRoomContent').height());
        },
        tabConResize:function(){

            $('.live_nav_opshow').width($('.liveRoom_nav').width() - $('.live_nav_player').width() - parseInt($('.live_nav_player').css('margin-left')) - parseInt($('.live_nav_opshow').css('margin-right')) - 2 );


            (function(){
                var anchorLevel = $('.anchorLevel').width();
                var sharephone = $('.sharephone').width() + parseInt($('.sharephone').css('margin-right'));
                var sharegroup = $('.sharegroup').width();
                var shareopt = $('.nav_shareopt').width();
                if((anchorLevel + sharephone + sharegroup) >= shareopt){
                    $('.nav_shareopt .sharephone').hide();
                }else{
                    $('.nav_shareopt .sharephone').show();
                }
                if((anchorLevel + sharegroup) >= shareopt){
                    $('.nav_shareopt .anchorLevel').hide();
                }else{
                    $('.nav_shareopt .anchorLevel').show();
                }

                var attention_left = $('.nav_attention_left').width() + 22;
                var attention_right = $('.nav_attention_right').width() + 2;
                if((attention_left + attention_right) >= $('.live_nav_opshow').width()){
                    $('.nav_attention_left').hide();
                }else{
                    $('.nav_attention_left').show();
                }

                var liveRoomOpt = $('.liveRoom_opt').width();
                var task = $('.liveRoom_opt .task').width() + 1;
                var sign = $('.liveRoom_opt .sign').width() + 1;
                var anchor_money = $('.liveRoom_opt .anchor_money').width() + parseInt($('.liveRoom_opt .anchor_money').css('margin-left'));
                var gift = $('.liveRoom_opt .giftdiv').width() + parseInt($('.liveRoom_opt .giftdiv').css('margin-right'));

                if((task + sign + anchor_money + gift) >= liveRoomOpt){
                    $('.liveRoom_opt .anchor_money').hide()
                }else{
                    $('.liveRoom_opt .anchor_money').show()
                }
            }());

            if($('.liveRoom_opt').width() < 765){
                $('.anchor_money').hide();
            }else{
                $('.anchor_money').show();
            }

            var video_width = $('.liveRoom_left .liveRoomContent .liveRoom_video').width();
            $('.liveRoom_left .liveRoomContent .liveRoom_video').css('height', video_width / 16 * 9);

            var one = a('.liveRoomOther .liveOne');
            if(!one[0])
                return false;

            var lvOther = a('.liveRoomOther').width();

            if(lvOther < 855 && lvOther >= 700) {
                one.css('width','330');
                one.find('.imagecontainer').css("width",'305px');
                one.find('.imagecontainer').css("height",'164px');
                one.find('.live_anchor_name').css('width','305px');
                //one.find('.playopt').css('left','153');
            } else {
                one.css('width','285');
                one.find('.imagecontainer').css("width",'260px');
                one.find('.imagecontainer').css("height",'140px');
                one.find('.live_anchor_name').css('width','260px');
                //one.find('.playopt').css('left','130');
            }

            var lvOne = a('.liveRoomOther .liveOne').width();
            var count = parseInt(lvOther / lvOne);

            if(count == 2){
                var c = lvOther - count * lvOne;
                c = c / 6;

                $(".liveOne").each(function(){
                    if($(this).index() % 2 == 0){
                        $(this).css('margin-left',   c + "px");
                        $(this).css('margin-right',2*c + "px");
                    }else{
                        $(this).css('margin-left', 2*c + "px");
                        $(this).css('margin-right',  c + 'px');
                    }
                });
            }else if(count > 2){
                var c = lvOther - count * lvOne;
                c = c / (2 * count + 1);
                $(".liveOne").each(function(){
                    $(this).css({
                        'margin-left'  : c + 'px',
                        'margin-right' : c + 'px'
                    });
                });
            }

            var player = thisMovie('rtmpplayer');
            if(player.changeSize){
                var now = parseInt($('#liveRoom_video').width());
                console.log(now);
                console.log(screen_width);
                player.changeSize(now, screen_width);
                screen_width = now;
            }
        },
        lrChatResize:function(){
            var liveRoom_height = document.body.clientHeight - $('.container-header').height();
            $('.liveRoom_right').height(liveRoom_height);
            var banner_height =  $('#right_block_div .banner').height() || 0;
            var contribute_height = parseInt($('.liveRoom_right .lr_online').css('margin-top')),
                chat_opt_height = $('#right_block_div .chatopt').height() ,
                lr_write_height = $('#right_block_div .lr_inwrite').height() + 33;

            var lr_chat_height = liveRoom_height - banner_height - contribute_height - chat_opt_height - lr_write_height;

            $('.liveRoom_right .lr_chat').height(lr_chat_height);
        },
        lrRightHidden:function(){
            a('.liveRoom_left').css('margin-right','0px');
            a('#right_block_div').addClass('none');
            a(".liveRoom_right").css('width','0px');
            a('.enlargeBlock').addClass('addEnlargeBlock');
            a('.enlargeBlock .arrow_right').addClass('arrow_left').removeClass('arrow_right');
            Room.tabConResize();
        },
        lrRightShow:function(){
            if(a(window).width() <= 1100)
                $('.liveRoom_left').css('margin-right','0px');
            else
                $('.liveRoom_left').css('margin-right','');

            a('#right_block_div').removeClass('none');
            a(".liveRoom_right").css('width','');
            a('.enlargeBlock').removeClass('addEnlargeBlock');
            a('.enlargeBlock .arrow_left').addClass('arrow_right').removeClass('arrow_left');
            Room.tabConResize();
        }
    }
    Room.chat = {
        proxy:'',//swfobject.getObjectById('imProxy'),
        _sip:'',
        _sport:'',
        login:function(){
            this.proxy = swfobject.getObjectById('imProxy');
            this._sip = _cip;
            this._sport = _cport;

            var self = this;
            var uid,encpass,roomid;
            console.log(self.proxy);
            uid = getCookie('_uid') || 3000000000;
            encpass = getCookie("_enc") || 'abcdefghigklmnopqrstuvwxyz123456';//5ff63e737f17
            roomid = $ROOM.anchorUserID;

            //console.log(  '加载－－' +self.proxy.PercentLoaded());
            var interval = setInterval(function(){//防止 chatProxy 未加载完而出现错误
                try{
                    if(self.proxy.PercentLoaded() == 100){
                        console.log(Date.parse(new Date()) - time);
                        self.proxy.login(self._sip,self._sport,uid,encpass,roomid,'proxyCallBack');
                        clearInterval(interval);
                    }
                }catch(e){}
            },1000);
        },
        sendMess: function(mess){
            var self = this;
            var mess = mess.replace(/[\n\r]/g,"");
            var m = '{"t":"100","mid":"1001","msg":"'+mess+'"}';

            console.log(m);
            self.proxy.sendMessage(m);
            //清除消息
            $("textarea.lr_incon").val('');
        },
        // 回调函数，用于处理返回信息
        callBack:function(a,b){
            var self = Room.chat;
            switch(a){
                case'result':
                    switch(b){
                        case"login.success":
                            var vc = $('span.viewer_count').text().replace('人在观看','');
                            vc = parseInt(vc) + 1;
                            $('span.viewer_count').html(vc + '人在观看');

                            console.log('logined ok');
                            console.log(Date.parse(new Date()) - time);
                            gb_enter($ROOM.anchorUserID);

                            break;
                        case"login.failed":
                            console.log('login failed');
                            break;
                        case"sendmessage.failed":
                            console.log('sendMessage failed');
                            break;
                        case"send.failed":
                            console.log('发送消息失败');
                            break;
                    }
                    break;
                case'receivemessage':
                    console.log(a);
                    console.log(b);
                    self.parseMess(b);//判断消息类型，并做相应处理
                    break;
            }
        },
        parseMess:function(mess){
            var self = this;
            var m = eval('(' + mess + ")");
            switch(m.t){
                // 用户进入房间 欢迎信息
                case 501:
                    self.showWelcome(m);
                    break;
                //房间用户发言信息
                case 502:
                    self.showMes(m);
                    break;
                // 房间点赞消息
                case 503:
                    self.upmes(m);
                    break;
                case 504:
                    self.sendGiftMsg(m);
                    break;
                case 535:
                    self.sendTheAir(m);
                    break;
                // 直播开始
                case 601:
                    initPlayer('rtmpplayer',$ROOM.anchorUserID);
                    self.liveStart();
                    break;
                // 直播结束
                case 602:
                    self.liveEnd();
                    break;
                // 消息发送成功
                case 1100:
                    $("textarea.lr_incon").val('');
                    break;
            }
            Room.scroll();
        },
        showWelcome: function(m){
            //roomgrouplist
            if(roomgrouplist.indexOf(m.uid) == -1){
                roomgrouplist[m.uid] = [];
                roomgrouplist[m.uid]['roomgroup'] = m.group;
            }

            var c = $('.lr_chat_ul');
            var a = "<li>欢迎 <span class='lr_userwords noticewords'>" + m.nn + "</span></li>";
            c.append(a);
        },
        showMes: function(d){
            //检查是否再屏蔽列表中
            if(user_black_list.indexOf(d.cuid) > -1){
                return false;
            }
            // console.log('show message');
            if(roomgrouplist.indexOf(d.cuid) == -1){
                roomgrouplist[d.cuid] = [];
                roomgrouplist[d.cuid]['roomgroup'] = d.group;
            }

            var c = $('.lr_chat_ul');
            var chatMsg = '<li>' +
                '<span class="lr_username alertWarn" data-uid="'+ d.cuid +'" data-tm="'+ d.tm +'">' + d.cunn + '</span>' +
                '<span class="lr_userwords lr_other_userwords" data-msgid="'+ d.msgid +'"></span>' +
                '<div class="clear"></div>'+
                '</li>';
            c.append(chatMsg);
            var li = c.find('li:last');
            li.find("span.lr_username").text(d.cunn);
            li.find('span.lr_other_userwords').html(replace_em(d.msg));

            if(thisMovie('rtmpplayer').JSONtoString) {

                console.log(JSON.stringify({msg:''+ d.msg}));
                thisMovie('rtmpplayer').JSONtoString(JSON.stringify({msg:''+d.msg}));
            }
            //damoo.emit({ text: d.msg, color: "#f49" });
        },
        upmes:function(d){
            var c = $(".lr_chat_ul");
            var chatMsg = '<li>' +
                '<span class="lr_username alertWarn" data-uid="'+ d.ouid +'"data-tm="'+ d.tm +'">' + d.ounn + '</span>' +
                '<span class="lr_userwords lr_other_userwords"style="color:#ff5959;padding-left:0px;">' + "赞了一下" + '</span>' +
                '<div class="clear"></div>'+
                '</li>';
            c.append(chatMsg);
        },
        liveStart: function(){
            var c = $(".lr_chat_ul");
            var a = "<li class='firstRemend'><span class='noticeName'></span><span class='lr_userwords noticewords'></span></li>";
            c.append(a);

            var li = c.find('li:last');

            li.find('span.noticeName').text("［房间公告］");
            li.find('span.lr_userwords').text("直播开始");
        },
        liveEnd:function(){
            var c = $(".lr_chat_ul");
            var a = "<li class='firstRemend'><span class='noticeName'></span><span class='lr_userwords noticewords'></span></li>";
            c.append(a);

            var li = c.find('li:last');

            li.find('span.noticeName').text('［房间公告］');
            li.find('span.lr_userwords').text('直播结束');
        },
        sendGiftMsg:function(d){

            var img = {
                31:'static/img/gift/hpbean.png',
                32:'http://staticlive.douyutv.com/upload/dygift/0dc60e11063a7dd81b9f2bb213d0cfeb.png',
                33:'static/img/gift/diamond.png',
                34:'static/img/gift/motorcycle.png',
                35:'http://staticlive.douyutv.com/upload/dygift/447b61f6c0d6890d4490a90d0bdbf8bc.png'
            }

            if(!global_gift[d.ouid]){
                global_gift[d.ouid] = {};
            }
            if(!global_gift[d.ouid][d.gid]){
                global_gift[d.ouid][d.gid] = {};
            }
            if(!global_gift[d.ouid][d.gid].num){
                global_gift[d.ouid][d.gid].num = d.gnum;
            }else{
                global_gift[d.ouid][d.gid].num += d.gnum;
            }
            console.log(global_gift);

            var c = $(".lr_chat_ul");
            var chatMsg = '<li>' +
                '<span class="lr_username alertWarn" data-uid="'+ d.ouid +'" data-tm="'+ d.tm +'">' + d.ounn + '</span>' +
                '<span class="lr_userwords lr_other_userwords"style="color:#ff5959;padding-left:0px;">' + "送了"+ d.gnum + '个' + d.gnm + '<img src="'+img[d.gid]+'" style="border-raidus:4px;margin-bottom:-4px;height:20px;" border="0"></span>' +
                '<div class="clear"></div>'+
                '</li>';
            c.append(chatMsg);
            Room.gift.giftBetterEffect(global_gift[d.ouid][d.gid].num, d.gid, d.gnm, d.ouid, d.ounn)
        },
        sendTheAir:function(d){
            var date = new Date();
            var node_id = 'air_'+ d.uid+'_'+ date.getTime() +''+parseInt( Math.random() * 10000 );
            var html = '<a id="'+node_id+'" href="http://dev.huanpeng.com/main/liveRoom2.php?luid='+ d.luid+'" class="air"><div> <div class="air_gif"> <img src="static/img/gift/air_fly.gif" alt=""/> </div> <div class="title_bg">'+ d.unick+'送给'+ d.lunick+' 一架飞机</div> </div></a>';
            $('.aircraft_track').append(html);

            $('#'+node_id).css({'width':$('#'+node_id).width(),'right':'-'+$('#'+node_id).width()});
            $('#'+node_id).animate({right:'100%'},15000,function(){
                $('#'+node_id).remove();
            });
        }
    }
    //房间礼物模块
    Room.gift = {
        initSendGift:function(){
            $('.liveRoom_opt .giftdiv .gift li .lw_item').bind('click', function(){

                if(gift_switch !== 1)
                    return false;

                var giftid = $(this).parent().data('giftid');
                var gifttype = $(this).parent().data('gifttype');
                if(gifttype == 2){
                    if(!check_login()){
                        return false;
                    }
                    var data = {
                        gtype:gifttype,
                        giftid:giftid,
                        num:1
                    }
                    Gift.send_gift(data);
                }
            });
            $('.liveRoom_opt .giftdiv .gift li.gift_item_1 .gift_item_hover .numSetBtnGroup span').bind('click', function(){
                if(!check_login()){
                    return false;
                }
                if(gift_switch !== 1){
                    return false;
                }
                var giftid = $(this).parents('.item_onhover').data('giftid');
                var gifttype = $(this).parents('.item_onhover').data('gifttype');
                var num = parseInt($(this).text()) || 0;
                if(gifttype == 1){
                    var data = {
                        giftid:giftid,
                        num:num
                    }
                    Gift.send_bean(data);
                }
            });
        },
        init:function(){
            this.initSendGift();
        },
        giftBetterEffect:function(num, giftid, giftName, uid, nick){
            console.log('num' + num + 'giftID' + giftid + 'giftName:'+giftName +'uid:'+ uid + 'nick:' + nick);
            if(global_gift[uid][giftid].interval){
                clearInterval(global_gift[uid][giftid].interval);
            }
            var img = {
                31:'static/img/gift/better/bean.png',
                32:'static/img/gift/like.gif',
                33:'static/img/gift/diamond.gif',
                34:'static/img/gift/motor.gif',
                35:'static/img/gift/air.gif'
            }

            var giftBetterEffectHtml = function(giftid, giftName, uid, nick){
                var htmlstr = '';
                var eClass = 'item_'+parseInt(giftid%30);
                htmlstr = '<div id="gift-node-'+uid+''+giftid+'" class="giftbetter-item '+eClass+'"><div class="item-back"> <div class="item-name">'+nick+'</div> <div class="item-gift clear"> <span class="fl">送出</span> <span class="fr">欢豆</span> </div><div class="item-num"><span class="n x"></span></div></div> <div class="item-head"> <img src="'+img[giftid]+'" alt=""/> </div> </div>';

                return htmlstr;
            }
            var updateGiftNum = function(id,num){
                num = num.toString();
                var htmlstr = '<span class="n x"></span>';
                for(var i=0; i<num.length; i++ ){
                    htmlstr += '<span class="n n'+num[i]+'"></span>';
                }
                console.log($('#'+id).find('.nbox'));
                $('#'+id).find('.item-num').html(htmlstr);
            }

            var id = "gift-node-" + uid + '' +giftid;
            if($('#'+id).get()[0]){
                updateGiftNum(id, num);
                if(!$('#'+id).hasClass('giftbatter-item-left')){
                    $('#'+id).animate({'left':'8px'},300,function(){
                        $('#'+id).addClass('giftbatter-item-left');
                    });
                }else{

                }
            }else{
                $('#right_block_div').append(giftBetterEffectHtml(giftid, giftName, uid, nick));
                updateGiftNum(id, num);
                $('#'+id).animate({'left':'8px'},300,function(){
                    $('#'+id).addClass('giftbatter-item-left');
                });
            }

            var interval = setInterval(function(){
                $('#'+id).animate({'left':'332px'}, 300, function(){
                    $('#'+id).remove();
                    clearInterval(interval);
                    global_gift[uid][giftid].interval = false;
                });
            }, 100000);
            global_gift[uid][giftid].interval = interval;
        }
    }
    return {
        init:function(){
            Room.init();

            Room.gift.init();
        },

        chatLogin: function(){
            proxyCallBack = Room.chat.callBack;
            //if(_isLogin);
            Room.chat.login();
        }
    }

}();
var proxyCallBack;
room.chatLogin()

var time = Date.parse(new Date());