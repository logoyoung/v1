var Sidebar;
(function(){
    var _isLogin = pageUser.isLogin;
    var a = jQuery;
    var $conf = conf.getConf();
    Sidebar = {
        barHtml:{
            personalHtml:function(s,u){
                var htmlstr = ''
                if(s) {
                    var finished = parseInt(u.integral) / parseInt(u.levelIntegral) * 100;
                    var c = u.levelIntegral - u.integral;

                    htmlstr += '<a href=""> <span class="personal_face"><img src="'+ u.pic +'"></span> </a>';
                    htmlstr += '<div id="personal_info">';
                    htmlstr += '<div class="p_detail">';
                    htmlstr += '<div class="p_face">';
                    htmlstr += '<img src="'+ u.pic+'">';
                    htmlstr += '</div>';
                    htmlstr += '<div class="p_info">';
                    htmlstr +='<p>'+ u.nickName+'</p>';
                    htmlstr +='<div class="clear"></div>';
                    htmlstr +='<span class="anchor_icon hpcoin"></span>';
                    htmlstr +='<span class="count">'+ u.hpcoin+'</span>';
                    htmlstr +='<span class="anchor_icon hpbean"></span>';
                    htmlstr +='<span class="count">'+ u.hpbean+'</span>';
                    htmlstr +='</div>';
                    htmlstr +='<a href="'+$conf.domain +'personal/recharge.php'+'" id="recharge">充值</a>';
                    htmlstr +='</div>';
                    htmlstr +='<div class="p_level">';
                    htmlstr +='<span class="level"></span>';
                    htmlstr +='<span class="lupIcon"><div class="arrow_up"></div><div class="line red"></div><div class="line red"></div><div class="line white"></div><div class="line red"></div><div class="line white"></div><div class="line red"></div></span>';
                    htmlstr +='<span class="levelBarSpan"><strong id="levelBar" style="width:'+finished+'%"></strong></span>';
                    htmlstr +='<div class="clear"></div>';
                    htmlstr +='<span class="levelup">距离升级还有<a>'+ c +'</a>欢豆</span>';
                    htmlstr +='</div>';
                    htmlstr +='<div class="p_msg">我的新消息：'+ u.readsign+' 条</div>'
                    htmlstr +='<div class="p_option">';
                    htmlstr +='<a href="'+$conf.domain + 'personal/mp/' +'" class="pCenter">个人中心</a>';
                    htmlstr +='<div class="lineheight"></div>';
                    htmlstr += '<a>退出</a>';
                    htmlstr +='</div>';
                    htmlstr +='</div>';
                    return htmlstr;
                }else{
                    htmlstr += '<a href=""> <span style="height: 40px;"><img src="static/img/userface_notlogin.png"></span>登录</a>';
                    return htmlstr;
                }
            },
            liveHtml:function(){

                return ''
            },
            gmListHtml:function(){
                return '';
            },
            followHtml:function(){
                var htmlstr = '<a class="btns"><span class="anchor_icon followIcon"></span>关注</a>';
                if(_isLogin)
                    htmlstr = '<a href="'+$conf.domain +'personal/follow/index.php" class="btns"><span class="anchor_icon followIcon"></span>关注</a><div id="followList"><div class="followList_detail"></div><a href="'+$conf.domain + 'personal/follow/'+'" class="viewall">查看全部</a></div> ';

                return htmlstr;
            },
            historyListHtml:function(s,h){
                var htmlstr = '<a class="btns"><span class="anchor_icon historyIcon"></span>历史</a>';
                if(_isLogin)
                    htmlstr += '<div id="historyList"><div class="historyList_detail"></div></div> ';//<a href="'+$conf.domain + 'personal/follow/index.php?tab=history'+'" class="viewall">查看全部</a>

                return htmlstr;
            }
        },
        init:function(){
            //this.initPerson();
            this.initFollowList();
            this.initHistoryList();
            this.initGameList();
            this.initAnchor();
        },
        initPerson:function(){
            var i = 0;
            var self = this;
            var u =  _isLogin ? pageUser.user : false;
            var p = a('.sidebar .personal');
            (function initPersonalInfo(u){
                p.html(self.barHtml.personalHtml(_isLogin,u));
                if(_isLogin) {
                    var logoutBtn =
                    logoutBtn.bind('click',logout_submit);
                    p.hover(loginEvent, function () {
                        return false;
                    });
                }else {
                    //$('.sidebar .personal  img').attr('src', 'static/img/userface_notlogin.png');
                    p.bind('click', unLoginEvent);
                }
                function loginEvent(){
                    var uid = getCookie('_uid');
                    var enc = getCookie('_enc');
                    a.ajax({
                        url:'http://' + document.domain + '/main/a/shamApi_accessPersonalInfo.php',
                        type:'post',
                        dataType:'json',
                        data:{
                            targetUserID:uid,
                            uid:uid,
                            encpass:enc
                        },
                        success:function(d){
                            changePersonalInfo(d);
                        }
                    });

                    function changePersonalInfo(d){
                        var pInfo = p.find("#personal_info");
                        //主要更新用余额信息 等级信息
                        pInfo.find('.p_info p').text(d.nick);
                        pInfo.find('.p_info .count:eq(0)').text(d.hpcoin);
                        pInfo.find('.p_info .count:eq(1)').text(d.hpbean);
                        //pInfo.find('.p_level .level').text('Lv.' + d.level);
                        pInfo.find('.p_level .level').addClass('anchor_icon').addClass('level'+ d.level);

                        var bar = parseInt(d.integral) / parseInt(d.levelIntegral) * 100;
                        var toLevelUp = d.levelIntegral - d.integral;

                        pInfo.find('.p_level .levelBarSpan').html('<strong id="levelBar" style="width:'+bar+'%"></strong>');
                        pInfo.find('.p_level .levelup a').text(toLevelUp);
                        pInfo.find('.p_msg').text('我的新消息:' + d.readsign + '条');
                    }
                }
                function unLoginEvent(e){
                    e.preventDefault();
                    console.log('unlogin' + i++);
                    loginFast.login(0);
                }
            }(u));
        },
        initFollowList:function(){
            var self = this;
            var f = a('.sidebar .followList');
            f.html(self.barHtml.followHtml());
            (function initFollowListInfo(){
                f.find('.btns').bind('click',function(e){

                    if(_isLogin) {
                        //location.href = "http://" + document.domain + 'xxx.php';
                        return false;
                    }
                    else
                        loginFast.login(0);
                });
                if(_isLogin)
                    f.hover(loginEvent,function(){return false;});
                function loginEvent(){
                    var uid = getCookie('_uid');
                    var enc = getCookie('_enc');
                    a.ajax({
                        url:'http://' + document.domain + '/main/a/followList.php',
                        type:'post',
                        dataType:'json',
                        data:{
                            uid:uid,
                            encpass:enc,
                            size:3
                        },
                        success:function(d){
                            function initPlaytimeHtml(time){
                                var t = calTime(time);
                                var str = [
                                    '年',
                                    '个月',
                                    '天',
                                    '小时',
                                    '分钟',
                                    '秒'
                                ];
                                for(var i in t)
                                    if(t[i])
                                        return "已播<em class='time'>"+t[i]+"</em>"+str[i];
                            }
                            console.log(d);
                            var flist = d.followList;
                            var htmlstr = '';
                            for(var i in flist){
                                var href = "http://" + document.domain+ '/main/room.php?luid=' +flist[i].anchorUserID;
                                htmlstr += '<a href="'+href+'">';
                                htmlstr += '<div class="followOne">';
                                htmlstr += '    <div class="f_face">';
                                htmlstr += '       <img src="'+flist[i].anchorPicURL+'">';
                                htmlstr += '    </div>';
                                htmlstr += '    <div class="f_info">'
                                htmlstr += '        <p>'+ flist[i].anchorNickName+'</p>';
                                htmlstr += '        <div class="clear"></div>';
                                //htmlstr += '        <span class="anchor_icon"></span>';
                                htmlstr += flist[i]['liveStatus'] == '100'? '<span class="playtime">'+initPlaytimeHtml(flist[i].liveStartTime)+'</span>' : '<span class="playtime">暂未直播</span>';
                                htmlstr += "<div class='right'>";
                                htmlstr += '        <span class="anchor_icon viewerIcon2"></span>';
                                htmlstr += '        <span class="viewercount">'+flist[i].viewerCount+'</span>';
                                htmlstr += "</div>";
                                htmlstr += '    </div>';
                                htmlstr += ' </div>';
                                htmlstr += '</a>';

                            }
                            if(htmlstr == ''){
                                htmlstr = '<div class="followOne no-data"><span class="no-data-logo"><img src="/static/img/logo/home_no_login.png" alt=""/></span><span class="no-data-title">您还没有任何关注哦</span></div>';
                                $('#followList .viewall').css('display', 'none');
                            }else{
                                $('#followList .viewall').css('display', 'block');
                            }

                            a("#followList .followList_detail").html(htmlstr);
                            a('#followList .followOne:last').css('border-bottom', '0');
                        }
                    });
                }
            }());
        },
        initHistoryList:function(){
            var self = this;
            var h = a('.sidebar .historyList');
            (function(){
                h.html(self.barHtml.historyListHtml());
                h.find('.btns').bind('click', function(){
                    if(_isLogin){
                        //location.href = '';

                    } else
                        loginFast.login(0);
                });

                if(_isLogin)
                    h.hover(loginEvent,function(){return false;});


                function loginEvent(){
                    var uid = getCookie('_uid');
                    var enc = getCookie('_enc');
                    a.ajax({
                        url:"http://" + document.domain + '/main/a/historyList.php',
                        type:'post',
                        dataType:'json',
                        data:{
                            uid:uid,
                            encpass:enc,
                            size:5
                        },
                        success:function(d){
                            console.log(d);
                            var hlist = d.historyList;
                            var htmlstr = '';

                            for(var i in hlist){
                                var href = 'http://' + document.domain + '/main/room.php?luid=' + hlist[i].anchorUserID;
                                htmlstr += '<a href="'+href+'">';
                                htmlstr += '<div class="historyOne">';
                                htmlstr += '<div class="h_face">';
                                htmlstr += '<img src="'+hlist[i].anchorPicURL+'">';
                                htmlstr += '</div>';
                                htmlstr += '<div class="h_info">'
                                htmlstr += hlist[i]['liveStatus'] == '100' ? '<span class="anchor_icon playingIcon">' : '<span class="anchor_icon unplayIcon ">';
                                //htmlstr += '<div class="arrow_right"></div>';
                                htmlstr += '</span>';
                                htmlstr += '<p>'+ hlist[i].anchorNickName+ '</p>';
                                htmlstr += '<div class="clear"></div>';
                                htmlstr += '<span class="anchor_icon viewTimeIcon"></span>';
                                htmlstr += '<span class="playtime">'+ calVisitTime(hlist[i].scanTime)+'</span>';
                                htmlstr += '</div>';
                                htmlstr += '</div>';
                                htmlstr += '</a>';
                                //console.log(htmlstr);
                            }
                            htmlstr = htmlstr != '' ? htmlstr : '<div class="historyOne no-data"><span class="no-data-logo"><img src="/static/img/logo/home_no_login.png" alt=""/></span><span class="no-data-title">没有浏览记录~</span></div>';;
                            a("#historyList .historyList_detail").html(htmlstr);
                            a('#historyList .historyOne:last').css('border-bottom', '0');
                        },
                        load:function(){
                            loadAnimate.selector('historyList').showLoad();
                        }
                    });
                }
            }());
        },
        initGameList:function(){
            $.ajax({
                url:'http://dev.huanpeng.com/main/a/gameList.php',
                type:'post',
                dataType:'json',
                data:{
                    size:12
                },
                success:function(d){
                    var list = d.gameList;
                    var htmlStr = '';
                    for(var i in list){
                        htmlStr +='<a href="GameZone.php?gid="'+list[i].gameid+'><span class="gameone">'+list[i].gamename+'</span></a>';
                    }
                    //console.log(htmlStr);
                    $('#gameList .gameList_detail').html(htmlStr);
                }
            });
        },
        initAnchor:function(){
            var url,text, eClass, data;
            eClass = "beAnchorIcon";
            if(pageUser.isAnchor){
                url = $conf.domain + 'room.php?luid='+pageUser.user.userID
                text = "去直播";
                if($ROOM.anchorUserID == pageUser.user.userID){
                    url = "javascript:;";
                    //if($ROOM.isLiving){
                    //    eClass = "closeLiveIcon";
                    //    text = "关直播";
                    //}else{
                    //    eClass = "pushLiveIcon";
                    //    text = "发直播";
                    //}
                    eClass = "pushLiveIcon"
                    text = '发直播';
                    data = 'pushLive';
                }
            }else{
                url = $conf.person + 'beanchor.php';
                text = '做主播';
                data = 'beanchor';
            }

            $('.sidebar_list.apply_anchor').html('<a href="'+url+'"><span class="anchor_icon '+eClass+'"></span>'+text+'</a>');
            $('.sidebar_list.apply_anchor a').data('liveStatus', data);

            //$('.sidebar_list.apply_anchor a').bind('click', function(){
            //    if($(this).find('.anchor_icon').hasClass('closeLiveIcon')){
            //
            //    }else if($(this).find('.anchor_icon').hasClass('pushLiveIcon')){
            //
            //    }
            //});
        }
    }
}());