$(function () {
    var hp_head = {};
    var _isAnchor = $head.content.list.isAnchor;
    var _isLogin = $head.content.list.LoginStatus;
    hp_head = {
        init: function () {
            hp_head._initDomain();
            hp_head.initPerson();
            hp_head.initAnchor();
            hp_head.initGameList();
            hp_head.initFollowList();
            hp_head.initHistoryList();
            hp_head.initOthers();
        },
        barHtml: {
            gameList_tpl: function (d) {
                var tpl = "<li><a href='GameZone.php?gid=" + d.gameID + "' class='game_name'>" + d.gameName + "</a></li>";
                return tpl;
            },
            //history 中 有isLiving判断
            historyList_tpl: function (d) {
                if (d.isLiving == 1) {
                    var tpl = "<li><a href='./" + d.roomID + "' class=''><div class='pic'><img src='" + d.head + "' width='32' height='32'/>"
                        + "</div><div class='detail l'><div class='detail_t of'><i class='icon_liveroom'></i><span class='liveroom_text'>" + d.nick + "</span>"
                        + "</div><div class='detail_b'><i class='icon_his'></i><span class='liveroom_text2'>" + calVisitTime(d.stime) + "</span>"
                        + "</div></div></a></li>";
                    return tpl;
                } else if (d.isLiving == 0) {
                    //不在线,更换图标
                    var tpl = "<li><a href='./" + d.roomID + "' class=''><div class='pic'><img src='" + d.head + "' width='32' height='32'/>"
                        + "</div><div class='detail l'><div class='detail_t of'><i class='icon_unliveroom'></i><span class='liveroom_text'>" + d.nick + "</span>"
                        + "</div><div class='detail_b'><i class='icon_his'></i><span class='liveroom_text2'>" + calVisitTime(d.stime) + "</span>"
                        + "</div></div></a></li>";
                    return tpl;
                }

            },
            //followList 关注时间
            followList_tpl: function (d) {
                if(d.isLiving == 1){
                    var tpl = "<li><a href='./" + d.roomID + "' class=''><div class='pic'><img src='" + d.head + "' width='32' height='32'/>"
                        + "</div><div class='detail name'><div class='detail_t of'><span class='focus_text'>" + d.nick + "</span>"
                        + "</div><div class='detail_b of' style='margin-top: -5px;'><span class='focusroom_text2'>" + initPlaytimeHtml(d.stime) + "</span>"
                        + "<div class='num_sbox of'><i class='icon_live_num'></i><span class='live_numtext'>" + d.viewCount + "</span></div></div></div></a></li>";
                    return tpl;
                }else{
                    var tpl = "<li><a href='./" + d.roomID + "' class=''><div class='pic'><img src='" + d.head + "' width='32' height='32'/>"
                        + "</div><div class='detail name'><div class='detail_t of'><span class='focus_text'>" + d.nick + "</span>"
                        + "</div><div class='detail_b of' style='margin-top: -5px;'><span class='focusroom_text2'>暂未直播</span>"
                        + "<div class='num_sbox of'><i class='icon_live_num'></i><span class='live_numtext'>" + d.viewCount + "</span></div></div></div></a></li>";
                    return tpl;
                }

            }
        },
        _initDomain : function () {
            // if (navigator.userAgent.toLowerCase().match(/(iphone|ipod|android|ios|ipad|ucweb)/i)){
            //     location.href = $conf.domain+'mobile/';
            // }
        },
        initPerson: function () {
            if (_isLogin) {
                loginEvent();
            } else {
                nologinEvent();
            }
            function loginEvent() {
                var f = $('#userinfo');
                $('#userinfo>a').attr('href',$conf.person);
                var userInfo = $head.content.list.userInfo;
                var userDrop_box = f.find('.presonBox_wz');
                $('.no-loginBtnBox').hide();

                f.find('#user_face').attr('src', userInfo.head);

                f.show();

                f.mouseenter(function () {
                    var requestUrl = $conf.api + 'user/info/getUserDetail.php';
                    var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc')};
                    ajaxRequest({url:requestUrl,data:requestData},function (responseData) {

                        userDrop_box.find('.person_img>img').attr('src', responseData.head);
                        userDrop_box.find('.person_name').text(responseData.nick);
                        userDrop_box.find('.hpb_num').text(numberFormat(parseInt(responseData.hpcoin), 1));
                        userDrop_box.find('.hpd_num').text(numberFormat(parseInt(responseData.hpbean), 1));

                        var needRate = (responseData.integral / responseData.levelIntegral) * 100 + '%';

                        userDrop_box.find('.levelIntegral').text(digitsFormat(responseData.gapIntegral));
                        userDrop_box.find('#levelBar').width(needRate);
                        userDrop_box.find('.unreadMsg').text(responseData.unreadMsg);

                        f.find('.presonBox_wz').show();
                    },function (responseData) {
                        console.error(responseData);
                    });

                });

            }

            function nologinEvent() {
                $('#userinfo').hide();
                $('.no-loginBtnBox').css('display', 'inline-block');
            }

        },
        initAnchor: function () {
            if (_isAnchor == 1) {
                $('.live_btn .r_btn_sm').text('发直播');

                //href 发直播链接
                $('.nav_rbtn .live_btn').attr('href', $conf.domain + 'room.php?luid=' + $head.content.list.userInfo.uid + '&to_open_live=1');
            } else {
                $('.live_btn .r_btn_sm').text('做主播');
                //主播认证 链接
                $('.nav_rbtn .live_btn').attr('href', $conf.person + 'beanchor.php');
            }
        },
        initGameList: function () {
            var f = $('.nav_l .game_list');
            var htmlStr = [];
            var list = $head.content.list.gameList;
            for (var i in list) {
                htmlStr.push(hp_head.barHtml.gameList_tpl(list[i]));
            }
            f.html(htmlStr);
        },
        initHistoryList: function () {
            var f = $('.nav_rbtn #history_btn');
            f.mouseenter(function () {
                if (_isLogin == 1) {
                    loginEvent();
                } else {
                    nologinEvent();
                }
                function loginEvent() {
                    var historyContent = f.find('.historyBox');
                    var htmlStr = [];
                    var loading = f.find('.loading-box');
                    loading.show();

                    var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),size:5};
                    var requestUrl = $conf.api + 'room/historyList.php';
                    ajaxRequest({data:requestData,url:requestUrl},function (responseData) {
                        var list = responseData.list;
                        if(list.length != 0){
                            for (var i in list) {
                                htmlStr.push(hp_head.barHtml.historyList_tpl(list[i]));
                            }
                        }else{
                            htmlStr = '<div class="no-lookBox" style="display: block;"><div class="no-look"><img src="./static/img/logo/home_no_login.png">' +
                                '<p class="no-lookText">您还没有观看过任何直播</p></div></div>';
                        }
                        historyContent.html(htmlStr);
                        loading.hide();
                        historyContent.show();
                    },function (responseData) {
                        htmlStr = '<div class="no-lookBox" style="display: block;"><div class="no-look"><img src="./static/img/logo/home_no_login.png">' +
                            '<p class="no-lookText">您还没有观看过任何直播</p></div></div>';
                        historyContent.html(htmlStr);
                        loading.hide();
                        historyContent.show();
                    })
                }

                function nologinEvent() {
                    $('#history-login').show();
                }
            });
        },
        initFollowList: function () {
            var f = $('.nav_rbtn #follow_btn');
            f.mouseenter(function () {
                if (_isLogin == 1) {
                    loginEvent();
                } else {
                    nologinEvent();
                }
                function loginEvent() {
                    var followContent = f.find('.focusBox');
                    var htmlStr = [];
                    var loading = f.find('.loading-box');
                    loading.show();

                    var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),size:3};
                    var requestUrl = $conf.api + 'room/followList.php';
                    ajaxRequest({data:requestData,url:requestUrl},function (responseData) {
                        loading.hide();
                        var list = responseData.list;
                        if (!list || list.length == 0) {
                            noFollowOne();
                        } else {
                            f.find('.livecount span').text(responseData.liveTotal);
                            for (var i in list) {
                                htmlStr.push(hp_head.barHtml.followList_tpl(list[i]));
                            }
                            followContent.html(htmlStr);
                            f.find('.no-focusBox').hide();
                            f.find('.follow_box').show();
                        }
                    },function(responseData){
                        loading.hide();
                        noFollowOne();
                    })
                }

                function nologinEvent() {
                    f.find('#follow-login').show();
                }

                function noFollowOne() {
                    f.find('.no-focusBox').show();
                }
            });
        },
        initOthers: function () {
            lazyLoad.init();
            $(".xl,.r_btnbox,.user_pic").hover(function () {
                $(this).children(".drop_menu").show();
                $(this).children(".drop_menu").css("display", "block");
            }, function () {
                $(this).children(".drop_menu").hide();
                $(this).children(".drop_menu").css("display", "none");
            });

            //登录
            $('.login_hp').click(function (e) {
                e.preventDefault();
                //				        LoginTPL();
                //				        $('.tabmodal_sel:eq(0)').click();
                loginFast.login(0);
            });
            //注册
            $('.reg_hp').click(function (e) {
                e.preventDefault();
                //				        LoginTPL();
                //				        $('.tabmodal_sel:eq(1)').click();
                loginFast.login(1);
            });
            //搜索focus
            $('#search-hp').focus(function () {
                $('.searchBox,.ser_btn,#search-hp').addClass('focus');
            }).blur(function () {
                $('.searchBox,.ser_btn,#search-hp').removeClass('focus');
            }).on('keypress', function (e) {
                if (e.keyCode == 13) {
                    if ($('#search-hp').val() == '') {
                        tips('请输入搜索内容哦~亲');
                        return;
                    } else {
                        location.href = $conf.domain + 'search.php?key=' + $('#search-hp').val();
                    }
                }
            });
            //搜索点击
            $('#search_btn').click(function (e) {
                e.preventDefault();
                if ($('#search-hp').val() == '') {
                    tips('请输入搜索内容哦~亲');
                    return;
                } else {
                    location.href = $conf.domain + 'search.php?key=' + $('#search-hp').val();
                }
            });

            //退出
            $('#delete_User').click(function () {
                logout_submit();
            });


            $(window).resize(function () {
                _Width_resize = $(window).width();
                if (_Width_resize >= 1180) {
                    $('.nav_r').removeClass('fl_980');
                } else {
                    $('.nav_r').addClass('fl_980');
                }
            });

            //头部右侧鼠标经过变色
            $(".icon-color").hover(function () {
                $(this).next().addClass("fc_orange");
            }, function () {
                $(this).next().removeClass("fc_orange");
            });

            //head部分搜索框点击清空value
            $("#search-hp").click(function () {
                $("#search-hp").val("");
            });

        }
    };
    hp_head.init();
});
