/**
 * Created by junxiao on 2017/4/27.
 */
console.log('the page start in time ' + new Date().getTime());

!function (a) {
    var b = {
        init: function (d, c) {
            return !function () {
                d.css({
                    'background-image': "url(" + c.imgUrl + ")",
                    'width': c.width,
                    'height': c.height,
                    'background-position': -c.left + ' ' + -c.top
                });

                var position = [];
                for (var i = 0; i < c.num; i++) {
                    position[i] = {};
                    position[i].x = ((i % c.row)) * (c.width + c.marginRight) + c.left;
                    position[i].y = (parseInt((i / c.row))) * (c.height + c.marginTop) + c.top;
                }

                var j = 0;
                var timer = 1000 / c.fps;
                var interval = setInterval(function () {
                    d.css('background-position', -position[j].x + 'px ' + -position[j].y + "px");
                    j++;
                    j = j == c.num ? 0 : j;
                }, timer);
            }();
        }
    };
    a.fn.toGif = function (d) {
        var c = a.extend({
            row: 2,
            left: 0,
            top: 0,
            marginRight: 0,
            marginTop: 0,
            num: 2,
            width: 80,
            height: 80,
            imgUrl: 'ab.png',
            fps: 8
        }, d);
        b.init(this, c);
    }
}(jQuery);

var Sidebar = {};
!function () {
    var _isLogin = pageUser.isLogin;
    var a = jQuery;
    var $conf = conf.getConf();
    Sidebar = {

        init: function () {
            Sidebar.initGameList();
            Sidebar.initFollowList();
            Sidebar.initHistoryList();
            Sidebar.initAnchor();
        },
        barHtml: {
            liveHtml: function () {
                return ''
            },
            GameList_tpl : function(d){
                var urlpre = $conf.domain + 'GameZone.php?gid=';
                var tpl = '<a href="'+urlpre+d.gameID+'">\
                    <span class="gameone">'+d.gameName+'</span>\
                    </a>';
                return tpl;
            },
            FollowList_tpl : function (d) {
                if(d.isLiving == 1){
                    var tpl = '<a href="../'+d.roomID+'">\
                        <div class="follow-fig">\
                        <div class="f-face">\
                        <img src="'+d.head+'">\
                        </div>\
                        <div class="f-info">\
                        <p>'+d.nick+'</p>\
                        <div class="clear"></div>\
                        <span class="playtime">'+initPlaytimeHtml(d.stime)+'</span>\
                        <div class="pNum">\
                        <span class="anchor_icon viewerIcon2 block"></span>\
                        <span class="viewCount block">'+d.viewCount+'</span>\
                        </div>\
                        </div>\
                        </div>\
                        </a>';
                    return tpl;
                }else{
                    var tpl = '<a href="../'+d.roomID+'">\
                        <div class="follow-fig">\
                        <div class="f-face">\
                        <img src="'+d.head+'">\
                        </div>\
                        <div class="f-info">\
                        <p>'+d.nick+'</p>\
                        <div class="clear"></div>\
                        <span class="playtime">暂未直播</span>\
                        <div class="pNum">\
                        <span class="anchor_icon viewerIcon2 block"></span>\
                        <span class="viewCount block">'+d.viewCount+'</span>\
                        </div>\
                        </div>\
                        </div>\
                        </a>';
                    return tpl;
                }

            },
            HistoryList_tpl : function (d) {
                if(d.isLiving == 1){
                    var tpl = '<a href="./'+d.roomID+'">\
                        <dic class="history-fig">\
                        <div class="h-face">\
                        <img src="'+d.head+'">\
                        </div>\
                        <div class="h-info">\
                        <span class="anchor_icon playIcon"></span>\
                        <p>'+d.nick+'</p>\
                        <div class="clear"></div>\
                        <span class="anchor_icon viewTimeIcon"></span>\
                        <p class="playtime">'+calVisitTime(d.stime)+'</p>\
                    </div>\
                    </dic>\
                    </a>';
                    return tpl;
                }else if(d.isLiving == 0){
                    var tpl = '<a href="./'+d.roomID+'">\
                        <dic class="history-fig">\
                        <div class="h-face">\
                        <img src="'+d.head+'">\
                        </div>\
                        <div class="h-info">\
                        <span class="anchor_icon unplayIcon"></span>\
                        <p>'+d.nick+'</p>\
                        <div class="clear"></div>\
                        <span class="anchor_icon viewTimeIcon"></span>\
                        <p class="playtime">'+calVisitTime(d.stime)+'</p>\
                    </div>\
                    </dic>\
                    </a>';
                    return tpl;
                }
            }
        },
        initGameList: function () {
            var f = a('#game-list .gamelist-detail');

            var size = 12;
            var list = $head.content.list.gameList;
            var htmlStr = [];
            for(var i = 0; i < size; i ++){
                htmlStr.push(Sidebar.barHtml.GameList_tpl(list[i]));
            }
            f.html(htmlStr);
        },
        initFollowList: function () {
            var f = a('.live-left .follow-list');
            f.mouseenter(function () {
                if(_isLogin){
                    loginEvent();
                }else{
                    nologinEvent();
                }
            });
            function nologinEvent() {
                $('.follow-loading').hide();
                $('.follow-nodata').show();
            }
            function loginEvent() {
                var requestUrl = $conf.api+'room/followList.php';
                var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),size:3};
                var followContent = f.find('#follow-content');
                var followDetail = f.find('.follow-detail');
                //if(followContent.find('a').length == 0){
                if(1){
                    ajaxRequest({url:requestUrl,data:requestData},function (d) {
                        var htmlStr = [];
                        var list = d.list;

                        if(list){
                            for(var i in list){
                                htmlStr.push(Sidebar.barHtml.FollowList_tpl(list[i]));
                            }
                            $('.follow-loading').hide();
                            $('.perNum').text(d.liveTotal);
                            followContent.html(htmlStr);
                            $('.follow-list').show();
                        } else if(!d.liveTotal || !d.total){
                            var tpl = '<li class="no_login">\
                                        <div class="img_no_login">\
                                            <img src="static/img/logo/home_no_login.png">\
                                        </div>\
                                        <div class="txt_div">您还没有任何关注哦</div>\
                                    </li>';
                            f.find('.viewAll').hide();
                            $('.follow-loading').hide();
                            followDetail.html(tpl);
                            $('.follow-list').show();
                            return;
                        }
                    });
                }
            }
        },
        initHistoryList: function () {
            var f = a('.live-left .history-list');
            f.mouseenter(function () {
                if(_isLogin){
                    loginEvent();
                }else{
                    nologinEvent();
                }
            });
            function nologinEvent() {
                $('.history-loading').hide();
                $('.history-nodata').show();
            }
            function loginEvent() {
                var requestUrl = $conf.api+'room/historyList.php';
                var requestData = {uid:getCookie('_uid'),encpass:getCookie('_enc'),size:5};
                var historyContent = f.find('.hislist');
                //if(historyContent.find('a').length == 0){
                if(1){
                    ajaxRequest({url:requestUrl,data:requestData},function (d) {
                        var htmlStr = [];
                        var list = d.list;
                        for(var i in list){
                            htmlStr.push(Sidebar.barHtml.HistoryList_tpl(list[i]));
                        }
                        $('.history-loading').hide();
                        historyContent.html(htmlStr);
                        $('.hislist').show();
                    });
                }
            }
        },
        initAnchor: function () {
            var url, text, eClass, data;
            eClass = "requestIcon";
            if (pageUser.isAnchor) {
                url = $conf.domain + 'room.php?luid=' + pageUser.user.userID + '&to_open_live=1';
                text = "去直播";
                eClass = "pushLiveIcon";
                if ($ROOM.anchorUserID == pageUser.user.userID) {
                    url = $conf.domain + 'room.php?luid=' + pageUser.user.userID + '&to_open_live=1';
                    eClass = "pushLiveIcon";
                    text = '发直播';
                    data = 'pushLive';
                }
            } else {
                url = $conf.person + 'beanchor.php';
                text = '做主播';
                data = 'beanchor';
            }

            $('.live-left .request-box').html('<a  href="' + url + '"><span class="roomIcon ' + eClass + '"></span><p class="slidebar-desc">' + text + '</p></a>');
            $('.sidebar_list.apply_anchor a').data('liveStatus', data);

        }

    };
    Sidebar.init();
}(jQuery);
var taskBox = {};
!function (a) {
    $conf = conf.getConf();
    var taskStatus = {
        0: {
            0: 'task-undo',
            1: 'task-btn-do',
            2: '去完成'
        },
        1: {
            0: 'task-receive',
            1: 'task-btn-receive',
            2: '可领取'
        },
        2: {
            0: 'task-finish',
            1: 'task-btn-finish',
            2: '已完成'
        }
    };

    taskBox.modalHtml = {};
    taskBox.modalHtml.head = function () {
        return '<div class="taskModal-head"><span class="taskModal-title">新手任务</span> <a class="task-close" href="javascript:;"></a> </div>';
    }
    taskBox.modalHtml.body = function (list) {
        var status = taskStatus;

        var html = '';
        for (var i in list) {
            var d = list[i];
            html += '<div data-stat=' + d.status + ' data-taskid=' + d.id + ' class="task-one ' + status[d.status][0] + '"> <p class="task-title">' + d.title + '</p> <p class="task-reword-desc">奖励<i>' + d.hpbean + '</i>个欢朋豆</p> <button class="' + status[d.status][1] + '">' + status[d.status][2] + '</button> </div>';
        }
        return '<div class="taskModal-body">' + html + '<div class="clear"></div></div>';
    }
    taskBox.modalHtml.foot = function () {
        return '    <div class="taskModal-foot"> <p>＊所有奖励需要绑定手机后领取</p> <span class="ufo"></span> </div>';
    }

    taskBox.set_pos = function () {
        var f = a('#taskModal');
        f.css('margin-left', -f.width() / 2 + 'px');
    }

    taskBox.createModal = function (b) {
        this.remove();
        Mask.creates();
        Mask.box.css('background-color', 'rgba(0,0,0,0)');
        a('<div/>', {
            id: 'taskModal',
            'class': 'taskModal',
            'style': 'position:fixed; left:50%; top:100px; z-index:1000;',
            html: b
        }).appendTo(document.body);
        this.set_pos();
    }
    taskBox.remove = function () {
        if (!a('#taskModal')[0]) {
            return;
        }
        Mask.remove();
        a('#taskModal').remove();
    }
    var task_open_loading = 0;
    taskBox.open = function () {
        if (task_open_loading) {
            return;
        }
        task_open_loading = 1;
        var self = this;
        var requestUrl = $conf.api + 'room/getMyTaskList.php';
        //var requestUrl = './getMyTaskList.php';
        var requestData = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
        };
        ajaxRequest({url:requestUrl,data:requestData}, function (d) {
            task_open_loading = 0;
            var html = self.modalHtml.head() + self.modalHtml.body(d.list) + self.modalHtml.foot();
            self.createModal(html);
            self.initEventHandle();
        },function (responseData) {
            if(responseData.code == -5026){
                loginFast.bindingMobile();
            }
            task_open_loading = 0;
        });
    }

    taskBox.initEventHandle = function () {
        var self = this;
        a('#taskModal .task-close').bind('click', function () {
            self.remove()
        });

        a('#taskModal .task-one button').bind('click', function () {
            var stat = parseInt(a(this).parent().data('stat'));
            var taskid = a(this).parent().data('taskid');
            //console.log(taskid);
            //console.log(stat);
            if(!check_phoneStatus(pageUser.user.phonestatus))
                return;
            if (!taskid) return;
            if (stat == 0) {
                if ($conf.taskUrl[taskid]) {
                    window.open($conf.taskUrl[taskid]);
                } else {
                }
                self.remove();
            } else if (stat == 1) {
                taskBox.receive(taskid);
            }

            return;
        });
    };

    taskBox.receive = function (taskid) {
        if (!taskid) {
            return false;
        }

        var requestUrl = $conf.api + 'room/getBeanByTask.php';
        // var requestUrl = './getBeanByTask.php';
        var requestData = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
            taskID:taskid
        };
        ajaxRequest({data:requestData,url:requestUrl},function(d){
            //tips('right');
            set_user_hpbean(d.hpbean);
            setProperty(d.hpbean,d.hpcoin);
            changeStat(taskid);
            tips('领取成功');
        },function(){
            tips('领取失败');
        });

        //注意要加验证
        function changeStat(taskid) {
            $('#taskModal .task-one.task-receive').each(function (index, element) {
                if ($(element).data('taskid') == taskid) {
                    $(element).data('stat', 2);
                    $(element).removeClass(taskStatus[1][0]).addClass(taskStatus[2][0]);
                    $(element).find('button').removeClass(taskStatus[1][1]).addClass(taskStatus[2][1]).text(taskStatus[2][2]);
                }
            });
        }
    }
}(jQuery);

var anchor_is_on_living = 0;
function anchor_unload_handler(e) {
    var warning = '该操作会导致直播结束，确认退出么？'
    if (anchor_is_on_living) {
        tips(warning, false, 1);
        e = (e || window.event);
        if (e) {
            e.returnValue = warning;
        }
        return warning;
    }

}
window.onbeforeunload = anchor_unload_handler;

var allowUseObs = true;
var myDeviceList;
window.getDeviceListCallBack = function ( type, list ) {

    if( type != 'audio') {return;}

    var audioStr = '';
    for( var i in list )
    {
        audioStr += list[i];
    }

    if( allowUseObs ? false : !audioStr )
    {
        return false;
    }

    myDeviceList = {};
    myDeviceList[type] = list;
    for(var i in list){
        myDeviceList[type][i] = decodeURIComponent( list[i].replace( /\+/g, "%20" ) );
    }
    var defaultVoice = getCookie('_uAudio') ? getCookie('_uAudio') : '';
    var dom = $('.select-audio');
    var itemIndex = 0;
    dom.find('.options .myOption').remove();
    dom.find('.options').append(function () {
        var option = ''
        for (var i in myDeviceList['audio']) {
            var item = myDeviceList['audio'][i];
            defaultVoice && item == defaultVoice && (itemIndex=i)
            option += '<div class="myOption" value="' + item + '">' + item + '</div>';
        }
        return option;
    });
    var cur = 'cur';
    dom.find('.options .myOption').eq(itemIndex).addClass(cur);
    Select('.mySelect');

    pushLiveBox.pushLive.openTheClient.init();
}

var msgToClient = {};
!function () {
    var local = 'http://127.0.0.1';
    var portList = [8764, 8765, 8766, 8767, 8768, 8769, 8770, 8771, 8772, 8773];
    var file = '/heartbeat';

    var timeOut = 10;
    var timer = 0;

    //设置发直播按钮
    function initPushLiveBtn() {
        $('.live-left .request-box a').data('liveStatus', 'pushLive').html('<span class="roomIcon pushLiveIcon"></span>发直播');
        return;
    }
    //loading tips
    var loadID = "connect-client-loading";
    var loadingShow = true;
    function loading(text, notShow) {
        if (!loadingShow || notShow) {
            loadingShow = !notShow;
            if (dialog.get(loadID)) {
                dialog.get(loadID).close().remove();
            }
            return false;
        }
        var content = '<span class="loading-gif"></span><p class="loading-text">'+text+'</p>'
        if(arguments.length > 2){
            for(var i = 2; i < arguments.length; i++){
                content += '<p class="loading-text" style="text-align: center;display: block;">'+arguments[i]+'</p>';
            }
        }
        var d = initNotice(content);
        var node = d.node;
        $(node).find('.ui-dialog-content').html(content);
        return d;

        function initNotice(content) {
            if (!dialog.get(loadID)) {
                var d = dialog({
                    skin: 'err-notice',
                    id: loadID,
                    content: content,
                    fixed: true
                });
                var node = dialog.get(loadID).node;
                $(node).find('.loading-gif').css({
                    'min-width': '20px',
                    height: '20px',
                    'background-img':'url("'+$conf.domain +'static/img/gift/load.gif")',
                    'background-repeat': 'no-repeat',
                    'display': 'inline-block',
                    'vertical-align': 'middle',
                    'background-position': '-1px 2px',
                    'margin-right': '5px'
                });
                $(node).find('.loading-text').css({
                    'display': 'inline-block',
                    'margin': '0',
                    'vertical-align': 'middle',
                    'line-height': '20px'
                });
                $(node).find('.ui-dialog-body').css('padding', '20px 0 20px 0')
                d.showModal();
                return dialog.get(loadID);
            }
            return dialog.get(loadID)
        }
    }
    function returnStepHttpData(port) {
        return {
            url : local + ':' + port + file
        }
    }
    msgToClient.interval = '';
    msgToClient.init = function () {
        var self = this;
        loading('正在和客户端进行连接，请勿刷新页面');
        self.interval = setInterval(function () {
            if (timer >= timeOut) {
                self.timeOutHandle();
                clearInterval(self.interval);
            } else {
                self.sendMsg(returnStepHttpData(portList[timer]));
            }
            timer++;
        }, 1000);
    };
    msgToClient.sendMsg = function (d) {
        $.ajax($.extend({}, d, {
            dataType: 'jsonp',
            jsonp: 'jsoncallback',
            success: function (d) {
                msgToClient.callBackFunc(d);
                return;
            },
            error: function (d) {
                console.log(d);
            }
        }));
    };
    msgToClient.callBackFunc = function (d) {
        clearInterval(msgToClient.interval);
        runSwfFunction('rtmpplayer_room', 'setVolumeAuthority',0,1);
        loading().close().remove();
    };
    msgToClient.timeOutHandle = function () {
        loading().close().remove();
        tips('您可能没有安装欢朋直播助手,请下载后再开播哦~');
    };
}();

var pushLiveBox = {};
!function (a) {
    pushLiveBox.openTheClient = {
        init: function () {
            if(!isWindows()){
                alert('请在windows电脑上操作,当前系统不支持此功能');
                return;
            }
            // if(anchor_is_on_living == '1'){return;}
            var html = '<div id="Client" style="display:none;"><iframe src="HuanpengTV://" style="display:none;"></iframe></div>';
            $('body').append(html);
            msgToClient.init();
        }
    };
}(jQuery);

function thisMovie(id) {
    return swfobject.getObjectById(id);
};


var $conf = conf.getConf();
var follow_stat = parseInt($ROOM.isFollow) || 0;


//tools
function myurlencode(param) {
    if (typeof param == 'string' || typeof param == 'number') {
        return encodeURIComponent(param)
    } else if (typeof param == 'object') {
        for (var i in param) {
            if (typeof param[i] == 'string' || typeof param[i] == 'number') {
                param[i] = encodeURIComponent(param[i]);
            }
            else if (typeof param == 'object') {
                param[i] = myurlencode(param[i]);
            } else {
                return '';
            }
        }

        return param;
    } else {
        return '';
    }
}

function isWindows() {
    return /windows|win32/i.test(navigator.userAgent);
}

function thisMovie(id) {
    return swfobject.getObjectById(id);
}

//主播收益 (欢豆)
function set_anchor_income(income) {
    $('.anchor_income .income').text(parseFloat(income).toFixed(1));
}
//主播等级 percent
function set_anchor_level(level, percent) {
    //console.log(percent);
    percent = percent > 100 ? 100 : percent;
    $('.anchor_level .anchorLvl_icon').addClass('lv' + level);
    $('.anchorLevel strong').css('width', percent + '%');
}
//主播观看人数
function set_anchor_viewerCount(count) {
    count = count < 0 ? 0 : digitsFormat(count);
    $('.player_otherdesc .viewer_count i').text(count);
}
//主播关注人数
function set_anchor_followCount(count) {
    count = digitsFormat(count);
    $('.nav_attention_left').text(count);
}
//设置用户欢朋豆
function set_user_hpbean(bean) {
    if (pageUser.isLogin) {
        bean = digitsFormat(bean);
        $('.anchor_money_left .bean_list span:eq(1)').text(bean);
    }
}
//设置用户欢朋币
function set_user_hpcoin(coin) {
    if (pageUser.isLogin) {
        coin = digitsFormat(coin);
        $('.anchor_money_left .coin_list span:eq(1)').text(coin);
    }
}

//取消关注
var is_rm_follow_loading = false;
function rm_follow() {
    if (is_rm_follow_loading || !check_login() || !check_phoneStatus(pageUser.user.phonestatus)) {
        return;
    }


    is_rm_follow_loading = true;

    var requestUrl = $conf.api + 'room/followUserCancel.php';
    // var requestUrl = './followUserCancel.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc'),
        luids:$ROOM.anchorUserID
    };
    ajaxRequest({url:requestUrl,data:requestData},function(){
        //tips('取消关注');
        is_rm_follow_loading = false;
        $('.followbtn:eq(0)').show();
        $('.followbtn:eq(1)').hide();
        set_anchor_followCount(parseInt($('.nav_attention_left').text()) - 1);
    },function(){
        is_rm_follow_loading = false;
    });
}

//关注
var is_follow_room_loading = false;
function follow_room() {
    if (is_follow_room_loading || !check_login() || !check_phoneStatus(pageUser.user.phonestatus)) {
        return;
    }
    if($ROOM.anchorUserID == pageUser.user.userID || $ROOM.anchorUserID == getCookie('_uid')){
        tips('不能关注自己哦~');
        return;
    }
    is_follow_room_loading = true;

    var requestUrl = $conf.api + 'room/followUser.php';
    // var requestUrl = './followUser.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc'),
        luid:$ROOM.anchorUserID
    };
    ajaxRequest({url:requestUrl,data:requestData},function(){
        //tips('关注');
        is_follow_room_loading = false;
        $('.followbtn:eq(0)').hide();
        $('.followbtn:eq(1)').show();
        set_anchor_followCount(parseInt($('.nav_attention_left').text()) + 1);
    },function(){
        is_follow_room_loading = false;
    });
}

//推荐直播列表
function return_liveList_html(d) {
    var url = $conf.domain + d.roomID;
    var imageClass = (d.orientation == 0 && d.ispic == 1) ? $conf.angleImage : '';

    if($ROOM.anchorUserID == d.uid){
        return '';
    }else{
        return '<div class="liveOne">\
    <a href="'+url+'">\
    <div class="imagecontainer">\
    <img class="'+imageClass+'" src="'+d.poster+'">\
    <div class="playopt"></div>\
    </div>\
    <div class="liveInfo">\
    <div class="videoName">'+d.title+'</div>\
    <div class="clear"></div>\
    <div class="liveDetail">\
    <span class="anchor_icon anchor"></span>\
    <span class="anchor_nick">'+d.nick+'</span>\
    <span class="anchor_icon viewerIcon2"></span>\
    <span>'+d.viewCount+'</span>\
    <span class="anchor_gameName">'+d.gameName+'</span>\
    </div>\
    </div>\
    </a>\
    </div>';
    }
}

//主播视频列表
function return_videoList_html(d) {
    var url = 'videoRoom.php?videoid=' + d.videoID;
    var imageClass = (d.orientation == 0 && d.ispic == 1) ? $conf.angleImage : '';
    return '<div class="liveOne"><a href="' + url + '"><div class="imagecontainer"> <img class="' + imageClass + '" src="' + d.poster + '"> <div class="playopt"></div> </div> <div class="liveInfo"> <div class="videoName">' + d.title + '</div> <div class="clear"></div> <div class="liveDetail"> <span class="anchor_icon anchor"></span> <span class="anchor_nick">' + d.nick + '</span> <span class="anchor_icon viewerIcon2"></span> <span>' + d.viewCount + '</span> <span class="anchor_gameName">' + d.gameName + '</span> </div> </div> </a></div>';
}


var clickuid;
var gustid = 0;
var click_chatid = null; //msgid 为举报弹幕准备
var clicknickuser;
var user_black_list = new Array();
var roomgrouplist = new Array();
var welcomeList = new Array();

//显示欢迎信息
function isShowWelcomeMsg(uid, level){
    var timeOut = 5 * 60 * 1000;
    var result = false;
    (is_in_list() || add_to_welcomeList()) && is_time_out() && add_to_welcomeList();
    return result;
    function add_to_welcomeList(){
        welcomeList[uid] = [];
        welcomeList[uid]['level'] = level;
        welcomeList[uid]['time'] = new Date().getTime();

        result = true;
    }

    function is_in_list(){
        return welcomeList[uid] ? true : false;
    }

    function is_time_out(){
        var time = new Date().getTime();
        return (time - welcomeList[uid].time >= timeOut);
    }
}


if (pageUser.isLogin) {
    if (pageUser.user.userID == $ROOM.anchorUserID) {
        roomgrouplist[pageUser.user.userID] = [];
        roomgrouplist[pageUser.user.userID]['roomgroup'] = 5;
    } else {
        roomgrouplist[pageUser.user.userID] = [];
        roomgrouplist[pageUser.user.userID]['roomgroup'] = pageUser.user.groupid;
    }
}

//waiting
function menu_show(e) {
    var d = pageUser.isLogin ? pageUser.user.userID : gustid;
    clickuid = $(e).data('uid');
    clicknickuser = $(e).html();

    //举报弹幕需要msgid
    click_chatid = $(e).nextAll('.lr_userwords').data('msgid');
    if (click_chatid == null || click_chatid == '') {
        //||Number(gid) > 5
        $('#user_report').parent().hide();
    } else {
        $('#user_report').parent().show();
    }
    if ((roomgrouplist[d]['roomgroup'] >= 4 && roomgrouplist[clickuid]['roomgroup'] < 4) || (roomgrouplist[clickuid]['roomgroup'] < 4 && pageUser.user.groupid == 5 ) || (roomgrouplist[clickuid]['roomgroup'] < 4 && pageUser.user.groupid == 2)) {
        $('#black_img').parent().show();

    } else {
        $('#black_img').parent().hide();
    }
    if (roomgrouplist[clickuid]['pg'] == 5) {
        $("#black_img").parent().hide();
    }
    $('#adminsetup').parent().hide();
    //console.log(d);
    if (roomgrouplist[d]['roomgroup'] == 5 || (pageUser.user.groupid == 5 && roomgrouplist[clickuid]['roomgroup'] < 5)) {
        $('#adminsetup').parent().show();
        //console.log('run the adminsetup');
        if (roomgrouplist[clickuid]['roomgroup'] == 4) {
            $("#adminsetup").html('解除管理员');
            $('#adminsetup').attr('rel', 1);
        } else {
            $('#adminsetup').html('任命管理员');
            $('#adminsetup').attr('rel', 4);
        }
    }

    if (user_black_list.indexOf(parseInt(clickuid)) > -1) {
        $('#user_black').html('取消屏蔽');
        $('#user_black').attr('rel', 1);
    } else {
        if (user_black_list.length >= 10) {
            $('#user_black').html('屏蔽人数已满');
            $('#user_black').attr('rel', 3);
        } else {
            $('#user_black').html('屏蔽该用户');
            $('#user_black').attr('rel', 0);
        }
    }
}

//任命管理员
function adminreg() {
    var requestUrl = $conf.api + 'room/addHomeAdmin.php';
    var requestData = {
        uid: getCookie('_uid'),
        encpass: getCookie('_enc'),
        nick: clicknickuser
    };
    ajaxRequest({url:requestUrl,data:requestData},function () {
        roomgrouplist[clickuid]['roomgroup'] = 4;
        alert('设置管理员成功');
    },function(){
        alert('系统繁忙');
    });
}

//取消管理员
function admindel() {
    var requestUrl = $conf.api+'room/cancelHomeAdmin.php';
    var requestData = {
        uid: getCookie('_uid'),
        encpass: getCookie('_enc'),
        adminID: clickuid
    };
    ajaxRequest({url:requestUrl,data:requestData},function(){
        roomgrouplist[clickuid]['roomgroup'] = 1;
        alert('取消管理员成功');
    },function () {
        alert('系统繁忙');
    });
}
//禁言用户
function silencedUser() {
    if (clickuid == pageUser.user.userID) {
        tips('不能禁言自己');
        return false;
    }
    if (roomgrouplist[pageUser.user.userID]['roomgroup'] < 4) {
        tips('权限不够');
        return false;
    }
    if (roomgrouplist[clickuid]['roomgroup'] >= 4 || clickuid == $ROOM.anchorUserID) {
        tips('不能禁言主播或房管');
        return false;
    }
    var requestUrl = $conf.api + 'room/setsilenced.php';
    var requestData =  {
        uid: getCookie('_uid'),
        encpass: getCookie('_enc'),
        luid: $ROOM.anchorUserID,
        targetUID: clickuid
    };
    ajaxRequest({url:requestUrl,data:requestData},function(d){
        if(requestData.uid  != requestData.targetUID){
            //setUserSilenceStatus(d.timestamp);
            chat_obj.errorMsg(clicknickuser+'已被你禁言');
        }else{
            setUserSilenceStatus(d.timestamp);
        }
    },function (d) {
        //添加 状态

    });
}

var black_obj = {
    black_show: function () {
        //
        var c = !pageUser.isLogin ? gustid : pageUser.user.userID;
        if (pageUser.user.groupid != 2) {
            if ((roomgrouplist[c]['roomgroup'] < 4 && pageUser.user.groupid != 5) || c == clickuid) {
                return false;
            }
        }
    },
    black_myblacklist_add: function () {
        //添加屏蔽用户
        if (user_black_list.indexOf(parseInt(clickuid)) > -1 || user_black_list.length > 10) {
            return false;
        }
        $('#itemopen #user_black').html('取消屏蔽');
        $('#itemopen #user_black').attr('rel', 1);
        user_black_list.push(parseInt(clickuid));
    },
    black_myblacklist_del: function () {
        if (user_black_list.indexOf(parseInt(clickuid)) == -1) {
            return false;
        }
        $('#user_black').html('屏蔽该用户');
        $('#user_black').attr('rel', 0);
        var c = user_black_list.indexOf(parseInt(clickuid));
        user_black_list.splice(c, 1);//从屏蔽列表删除该用户
    },
    black_myblacklist_send: function (c) {
        //向播放器发送我的屏蔽列表
    },
    chat_report: function () {
        var d = !pageUser.isLogin ? gustid : pageUser.user.userID;
        if (d == clickuid) {
            alert('不能举报自己');
            return false;
        }
        if (click_chatid == '' || click_chatid.length <= 1) {
            $('#itemopen').hide();
            return false;
        }
        //handle the event;
        //console.log('message report');
        var requestUrl = $conf.api + 'room/reportChatMessage.php'
        var requestData = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
            luid:$ROOM.anchorUserID,
            messageid:click_chatid,
            reportUid:clickuid
        };

        alert('举报成功');
        $('#itemopen').hide();

        ajaxRequest({url:requestUrl,data:requestData},function () {
            // alert('举报成功');
            // $('#itemopen').hide();
        });
    }
};

//欢豆领取机制  waiting

var gBean_obj;
!function (a) {
    var imgUrl = $conf.domain + '../static/img/get_bean/';
    var getBeanConf = {
        box_img: {
            wait: imgUrl + 'not-login-1.jpg',//'boxWaitImg.png',
            already: imgUrl + 'not-login-2.png'// 'boxAlready.png'
        },
        list_img: {
            waiting: imgUrl + 'waiting.png',
            //grow:
            receive: imgUrl + 'receive.png',
            already: imgUrl + 'already.png'
        }
    };

    function initGetHpBean(lvl, time, isVip) {
        a('#box_show').data('lvl', lvl);
        if (isVip == 1) {
            //for the vip user
        } else {
            if (lvl > 5) {
                a.each(a('#get_rem .lw_list li'), function (index, element) {
                    a(element).find('span img').attr('src', getBeanConf['list_img'].already);
                    a(element).find('a').removeClass().addClass('already').text('已领取');
                });
                $('#get_time').html('你真无聊');
                return false;
            }
        }

        if (!$('#box').hasClass('wait')) {
            $('#box img').attr('src', getBeanConf['box_img'].wait);
            $('#box').addClass('wait');
        }


        var curr = a('#get_rem .lw_list li:eq(' + (lvl - 1) + ')');
        if (lvl > 1) {
            a.each(curr.prevAll(), function (index, element) {
                a(element).find('span img').attr('src', getBeanConf['list_img'].already);
                a(element).find('a').removeClass().addClass('already').text('已领取');
            });
        }

        if (time <= 0) {
            //设置可领取状态
            curr.find('span img').attr('src', '../static/img/get_bean/receive.png');
            $('#box').removeClass('wait')
            curr.find('a').removeClass().addClass('receive').text('可领取');
            $('#box img').attr('src', getBeanConf['box_img'].already);
            $('#get_time').html('可领取');
        } else {

            curr.find('span img').attr('src', getBeanConf['list_img'].waiting);
            curr.find('a').removeClass().addClass('grow').text('培育中...');

            var sCount = Number(time);//+60;
            var minute = parseInt(sCount / 60);
            var second = parseInt(sCount % 60);
            console.warn('到时领取分钟数'+minute);
            console.warn('到时领取秒数'+ second);
            minute = minute < 10 ? '0' + minute : minute;
            second = second < 10 ? '0' + second : second;

            a('#get_time').text(minute + ':' + second);
            setTimeout(setTime, 1000);
        }
    }

    function setTime() {
        var time = a('#get_time').text();
        if (!time || time == "" || time.length <= 0 || time.indexOf(':') < 0) {
            return false;
        }
        var tArray = time.split(':');
        var minute = parseInt(tArray[0]);
        var second = parseInt(tArray[1]);

        var sCount = minute * 60 + second;

        if (sCount > 1) {
            sCount--;

            var m = parseInt(sCount / 60);
            var s = parseInt(sCount % 60);

            m = m < 10 ? ('0' + m) : m;
            s = s < 10 ? ('0' + s) : s;

            a('#get_time').html(m + ':' + s);
            setTimeout(setTime, 1000);
        } else {
            //可领取阶段
            var lvl = Number(a("#box_show").data('lvl'));
            var curr = a('#get_rem .lw_list li:eq(' + (lvl - 1) + ')');
            curr.find('span img').attr('src', '../static/img/get_bean/receive.png');
            curr.find('a').removeClass().addClass('receive').text('可领取');
            $('#get_time').html('可领取');
        }
    }

    function hb_receive() {
        if (!check_login()) {
            return false;
        }
        if(!check_phoneStatus(pageUser.user.phonestatus)){
            return;
        }
        $('#get_rem').css('left', '-1000px').addClass('to-none');
        geetest({product:'popup',append:'#receiveBean-validate'}, function(data){
            requestGetBean(0, data);
        });
        function requestGetBean(vcode, gtdata) {
            var data = {
                uid: getCookie('_uid'),
                encpass: getCookie('_enc'),
                lvl: a('#box_show').data('lvl'),
                luid: $ROOM.anchorUserID,
                event: 'pick',
                vcode: vcode,
                type:'gt'
            };
            var requestData = $.extend({},data,gtdata);
            var requestUrl = $conf.api + 'room/shamApi_gb_enter.php';
            // var requestUrl = './shamApi_gb_enter.php';
            ajaxRequest({url:requestUrl,data:requestData},function (d) {
                var lvl = parseInt(d.lvl);
                var time = parseInt(d.time);
                var isVip = parseInt(d.isVip);
                initGetHpBean(lvl, time, isVip);
                if(d.revCount){
                    set_user_hpbean(d.hpbean);
                    tips('领取了' + d.revCount + '个欢朋豆');
                }else{
                    tips('你已经领取过了');
                }
            },function(d){
                if(d.code == -4031){
                    tips(d.desc);
                    $('.ui-state-error-text').remove();
                    $('.gb-vcodeDiv').after('<p class="ui-state-error-text">验证码错误</p>');
                    $('.gb-vcodeDiv .gb-imgCode img').attr('src', $conf.api + 'shamApi_gb_vcode.php?nowtime=' + new Date().getTime());
                    return false;
                }
            });
        }
    }
    gBean_obj = {
        show_time: initGetHpBean,
        receive: hb_receive
    }
}(jQuery);

//抢宝箱机制
var treasure_obj;
!function (a) {
    var treasure_loading = false;//防止多次请求
    var waitList = [];
    var isBoxshow = 0;
    var boxCount = 0;
    var curr_trid = 0;
    var encpass = getCookie('_enc');
    var noticeId = '';

    function treasure_receive() {
        if (treasure_loading || !curr_trid || !check_login() || !check_phoneStatus(pageUser.user.phonestatus)) {
            return;
        }
        treasure_loading = true;

        var requestUrl = $conf.api + 'room/open_treasure.php';
        // var requestUrl = './open_treasure.php';
        var requestData = {
            uid:pageUser.user.userID,
            treasureID:curr_trid,
            encpass:encpass,
            luid:$ROOM.anchorUserID
        };

        ajaxRequest({url:requestUrl,data:requestData},function (d) {
            noticeId = 'notice-id' + curr_trid;
            curr_trid = 0;
            if(d.count > 0){
                set_user_hpbean(d.hpbean);
                receiveSuccNotice(d.nick,d.count);
            }else {
                receiveFailed();
            }
        },function () {
            noticeId = 'notice-id' + curr_trid;
            curr_trid = 0;
            receiveFailed();
        });
    }

    function receiveSuccNotice(nick, bean) {
        var content = '<p class="small">恭喜你打开了<span class="nick" style="color:#ff7800;">' + nick + '</span>的宝箱</p><p class="big">抽取到了<span class="bean">' + bean + '</span>欢朋豆</p>'
        var skin = 'err-notice receiveTreasure-notice successful';
        receiveNoticeBox(content, skin, noticeId);
    }

    function receiveFailed() {
        var content = '<p class="big">很遗憾，您什么都没有抽到</p><p class="big">下次再试试吧～</p>';
        var skin = 'err-notice receiveTreasure-notice failed';
        receiveNoticeBox(content, skin, noticeId);
    }

    function receiveNoticeBox(content, skin, id) {
        $('.receiveTreasure-notice').parent().remove();
        var diaLog = dialog({
            content: content,
            title: '领取宝箱',
            skin: skin,
            id: id
            //fixed:true
        });
        var container = $('.liveRoom_video_container');
        var notice = $('.receiveTreasure-notice');

        notice.find('.ui-dialog-arrow-a').hide();
        notice.find('.ui-dialog-arrow-b').hide();
        notice.find('.ui-dialog-close').remove();
        notice.append('<div class="backgroundImg"></div>');

        var left = (container.width() + parseInt($('.sidebar').width()) + 20 ) / (2 * parseInt($('.container').width()));
        left = parseInt(left * 100);
        notice.css({
            left: left + '%',
            top: '50%',
            'margin-left': -parseInt(notice.width()) / 3 + 'px',
            position: 'fixed'
        });
        diaLog.show($('.liveRoom_nav').get()[0]);
        $('.ui-popup-backdrop').css('opacity', '0');

        var element = dialog.get(id).node;
        element = $(element).find('.receiveTreasure-notice');

        element.css('margin-top', '20px');//element.height() +'px'
        element.animate({'margin-top': '0px'}, 500, function () {
            setTimeout(function () {
                diaLog.close().remove();
                treasure_loading = false;
                a('#treasure_box_div a').remove();
                isBoxshow = 0;
                try{
                    if (boxCount <= 0 || waitList.length <= 0) {
                        console.log('the waitList lengtn is 0');
                        $('#treasure-box-content').remove();
                        return;
                    } else {
                        var obj = waitList.shift();
                        obj.unick = obj.unick ? obj.unick : obj.nick;
                        initBox(obj.trid, obj.uid, obj.ctime, obj.unick, parseInt($ROOM.treasure.timeOut));
                    }
                }catch(e){
                    console.log(e)
                }

            }, 1000);
        });
    }

    function initBoxOpen() {
        a('#treasure_box_div .tr_box_body').toGif({
            width: 80,
            height: 80,
            row: 1,
            num: 1,
            imgUrl: $conf.domain + '../static/img/gift/treasureBox/box-open.png',
            fps: 60
        });
    }

    function initBoxClose(){
        a('#treasure_box_div .tr_box_body').toGif({
            width: 80,
            height: 80,
            row: 3,
            num: 3,
            imgUrl: $conf.domain + '../static/img/gift/treasureBox/box.png',
            fps: 8
        });
    }

    function initBox(trid, uid, ctime, unick, timeOut) {
        if (isBoxshow == 0) {
            isBoxshow = 1;
            curr_trid = trid;
            //当前没有宝箱
            a('#treasure_box_div').html(return_box_html());
            a('#treasure_box_div #treasure-box-content').data('trid', trid);
            a('#treasure_box_div #treasure-box-content').data('uid', uid);
            a('#treasure_box_div').attr('title', unick + '的宝箱');
            initBoxClose();
            var date = new Date();
            var time = timeOut - (parseInt(date.getTime() / 1000) - ctime);
            if (time <= 0) {
                a('.tr_box_time').html('可领取');
                a('#treasure_box_div #treasure-box-content').removeClass().addClass('receive');
            } else {
                var sCount = time;
                var minute = parseInt(sCount / 60);
                var second = parseInt(sCount % 60);
                minute = minute < 10 ? '0' + minute : minute;
                second = second < 10 ? '0' + second : second;

                a('.tr_box_time').html(minute + ':' + second);
                setTimeout(setTime, 1000);
            }
        } else {
            var obj = {
                trid: trid,
                uid: uid,
                ctime: ctime,
                unick: unick
            };
            waitList.push(obj);
        }
        boxCount = waitList.length + 1;
        if (boxCount > 1) {
            a('.treasure_num').show().text(boxCount)
        }
    }

    function return_box_html() {
        return '<span id="treasure-box-content" href="javascript:;" class="wait"> <div class="treasure_num">' + boxCount + '</div><div class="tr_box_notice"></div> <div class="tr_box_body"></div> <div class="tr_box_time"></div> </span>';
    }

    function setTime() {
        var time = a('.tr_box_time').text();
        if (!time || time == "" || time.length <= 0 || time.indexOf(':') < 0) {
            return false;
        }
        var tArray = time.split(':');
        var minute = parseInt(tArray[0]);
        var second = parseInt(tArray[1]);

        var sCount = minute * 60 + second;

        if (sCount > 1) {
            sCount--;

            var m = parseInt(sCount / 60);
            var s = parseInt(sCount % 60);

            m = m < 10 ? ('0' + m) : m;
            s = s < 10 ? ('0' + s) : s;

            a('.tr_box_time').html(m + ':' + s);
            setTimeout(setTime, 1000);
        } else {
            a('.tr_box_time').html('可领取');
            a('#treasure_box_div #treasure-box-content').removeClass().addClass('receive');
        }
    }

    treasure_obj = {
        init: initBox,
        receive: treasure_receive,
        initWaitList:function(list){
            waitList = list;
        }
    };
}(jQuery);



var gift_switch = 1;

function gb_enter(luid) {
    var requestUrl = $conf.api + 'room/shamApi_gb_enter.php';
    var requestData = {
        uid:getCookie('_uid'),
        encpass:getCookie('_enc'),
        luid:luid,
        event:'enter'
    };
    ajaxRequest({url:requestUrl,data:requestData},function (d) {
        if(d.lvl){
            gBean_obj.show_time(d.lvl,d.time,d.isVip);
        }
    });
}

//聊天栏显示送礼
function return_giftBetter_html(giftid, giftName, uid, nick, img) {
    var nodeClass = 'item_' + parseInt(giftid % 30);
    return '<div id="gift-node-' + uid + '' + giftid + '" class="giftbetter-item ' + nodeClass + '"><div class="item-back"> <div class="item-name">' + nick + '</div> <div class="item-gift clear"> <span class="fl">送出</span> <span class="fr">' + giftName + '</span> </div><div class="item-num"><span class="n x"></span></div></div> <div class="item-head"> <img src="' + img + '" alt=""/> </div> </div>';
}
var gbter_obj;
var waitObj = {};
var rlist_edit_loading = false; //读写锁
gbter_obj = {
    conf:{
        31:{
            img:'../static/img/gift/better/gift-1.png',
            time:'1000'
        },
        32:{
            img:'../static/img/gift/better/gift-2.png',
            time:'1000'
        },
        33:{
            img:'../static/img/gift/better/gift-3.png',
            time:'2000'
        },
        34:{
            img:'../static/img/gift/better/gift-4.png',
            time:'5000'
        },
        35:{
            img:'../static/img/gift/better/gift-5.png',
            time:'10000'
        }
    },
    _runNext:function(){
        var self = this;
        if(self.waitList.length){
            var d = self.waitList.delete();
            if(d!=false){
                self.add(d);
            }else if(self.waitList.length){
                self._runNext();
            }
        }
    },
    runNext:function(){
        var used = this.runList.length;
        for(var i = used; i<=4; i++){
            this._runNext();
        }
    },
    add:function(d){
        var self = this;
        var runList = self.runList;
        var waitList = self.waitList;
        if(!d.in_timer) d.in_timer = d.timer;
        var index = runList.isExist(d.ouid, d.gid);
        if(index){
            if(!runList.update(index - 1, d.timer)){
                waitList.add(d);
            }
        }else{
            if(runList.length >= 4){
                if(d.gid == 35){
                    waitList.add(d);
                    var position = runList.lowest();
                    if(position && runList.list[position-1].giftID != 35){
                        runList.list[position-1].obj.del(function(id){
                            runList.delete(id);
                            self.runNext();
                        });
                    }
                }else{
                    waitList.add(d);
                }
            }else{
                runList.add(d);
            }
        }
    },
    runList:{
        length:0,
        stat:[0,0,0,0],
        list:[],
        add:function(d){
            var self = this;
            if(rlist_edit_loading || self.length >= 4){
                gbter_obj.waitlist.add(d);
                return;
            }
            rlist_edit_loading = true;
            var index;
            var tmp = {};
            index = this.stat.indexOf(0);

            tmp.uid = d.ouid;
            tmp.giftID = d.gid;
            tmp.obj = this._createGiftBetter(index,d);

            this.stat[index] = 1;
            this.list[index] = tmp;
            this.length++;
            rlist_edit_loading = false;
        },
        delete:function(index){
            rlist_edit_loading = true;
            this.list[index] = null;
            this.length --;
            this.stat[index] = 0;
            rlist_edit_loading = false;
        },
        update:function(index, time){
            return this.list[index].obj.changeTimer(time);
        },
        isExist:function(uid,giftID){
            for(var i in this.list){
                if(this.list[i] && this.list[i].uid == uid && this.list[i].giftID == giftID)
                    return this.list[i].obj._id + 1;
            }
            return 0;
        },
        lowest:function(){
            var list = this.list;
            if(!list.length) return 0;
            var index = 0
            for (var i in list){
                if(list[i].giftID && list[i].giftID < list[index].giftID)
                    index = i;
            }
            return index + 1;
        },
        _createGiftBetter:function(id,d){
            var uid = d.ouid;
            var giftID = d.gid;
            var inTimer = d.in_timer;
            var timer = d.timer;
            var nick = d.ounn;
            var giftName = d.gname;
            var giftImg = gbter_obj.conf[giftID].img;
            var timeLimit = gbter_obj.conf[giftID].time;

            return new giftBetter(id, uid, giftID, inTimer, timer, nick, giftName, giftImg, timeLimit);
        }
    },
    waitList:{
        id:0,
        length:0,
        list:[],
        list_highest:[],//highest priority;
        indexConnect:[],//waitObj index connect : indexConnect[uid][gid]:wid
        add:function(d){
            if(this.isExist(d.ouid, d.gid)){
                this._update(this._getIndexWaitId(d.ouid, d.gid), d.timer);
            }else{
                //this._add(this._sort(this._setIndexWaitId(d.ouid, d.gid, this._setWaitObj(d))));
                var highest = d.gid == 35 ? 1 : 0;
                this._add2(this._setIndexWaitId(d.ouid, d.gid, this._setWaitObj(d)), highest);
            }
        },
        delete:function(){
            var wid;
            if(this.list_highest.length > 0){
                wid = this.list_highest.shift();
            }else{
                wid = this.list.shift();
            }
            //var wid = this.list.pop();
            var d = waitObj[wid];
            if(!d)
                return false;
            this._delIndexWaitId(d.ouid, d.gid);
            delete waitObj[wid];
            this.length--;
            return d;
        },
        isExist:function(uid, gid){
            return this._getIndexWaitId(uid, gid) == -1 ? false : true;
        },
        _update:function(waitid, timer){
            waitObj[waitid].timer = timer;
        },
        _add:function(index){
            this.list.splice(index, 0, this.id);
            this.id ++;
            this.length++;
        },
        _add2:function(id, highest){
            if(highest){
                this.list_highest.push(id);
            }else{
                this.list.push(id);
            }
            this.id ++;
            this.length++;
        },
        _setWaitObj:function(d){
            waitObj[this.id] = d;
            waitObj[this.id].in_timer = d.timer;
            var id = this.id;
            return id;
        },
        _sort:function(waitid){
            var list = this.list;
            var p = list.length;
            $.each(list, function(index,element){
                if (waitObj[element].gid >= waitObj[waitid].gid) {
                    p = index;
                    return;
                }
            });
            return p;
        },
        _getIndexWaitId:function(uid, gid){
            var self = this;
            if(self.indexConnect[uid] && self.indexConnect[uid][gid])
                return self.indexConnect[uid][gid] - 1;
            else
                return -1;
        },
        _setIndexWaitId:function(uid, gid, waitid){
            var self = this;
            if(!self.indexConnect[uid]) self.indexConnect[uid] = [];
            self.indexConnect[uid][gid] = waitid + 1;
            return waitid;
        },
        _delIndexWaitId:function(uid, gid){
            var self = this;
            self.indexConnect[uid] && self.indexConnect[uid][gid] && (self.indexConnect[uid][gid]=0);
        }
    }
};

function giftBetter(id, uid, giftID, inTimer, timer, nick, giftName, giftImg, timeLimit){
    this._id = id;
    this._uid = uid;
    this._giftID = giftID;
    this._inTimer = inTimer;
    this._timer = timer;
    this._nick = nick;
    this._giftName = giftName;
    this._giftImg = giftImg
    this._timeLimit = timeLimit;

    this._hideInterval='';
    this._changeNumInterval;
    this._dom = '';
    this._item = $('#giftBetter_item1');

    this._changeNumTimeOut = 200;

    if(this._inTimer > this._timer)
        this._inTimer = 1;

    this.init();
}

giftBetter.prototype = {
    _getHtmlStr:function(){
        var nodeClass = 'item_' + parseInt(this._giftID % 30);
        return '<div id="gift-node-' + this._uid + '' + this._giftID + '" class="giftbetter-item ' + nodeClass + '"><div class="item-back"> <div class="item-name">' + this._nick + '</div> <div class="item-gift clear"> <span class="fl">送出</span> <span class="fr">' + this._giftName + '</span> </div><div class="item-num"><span class="n x"></span><div class="item-num-box"></div></div></div> <div class="item-head"> <img src="' + this._giftImg + '" alt=""/> </div> </div>';
    },
    _getDom:function(){
        var nodeID = 'gift-node-'+this._uid+''+this._giftID;
        return $('#'+nodeID);
    },
    _createDom:function(){
        var html = this._getHtmlStr();
        if(this._giftID == 35){
            this._item.prepend(html)
        }else{
            this._item.append(html);
        }
        return this._getDom();
    },
    _changeNum:function(){
        var num = this._inTimer;
        var htmlstr = '';
        num = num.toString();
        for (var i = 0; i < num.length; i++) {
            htmlstr += '<span class="n num n' + num[i] + '"></span>';
        }
        this._dom.find('.item-num-box').html(htmlstr);
        var dom = this._dom.find('.item-num-box');
        //if(!dom.attr('style'))
        this._changeNumRenderFX();
    },
    _changeNumRenderFX:function(){
        if($conf.isIE){
            var dom = this._dom.find('.item-num-box'),
                right = parseInt(dom.css('margin-left'), 10) || 0,
                zRight = right + 5,
                top = parseInt(dom.css('margin-top'), 10) || 0,
                zTop = top - 4;

            supportCss3('transform') ? dom.css({
                    '-ms-transform':"scale(1.2)",
                    'margin-top':zTop,
                    'margin-left':zRight
                }).animate({
                    '-ms-transform':"scale(1)",
                    'margin-top':top,
                    'margin-left':right
                }, 100, function(){
                    $(this).removeAttr('style');
                }):($conf.isIE7 && (zRight = right + 10, zTop = top - 4), dom.css({
                    'zoom':'1.2',
                    'margin-top':zTop,
                    'margin-left':zRight
                }).animate({
                    'zoom':'1',
                    'margin-top':top,
                    'margin-left':right
                }, 100, function(){
                    $(this).removeAttr('style');
                }));
        }else{
            var dom = this._dom.find('.item-num-box'),
                right = parseInt(dom.css('margin-left'), 10) || 0,
                zRight = right + 5,
                top = parseInt(dom.css('margin-top'), 10) || 0,
                zTop = top - 3,
                scale_big = 1.2,
                scale_small = 1;
            $conf.isFF && (scale_big=1.1 , zTop = top - 3, zRight = right + 5), dom.css({
                'zoom':scale_big,
                '-moz-transform':"scale(" + scale_big + ")",
                'margin-top':zTop,
                'margin-left':zRight
            }).animate({
                'zoom':scale_small,
                '-moz-transform':"scale(" + scale_small + ")",
                'margin-top':top,
                'margin-left':right
            }, 100, function(){
                $(this).removeAttr('style')
            });
        }
    },
    _intervalChangeNum:function(){
        this._changeNum();
        var that = this;
        this._changeNumInterval = setInterval(function(){
            that._inTimer ++;
            if(that._inTimer > that._timer){
                //that._changeTimerLock = 1;
                clearInterval(that._changeNumInterval);

                that._changeNumInterval = false;
                that._intervalHide();
            }else{

                that._changeNum();
            }
        }, this._changeNumTimeOut);
    },
    _intervalHide:function(){
        if(!this._dom || !this._dom.get()[0]) return;
        var that = this;
        this._hideInterval = setInterval(function(){
            that._dom.animate({left:'332px'}, 300, function(){
                clearInterval(that._hideInterval);
                that._hideInterval = false;
                that.del(function(){
                    gbter_obj.runList.delete(that._id);
                    gbter_obj.runNext();
                });
            });
        }, this._timeLimit);
    },
    _show:function(){
        this._dom.animate({left:'8px'}, 100);
        this._intervalChangeNum();
    },
    init:function(){
        this._dom = this._createDom();
        this._show();
    },
    del:function(callBackFn){
        this._dom.remove();
        this._changeNumInterval && clearInterval(this._changeNumInterval) && (this._changeNumInterval=false);
        this._hideInterval && clearInterval(this._hideInterval) && (this._hideInterval=false);
        callBackFn && typeof callBackFn=='function' && callBackFn(this._id);
    },
    changeTimer:function(timer){
        var that = this;
        //console.log(timer);
        //console.log(this._changeTimerLock);
        if(this._changeNumInterval){
            checkInTimer(timer);
            this._timer = timer;
            return true;
        }else{
            if(this._hideInterval){
                checkInTimer(timer);
                clearInterval(this._hideInterval);
                this._timer = timer;
                this._show();
                return true
            }else{
                return false;
            }
        }

        function checkInTimer(timer){
            if(that._inTimer > timer){
                that._inTimer = 1;
            }
        }
    }
}

//更新主播 收益
function updateAnchor_income(gid, num) {
    var exp=0;
    var lvl = $ROOM.anchorLevel;

    if (gid == 31) {
        //exp = num / $ROOM.giftExp[gid]['money'] * $ROOM.giftExp[gid]['exp'];
        $ROOM.anchorIncome = parseFloat(num).toFixed(1);

        set_anchor_income($ROOM.anchorIncome);
    } else {
        exp = $ROOM.giftExp[gid]['exp'];
    }

    var maxLvl = 30;
    exp = parseInt(exp) + parseInt($ROOM.anchorIntegral);

    if (lvl == maxLvl) {
        set_anchor_level(lvl, exp / $ROOM.anchorLevelList[lvl] * 100);
    } else {
        for (var i = lvl; i <= maxLvl; i++) {
            if (exp <= $ROOM.anchorLevelList[i]) {
                $ROOM.anchorLevel = i;
                $ROOM.anchorIntegral = exp;
                set_anchor_level(i, exp / $ROOM.anchorLevelList[i] * 100);
                return;
            }
        }
    }
}

var runlist_write_lock = false;
var airWaitObj = {};
var airbetter_obj = {
    runlist: {},
    waitlist: [],
    track: [0, 0, 0],
    wid: 0,
    add: function (d) {
        var runid = d.uid.toString() + '_' + d.luid.toString();
        if (this.track.indexOf(runid) > -1) {
            var date = new Date();
            var airid = 'air_' + d.uid + '_' + date.getTime() + '' + parseInt(Math.random() * 10000);
            this.betterEffect(airid, this.track.indexOf(runid), d);
            return;
        }
        if (this.track.indexOf(0) > -1) {
            this.run_add(d);
        } else {
            this.wait_add(d)
        }
    },
    run_add: function (d) {
        if (runlist_write_lock) {
            this.wait_add(d);
        }
        var runid = d.uid.toString() + '_' + d.luid.toString();
        var date = new Date();
        var airid = 'air_' + d.uid + '_' + date.getTime() + '' + parseInt(Math.random() * 10000);

        runlist_write_lock = true;
        var index = this.track.indexOf(0);
        this.runlist[index] = {};
        this.runlist[index].nodeid = airid;
        this.track[index] = runid;
        runlist_write_lock = false;
        this.betterEffect(airid, index, d);
    },
    wait_add: function (d) {
        airWaitObj[this.wid] = d;
        this.waitlist.push(this.wid);
        this.wid++;
    },
    wait_del: function () {
        var wid = this.waitlist.shift();
        var d = airWaitObj[wid];
        airWaitObj[wid] = null;
        return d;
    },
    betterEffect: function (node_id, index, d) {
        var self = this;
        var html = '<a id="' + node_id + '" href="'+$conf.domain+d.roomID + '" class="air"><div> <div class="air_gif"></div> <div class="title_bg"><span class="nick">' + d.unick + '</span> 送给 <span class="nick">' + d.lunick + '</span> <span class="num">1</span>艘飞船，快来抢宝箱啊!</div><div class="title-after"></div> </div></a>';
        $('.aircraft_track:eq(' + index + ')').append(html);
        $('#' + node_id).find('.air_gif').toGif({
            width: 88,
            height: 88,
            row: 3,
            num: 3,
            imgUrl: $conf.domain + '../static/img/gift/craft.png',
            fps: 3
        });
        var nodeWidth = $('#' + node_id).width() + 20;
        $('#' + node_id).css({'right': '-' + nodeWidth + 'px', 'width': nodeWidth});

        $('#' + node_id).animate({right: '100%'}, 16000, 'linear', function () {
            if (self.runlist[index].nodeid == node_id) {
                if (runlist_write_lock) {
                    //容易死锁
                    var interval = setInterval(function () {
                        if (!runlist_write_lock) {
                            runlist_write_lock = true;
                            self.track[index] = 0;
                            self.runlist[index] = {};
                            runlist_write_lock = false;
                            clearInterval(interval);
                        }
                    }, 10);
                } else {
                    runlist_write_lock = true;
                    self.track[index] = 0;
                    self.runlist[index] = {};
                    runlist_write_lock = false;
                }
            }
            if (self.waitlist.length > 0 && self.track[0] > -1) {
                self.add(self.wait_del());
            }
            $('#' + node_id).remove();
        });
    }
};

//充值dialog
var errNotice = {};
!function () {
    var errStruct = {
        skin: 'err-notice',
        title: '提示',
        content: '',
        okValue: '充值',
        ok: function () {
            window.open($conf.person + 'recharge.php');
            return true;
        }
    };
    errNotice.recharge = function () {
        var d = dialog($.extend(errStruct, {
            content: '<p>赠送失败</p><p>当前欢朋币不足，点击获取</p>',
            okValue: '充值'
        }));
        $('.err-notice .ui-dialog-header .ui-dialog-close').text('').addClass('personal_icon close');
        $('.err-notice .ui-dialog-arrow-a').css('display', 'none').next().css('display', 'none');
        $('.err-notice').css({
            'left': '50%',
            'top': '50%',
            'position': 'fixed',
            'margin-left': -$('.err-notice').width() / 2 + 'px'
        });
        d.showModal($('.liveRoom_nav')[0]);
    }
    errNotice.phone = function () {
        var d = dialog($.extend(errStruct, {
            content: '<p>请先认证手机</p>',
            okValue: '绑定手机',
            ok: function () {
                window.open($conf.person + 'mp/certify_phone/');
                return true;
            }
        }));

        $('.err-notice .ui-dialog-header .ui-dialog-close').text('').addClass('personal_icon close');
        $('.err-notice .ui-dialog-arrow-a').hide().next().hide();
        $('.err-notice').css({
            'left': '50%',
            'top': '50%',
            'position': 'fixed',
            'margin-left': -$('.err-notice').width() / 2 + 'px'
        });

        d.showModal($('.liveroom_nav')[0]);

    }
}();

function setUserGroupList(uid, group) {
    if (uid >= 3000000000) return;
    uid = uid.toString();
    var index = {1:'user', 4:'admin', 5:'own'};
    if(roomgrouplist.indexOf(uid) == -1){
        roomgrouplist[uid] = [];
        if(uid == $ROOM.anchorUserID){
            roomgrouplist[uid]['roomgroup'] = $conf.group['own'];
        }else{
            roomgrouplist[uid]['roomgroup'] = $conf.group[index[group]];
        }
    }
}

function setUserSilenceStatus(timestamp) {
    pageUser.user.isSilence = 1;
    if(timestamp){
        pageUser.user.silenceTime = timestamp;
    }
}

function set_report_option(flag) {

    $('.anchor_report').addClass('none');
    if (flag) {
        $('.anchor_report').removeClass('none');
    }
}

function return_chatMsgTagHtml(lvl, uid, phone, type) {
    var limitLvl = 6;
    if (type != 'msg') {
        limitLvl = 1;
    }
    var lvlSpan = '';
    var tagSpan = '';
    var phoneSpan = '';
    if (lvl >= limitLvl) {
        lvlSpan = '<span class="chat-tag-block"><span class="ulevel userLvl-icon level' + lvl + '"></span></span>';
    }
    if (!roomgrouplist[uid]) {
        return phoneSpan + tagSpan + lvlSpan;
    }
    //这样写容易在客户端修改么？
    if (roomgrouplist[uid]['roomgroup'] == 5) {
        tagSpan = '<span class="chat-tag-block"><span class="roomTag own">主播</span></span>';
    } else if (roomgrouplist[uid]['roomgroup'] == 4) {
        tagSpan = '<span class="chat-tag-block"><span class="roomTag">房管</span></span>';
    }
    if (Number(phone) == 1) {
        phoneSpan = '<span class="chat-tag-block"><span class="anchor_icon phoneIcon"></span></span>';
    }

    return phoneSpan + tagSpan + lvlSpan;
}

function return_chatMsgLevelTagHtml(lvl){
    var type = arguments[1] ? arguments[1] : '';
    var limitLvl = type != 'msg' ? 1 : 6;
    var html = '';
    lvl >= limitLvl && (html = '<span class="chat-tag-block"><span class="ulevel userLvl-icon level' + lvl + '"></span></span>');
    return html;
}

function return_chatMsgHtml(d) {
    var self_item = '';
    var chatMsg = replace_em(d.msg);

    if (pageUser.isLogin && d.cuid == pageUser.user.userID) {
        self_item = "lr-chatMsg-self";
    }
    return '<li class="' + self_item + '">' + return_chatMsgTagHtml(d.level, d.cuid, d.phone) + '<span class="lr_username alertWarn " data-uid="' + d.cuid + '" data-tm="' + d.tm + '">' + d.cunn + '</span><i>:</i>' + '<span class="lr_userwords lr_other_userwords" data-msgid="' + d.msgid + '">' + chatMsg + '</span>' + '<div class="clear"></div>' + '</li>';
}
function return_chatGiftMSgHtml(d) {

    var img = {
        31: '../static/img/gift/gift-1.png',
        32: '../static/img/gift/gift-2.png',
        33: '../static/img/gift/gift-3.png',
        34: '../static/img/gift/gift-4.png',
        35: '../static/img/gift/gift-5.png'
    };
    var num = d.gid == 31 ? d.gnum + '粒' : d.timer + '连击';
    var text = '赠送给主播 ' + '<em style="color:#ff7800;">'+d.gname + '</em> <img src="'+img[d.gid]+'" style="border-radius:4px;">' + num;
    return '<li>' + return_chatMsgTagHtml(d.level, d.ouid, d.phone, 'gift') + '<span class="lr_username alertWarn" data-uid="' + d.ouid + '" data-tm="' + d.tm + '">' + d.ounn + '</span><i>:</i><span class="lr_userwords lr_other_userwords"style="padding-left:0px;">'+text+'</span><div class="clear"></div></li>'
}
function return_liveStartHtml() {

}
function return_liveEndHtml() {

}
function return_noticeMsgHtml(msg) {
    return '<li class="firstRemend"><span class="lr_userwords noticewords">系统广播：' + msg + '</span></li>';
}


var chatMessageHtml = {
    _levelTag:function(lvl){
        var type = arguments[1] ? arguments[1] : '';
        var limitLvl = type != 'msg' ? 1 : 6;
        var html = '';
        lvl >= limitLvl && (html = '<span class="chat-tag-block"><span class="ulevel userLvl-icon level' + lvl + '"></span></span>');
        return html;
    },
    _userTag:function(uid){
        var html = '';
        if(roomgrouplist[uid]['roomgroup'] == $conf.group.own){
            html = '<span class="chat-tag-block"><span class="roomTag own">主播</span></span>';
        }else if(roomgrouplist[uid]['roomgroup'] == $conf.group.admin){
            html = '<span class="chat-tag-block"><span class="roomTag">房管</span></span>';
        }
        return html;
    },
    _phoneTag:function(phone){
        if(phone == '1')
            return '<span class="chat-tag-block"><span class="anchor_icon phoneIcon"></span></span>';
        else
            return '';
    },
    _tag:function(lvl, uid, phone, type){
        return this._phoneTag(phone) + this._userTag(uid) + this._levelTag(lvl, type);
    },
    _nick:function(nick, tm, uid){
        return '<span class="lr_username alertwarn" data-uid="'+uid+'" data-tm="'+tm+'">'+nick+'</span><i>:</i>';
    },
    _nick2:function(nick){
        return '<span class="lr_username noticewords">' + nick + '</span>';
    },
    _msg:function(msg, msgid){
        var chatMsg = replace_em(msg);
        msgid = msgid ? msgid : '';
        return '<span class="lr_userwords lr_other_userwords" data-msgid="' + msgid + '">' + chatMsg + '</span><div class="clear"></div>';
    },
    welcome:function(nick, level){
        return '<li><img src="'+$conf.domain+'../static/img/emoji/welcome.png" class="welcome" />欢迎 '+this._levelTag(level)+this._nick2(nick)+'<span> 进入本直播间</span></li>';
    },
    msg:function(d){
        var classStr = '';
        pageUser.isLogin && (pageUser.user.userID == d.cuid) && (classStr = 'lr-chatMsg-self');
        return '<li class="'+classStr+'">'+this._tag(d.level, d.cuid, d.phone) + this._nick(d.cunn, d.tm, d.cuid) + this._msg(d.msg, d.msgid)+'</li>';
    },
    gift:function(d){
        var num = d.gid == 31 ? d.gnum + '粒' : d.timer + '连击';
        var text = '赠送给主播' + '<em style="color:#ff7800;">'+d.gnm + '</em> <img src="'+gbter_obj.conf[d.gid].img+'" style="border-radius:4px; margin:0px 4px;">' + num;
        return '<li>'+this._tag(d.level, d.ouid, d.phone, 'gift')+this._nick(d.ounn, d.tm, d.ouid)+'<span class="lr_userwords lr_other_userwords">'+text+'</span><div class="clear"></div></li>';
    },
    notice:function(msg){
        return '<li class="firstRemend"><span class="lr_userwords noticewords">系统广播：' + msg + '</span></li>';
    },
    openTreasure:function(d){
        var msg = '打开宝箱，获得了<em style="color:#03a9f4;"> ' + d.num + ' </em>个欢朋豆<img src="../static/img/gift/gift-1.png" style="border-radius:4px; margin:0px 4px;">';
        msg = '<span class="lr_userwords lr_other_userwords">' + msg + '</span><div class="clear"></div>';
        return '<li>'+this._nick(d.unick, d.tm, d.uid) + msg + '</li>';
    }
}

var shieldGift = false; //全局总开关
var shieldGiftNotice = true;
var shieldChatBanner = true;/*礼物combo 效果屏蔽，留作预留字段，现在做法是将外部隐藏*/
var shieldPlayerScroll = true;

var isLockScreen = false;
var socket_liveStart_receive = false;
var isNetWorkLogin = 0;
var chat_obj = {};
!function (a) {
    var sendMsgLock = 0;
    var sendMsgLockTime = 0;
    var myGroupID = pageUser.isLogin ? pageUser.user.groupid : 1;
    var chatLiDom = $('.lr_chat_ul');
    chat_obj.chatLiDom = function(){
        if(!chatLiDom.get()[0]){
            chatLiDom = $('.lr_chat_ul');
            return $('.lr_chat_ul');
        }else{
            return chatLiDom;
        }
    };
    chat_obj.proxy = swfobject.getObjectById('imProxy');
    chat_obj.login = function (ip, port) {
        var uid = getCookie('_uid') || 3000000000;
        var encpass = getCookie('_enc') || 'gustuserenterencpass';
        var roomid = $ROOM.anchorUserID;
        runSwfFunction('imProxy', 'login', ip, port, uid, encpass, roomid, 'proxyCallBack');
    };

    chat_obj.loginSuccSendMsg = function(){
        var content = {
            t:104,
            mid:new Date().getTime()
        };

        runSwfFunction('imProxy', 'sendMessage', JSON.stringify(content));
    };

    chat_obj.loginSucc = function () {
        console.log('chat login success in time' + new Date().getTime());


        chat_obj.loginSuccSendMsg();

        if (!gift_switch) gift_switch = 1;
        //if (pageUser.user.userID != $ROOM.anchorUserID) {
        //  room.viewerCount++;
        // set_anchor_viewerCount(room.viewerCount);
        //}
        gb_enter($ROOM.anchorUserID);
        isNetWorkLogin = 1;
        if (pageUser.user.userID == $ROOM.anchorUserID) return; //自己进入直播间不展示
        //在成功以后 才进行发信息
        // if (pageUser.user.level >= 10 && isShowWelcomeMsg(pageUser.user.userID, pageUser.user.level)) {
        //     chat_obj.chatLiDom().append(chatMessageHtml.welcome(pageUser.user.nickName, pageUser.user.level));
        // }
    };
    chat_obj.loginFailed = function () {};

    chat_obj.sendMessage = function (msg, outer) {
        if (sendMsgLock) return 1;
        sendMsgLock = 1;
        sendMsgLockTime = 2;
        msgSendTime();
        function msgSendTime() {
            var btn = $('.lr_inwrite .lr_insend');
            if (sendMsgLockTime > 0) {
                btn.addClass('disabled');
                btn.find('span').text('[' + sendMsgLockTime + ']');

                if (sendMsgLockTime == 0) {
                    btn.removeClass('disabled');
                    btn.find('span').text('发送');
                    sendMsgLock = 0;
                } else {
                    setTimeout(msgSendTime, 1000);
                    sendMsgLockTime--;
                }
            } else {
                btn.removeClass('disabled');
                btn.find('span').text('发送');
                sendMsgLock = 0;
            }
        }

        if(outer){
            if(!check_user_login())
                return 3;
            if(!check_phoneStatus(pageUser.user.phonestatus))
                return 4;

        }else{
            if (!check_login())
                return 3;
            if(!check_phoneStatus(pageUser.user.phonestatus))
                return 4;
        }

        if (pageUser.user.isSilence) {
            chat_obj.errorMsg('您当前被禁言，无法发送消息');
            $("textarea.lr_incon").val('');
            return 4;
        }
        var msg = $.trim(msg.replace(/\r\n/g, ""));
        if(msg == ''){
            chat_obj.errorMsg('请输入聊天信息');
            return;
        }
        if (msg.length > 50) {
            chat_obj.errorMsg('您的发言超出了规定长度');
            return 2;
        }
        var content = {
            t:100,
            mid:1001,
            msg:msg,
            identity:myGroupID
        };
        runSwfFunction('imProxy', 'sendMessage', JSON.stringify(content));
        if(outer){
            return 0;
        }

        $("textarea.lr_incon").val('');
        $("textarea.lr_incon").val($("textarea.lr_incon").val().replace(/[\r\n]/g, ""));
    };

    chat_obj.sendCallBack = function (m) {
        if(m.e){
            if (m.e == -3009) {
                chat_obj.errorMsg('您当前被禁言，无法发送消息');
                return false;
            }
        }else{
            $("textarea.lr_incon").val('');
            $("textarea.lr_incon").val($("textarea.lr_incon").val().replace(/[\r\n]/g, ""));
        }

    };

    chat_obj.welcome = function (m) {
        //set welcomeList, if in the welcomeList and the time <= 10 minue ,do not show the msg
        setUserGroupList(m.uid, m.group);
        set_anchor_viewerCount(m.viewCount);
        if (m.uid == $ROOM.anchorUserID) return; //自己进入直播间不展示
        //room.viewerCount++;
        // set_anchor_viewerCount(room.viewerCount);
        if(m.showWel==1 && isShowWelcomeMsg(m.uid, m.level)){

            chat_obj.chatLiDom().append(chatMessageHtml.welcome(m.nn, m.level));
        }
    };
    chat_obj.userExit = function (m) {
        //room.viewerCount--;
        set_anchor_viewerCount(m.viewCount);
    };

    chat_obj.liveStart = function (d) {
        socket_liveStart_receive = true;
        set_report_option(true);
        $ROOM.liveID = d.lid;
        //jx
        //initPlayer('rtmpplayer_room', $ROOM.anchorUserID);
        runSwfFunction('rtmpplayer_room', 'setHostID',parseInt($ROOM.anchorUserID));
        chat_obj.chatLiDom().append(chatMessageHtml.notice('直播开始'));
    };

    chat_obj.liveEnd = function () {
        set_report_option(false);
        chat_obj.chatLiDom().append(chatMessageHtml.notice('直播结束'));
        //send to the flash to show other living
        runSwfFunction('rtmpplayer_room', 'liveEnd', 1);
        // msgToClient.initPushLiveBtn();
    };

    chat_obj.giftMsg = function (d) {
        //console.log(return_chatGiftMSgHtml(d));
        if(!shieldGift || !shieldGiftNotice )
            $('.lr_chat_ul').append(return_chatGiftMSgHtml(d));
        if(d.gid == 31)
            updateAnchor_income(d.gid, d.gd);
        //欢豆不展示
        if (d.gid > 31)
            gbter_obj.add(d);
    };
    chat_obj.silenceMsg = function (d) {
        //判断 是否是自己
        if(pageUser.user.userID == d.uid){
            setUserSilenceStatus(d.outTimestamp);
        }
        chat_obj.errorMsg('用户' + d.targetNick + '被房管' + d.adminNick + '禁言一个小时');
    };
    chat_obj.sendTheAir = function (d) {
        //调度算法
        if ($ROOM.anchorUserID == d.luid) {
            treasure_obj.init(d.treasureID, d.uid, d.tm, d.nick, d.timeOut);
        }
        if( !shieldGift || !shieldPlayerScroll )
            runSwfFunction('rtmpplayer_room','addGiftBanner',d.uid, d.luid, d.nick, d.lunick, $conf.domain + 'room.php?luid='+d.luid);
        return;
    };
    chat_obj.msgShow = function (d) {
        if (user_black_list.indexOf(parseInt(d.cuid)) > -1) {
            //屏蔽 转number
            return false;
        }
        setUserGroupList(d.cuid, d.group);
        chat_obj.chatLiDom().append(chatMessageHtml.msg(d));

        var msg = d.msg.toString();
        msg = msg.replace(/\[em_([0-9]*)\]/g, '');
        runSwfFunction('rtmpplayer_room', 'JSONtoString', JSON.stringify({msg: msg}));
    };
    chat_obj.treasureopen = function (d) {
        if (d.num <= 0) {
            return;
        }

        chat_obj.chatLiDom().append(chatMessageHtml.openTreasure(d));
    };
    chat_obj.rRankList = function (d) {
        var rIndex = $('.lr_contribution_tab li.selected').index() + 1;
        if (rIndex == d.type) {
            set_rank_list(d.type);
        }
    };
    chat_obj.sendBean = function (c) {
        var proxy = chat_obj.proxy || swfobject.getObjectById('imProxy');
        if (!proxy || !proxy.sendMessage) {
            gift_switch = 1;
            return;
        }
        if (!check_login() || !gift_switch) {
            return;
        }
        if(!check_phoneStatus(pageUser.user.phonestatus)){
            return;
        }

        var content = {
            't': 102,
            'enc': getCookie('_enc'),
            'gid': c.giftid,
            liveid: $ROOM.liveID,
            'num': c.num,
            'identity':myGroupID
        };
        gift_switch = 0;
        runSwfFunction('imProxy', 'sendMessage', JSON.stringify(content));
    };
    chat_obj.sendBeanCallBack = function (d) {
        gift_switch = 1;
        if (d.e == 0) {
            set_user_hpbean(parseInt(d.bean));
            setProperty(d.bean, d.coin);
        }
        if (d.e) {
            if (d.e == -3515) {
                tips('您的欢朋豆余额不足');
            }else if (d.e == -3510){
                tips('服务器繁忙');
            }
        }
    };

    chat_obj.sendGift = function (c) {
        var proxy = chat_obj.proxy || swfobject.getObjectById('imProxy');
        if (!proxy || !proxy.sendMessage) {
            gift_switch = 1;
            return;
        }

        if (!check_login() || !gift_switch) {
            return;
        }
        if(!check_phoneStatus(pageUser.user.phonestatus)){
            return;
        }
        var content = {'t': 103, 'enc': getCookie('_enc'), 'gid': c.giftid, 'liveid': $ROOM.liveID, 'identity':myGroupID};
        runSwfFunction('imProxy', 'sendMessage', JSON.stringify(content));
        gift_switch = 0;
    };

    chat_obj.sendGiftCallBack = function (d) {
        gift_switch = 1;
        if (d.e == 0) {
            set_user_hpcoin(parseInt(d.coin));
            setProperty(d.bean, d.coin);
        }
        if (d.e) {
            if (d.e == -3514) {
                errNotice.recharge();
            }
        }
    };
    chat_obj.succEnterCallBack = function (d) {
        console.log(d);
    }
    chat_obj.liveNotice = function (d) {
        alert('您的直播涉嫌' + d.reason + '问题请及时更改');
    };
    chat_obj.liveStop = function (d) {
        if (pageUser.isLogin && pageUser.user.userID == d.luid) {
            alert('您的直播涉嫌' + d.reason + '问题已经关闭，请更改后在发起直播');
        }
    };
    chat_obj.killUser = function (d) {
        if (pageUser.isLogin && pageUser.userID == d.luid) {
            alert("您的直播涉嫌" + d.reason + "问题,该账号已被冻结，如有疑问请联系客服");
        } else {
            location.href = location.href;
        }
    };

    chat_obj.errorMsg = function (msg) {
        //chat_obj.chatLiDom().append(return_noticeMsgHtml(msg));
        chat_obj.chatLiDom().append(chatMessageHtml.notice(msg));
    }

    chat_obj.netWorkClose = function () {
        return;
        isNetWorkLogin = 0;
        netWorkLogin();
        function netWorkLogin() {
            var chatIp = $ROOM.chatServer[0].split(':')[0] || '';
            var chatPort = $ROOM.chatServer[0].split(':')[1] || '';
            var uid = getCookie('_uid') || 3000000000;
            var encpass = getCookie('_enc') || 'gustuserenterencpass';
            var roomid = $ROOM.anchorUserID;
            if (!isNetWorkLogin) {
                runSwfFunction('imProxy', 'login', chatIp, chatPort, uid, encpass, roomid, 'proxyCallBack');
                setTimeout(netWorkLogin, 2000);
            }
        }
    }
}(jQuery);

window.proxyCallBack = function (a, b) {
    console.log(a);
    console.log(b);
    if(a == 'result' && b == 'login.success'){
        console.log('proxyCallBack login success in time ' + new Date().getTime());
    }
    var obj = {};
    obj.result = {
        'login.success': 'loginSucc',
        'login.failed': 'loginFailed',
        'sendmessage.failed': '',
        'send.failed': ''
    };
    obj.receivemessage = {
        '501': 'welcome',
        '502': 'msgShow',
        '503': '',
        '504': 'giftMsg',
        '505': 'silenceMsg',
        '506': 'userExit',
        '511': 'treasureopen',
        '535': 'sendTheAir',
        '540': "liveNotice",
        "541": 'liveStop',
        '542': 'killUser',
        '601': 'liveStart',
        '602': 'liveEnd',
        '701': 'rRankList',
        '1100': 'sendCallBack',
        '1102': 'sendBeanCallBack',
        '1103': 'sendGiftCallBack',
        '1104': 'succEnterCallBack'
    };
    obj['network.error'] = {
        'closed': 'netWorkClose',
        'timeout': 'netWorkClose'
    };
    var isFunction = function (a, b, obj) {
        if (obj[a] && obj[a][b] && typeof chat_obj[obj[a][b]] == 'function') {
            return true;
        }
        return false;
    };
    var h, j;
    if (a == 'receivemessage') {
        h = eval('(' + b + ')');
        j = h.t;
    } else if (a == 'result' || a == 'network.error') {
        h = b;
        j = b;
    }
    //console.log(obj[a][j]);
    if (isFunction(a, j, obj)) {
        var runFunction = chat_obj[obj[a][j]];
        runFunction(h);
    }
};
var ranklist = {
    day: [],
    mon: [],
    all: []
};
//rankList
function return_rankOne_html(num, d) {
    var orderIcon = '';
    var span;
    if (num <= 3) {
        orderIcon = 'num_' + num;
        if (!d) {
            //console.log('no');
            return '<li><span class="orderIcon anchor_icon ' + orderIcon + '"></span><span>虚位以待</span><div class="clear"></div></li>';
        } else {
            span = '<span class="orderIcon anchor_icon ' + orderIcon + '"></span>';
        }
    } else {
        span = '<span class="orderIcon">' + num + '</span>';
    }
    return '<li> ' + span + ' <span class="icon icon_money anchor_icon hpcoin"></span> <span class="point">' + digitsFormat(d.money) + '</span> <span class="uNickname">' + d.nick + '</span> <div class="clear"></div></li>';
}

var is_rank_loading = false;
function set_rank_list(type) {
    if (is_rank_loading) {
        return false;
    }
    is_rank_loading = true;

    var requestUrl = $conf.api + 'room/LiveRoomRanking.php';
    // var requestUrl = './LiveRoomRanking.php';
    var requestData = {
        timeType:type,
        luid:$ROOM.anchorUserID
    };

    ajaxRequest({url:requestUrl,data:requestData},function(d){
        is_rank_loading = false;
        var i = 1;
        if(d.list){
            var htmlStr = '';
            $.each(d.list,function(key,val){
                htmlStr += return_rankOne_html(key +1,val);
                i ++;
            });

            if(i <= 3){
                for(;i<=3;i++){
                    htmlStr += return_rankOne_html(i, false);
                }
            }
            $('.lr_contribution .tabCon').eq(type - 1).html('<ul>' + htmlStr + '</ul>');
        }else{
            var htmlStr = '';
            for (; i <= 3; i++) {
                htmlStr += return_rankOne_html(i, false);
            }
            $('.lr_contribution .tabCon').eq(type - 1).html('<ul>' + htmlStr + '</ul>');
        }
    },function(){

    });
}


function pushliveCheck() {

}


var room = {};
var right_btn_flag = 0;
!function (a) {
    room.viewerCount = 0;
    room.init = function () {
        room.initSwfObbject();
        window.fullScreenSendMessage = function (msg) {
            return chat_obj.sendMessage(msg,1);
        };

        var chatIp = $ROOM.chatServer[0].split(':')[0] || '';
        var chatPort = $ROOM.chatServer[0].split(':')[1] || '';
        chat_obj.login(chatIp, chatPort);


        if (pageUser.isLogin) {
            room.user_status();
        } else {
            room.gust_status();
        }

        set_report_option($ROOM.isLiving);
        room.nav_player($ROOM);
        room.nav_follow($ROOM.fansCount);
        room.share_website();
        room.push_phone();
        room.gift();
        room.taskInit();
        room.get_bean();
        room.lr_treasure();
        room.chatopt();
        room.lr_rank_list();
        set_rank_list(1);
        room.chat_menushow();
        room.initGiftInfo();
        room.lr_other();

        /* junxiao*/
        room.resize_win_bind();
        room.mcscroll_bar_bind();
        room.live_video_notice();
        room.lr_videolist_resize();
        room.resize_right();
        room.room_right_bind();

        room.anchorToLive();

        room.initAngleImage();

        if(!isNetWorkLogin) chat_obj.netWorkClose();
    };

    //anchor 信息
    room.nav_player = function (d) {
        var navPlayer = $('.live_nav_player');
        navPlayer.find('.player_face>img').attr('src', d.anchorUserPicURL);

        var anchorInfo = navPlayer.find('.player_info');
        anchorInfo.find('.player_gamedesc').text(d.liveTitle);
        anchorInfo.find('.anchor_name').text(d.anchorNickName);
        anchorInfo.find('.anchor_gameName').text(d.gameName);

        set_anchor_income(d.anchorIncome);
        set_anchor_level(d.anchorLevel, d.anchorIntegral / d.anchorLevelList[d.anchorLevel] * 100);
        room.viewerCount = d.viewerCount;
        set_anchor_viewerCount(room.viewerCount);

        //举报template
        var reportHtmlFunc = '';
        $('.anchor_report').bind('click', function () {
            if (!check_login() || !check_phoneStatus(pageUser.user.phonestatus)) {
                return;
            }

            if (!reportHtmlFunc) reportHtmlFunc = huanpeng.template('jsTemplate-report');
            var html = reportHtmlFunc({nickName: $ROOM.anchorNickName, roomID: $ROOM.anchorUserID});
            var dialogs = dialog({
                skin: 'err-notice report light',
                content: html,
                fixed: true,
                title: '举报房间',
                button: [
                    {
                        value: '提交',
                        autofocus: true,
                        callback: function () {
                            initEvent();
                            return false;
                        }
                    }
                ]
            });
            //var pic = '';
            // $('#report-pic').change(function () {
            //     $('#report-pic-upload').ajaxSubmit({
            //         url: $conf.api + 'upload/upload.php',
            //         type: 'post',
            //         dataType: 'json',
            //         data: {
            //             uid: getCookie('_uid'),
            //             encpass: getCookie("_enc"),
            //             type: 0
            //         },
            //         success: function (d) {
            //             if (d.status == 1) {
            //
            //                 pic = d.content.picture;
            //             } else {
            //                 alert(d.content.desc);
            //             }
            //         }
            //     });
            // });

            dialogs.showModal();
            $('#report_name').text($ROOM.anchorNickName);
            $('#report_room').text($ROOM.anchorUserID);
            function initEvent() {
                var reason = $('#report-select').val();
                var contact = $('#contact').val();
                // if (!pic) {
                //     alert('pic not set');
                //     return;
                // }
                var data = {
                    uid: getCookie('_uid'),
                    encpass: getCookie('_enc'),
                    luid: $ROOM.anchorUserID,
                    liveID: $ROOM.liveID,
                    reason: reason,
                    contact: contact,
                    //pic: pic
                };

                ajaxRequest({url: $conf.api + 'other/report.php', data: data}, function () {
                    alert('举报成功');
                    location.href = location.href;
                }, function (d) {
                    if (d.type == 2)
                        tips(d.desc);
                    else
                        tips('举报失败');
                });
            }
        });

    };
    //关注
    room.nav_follow = function (c) {
        var followbtn = a('.nav_attention .followbtn:eq(0)');
        var followedbtn = a('.nav_attention .followbtn:eq(1)');

        followbtn.bind('click', follow_room);

        followedbtn.bind('click', rm_follow);
        followedbtn.hover(function () {
            $(this).text('取消关注');
        }, function () {
            $(this).text('已关注');
        });

        set_anchor_followCount(c);

        if (follow_stat) {
            followbtn.hide();
            followedbtn.show();
        }
    };
    //分享
    room.share_website = function () {
        var uid = 0;
        pageUser.isLogin && (uid = pageUser.user.userID);
        var shareDesc = getShareContent('wechat',$ROOM.liveTitle,$ROOM.anchorNickName,$ROOM.anchorUserID,uid);
        $('#wx-share-qrcode').qrcode({render: 'canvas', text: shareDesc.url, width: 120, height: 120});//table
        $('#wx-share-qrcode').html(convertCanvasToImage($('#wx-share-qrcode>canvas')[0]));
        $('.sharegroup .modalBody .url_text').val(location.href);

        var b = a('.sharegroup');
        var dom = {
            shareBtn: b,
            shareContent: b.find('#shareModal'),
            copyBtn: b.find('#copyUrl'),
            shareInput: b.find('.url_text')
        };

        var that = {};
        that.alert = false;
        dom.shareBtn.on("mouseenter", function () {
            if (!that.hasCopy) {
                b.addClass('onhover').css('height', '32px');
                dom.shareContent.removeClass('none');
                //
                dom.copyBtn.zclip({
                    path: $conf.domain + '../static/js/ZeroClipboard.swf',
                    copy: dom.shareInput.val(),
                    afterCopy: function () {
                        if (!that.alert) {
                            tips('已成功复制到您的剪切板');
                        }
                        that.alert = true;
                    }
                });

                that.hasCopy = !0;


            }
            $('#shareModal #wx-share-qrcode').hide();
        });
        dom.shareContent.on('mouseenter', function () {
            clearTimeout(that.timer);
        });
        dom.shareContent.on('mouseleave', function () {
            setTimeout(function () {
                b.removeClass('onhover').css('height', '');
                dom.shareContent.addClass('none');
                a('.zclip').remove();
                that.hasCopy = !1;
            }, 100);
        });
        dom.shareBtn.on('mouseleave', function () {
            that.timer = setTimeout(function () {
                b.removeClass('onhover').css('height', '');
                dom.shareContent.addClass('none');
                a('.zclip').remove();
                that.hasCopy = !1;
            }, 100);
            $('#shareModal #wx-share-qrcode').hide();
        });
        dom.copyBtn.on('click', function () {
            that.alert = false;
        });
        $('#shareModal .moreShare .shareBtn span').click(function () {

            var option = {
                url: location.href,
                title: $ROOM.liveTitle + '/' + $ROOM.anchorNickName,
                sumary: $ROOM.liveTitle + '/' + $ROOM.anchorNickName
            };

            var cmd = $(this).attr('data-cmd');
            var cmdData = {
                tsina:'weibo',
                tqq:'qq',
                tqzone:'qq',
                wx:'wechat'
            };
            var uid = 0;
            pageUser.isLogin && (uid = pageUser.user.userID);
            var shareDesc = getShareContent(cmdData[cmd],$ROOM.liveTitle,$ROOM.anchorNickName,$ROOM.anchorUserID,uid);

            if (cmd) {
                if (cmd == 'wx') {
                    $('#shareModal #wx-share-qrcode').show();
                } else {
                    option = {
                        sumary:$ROOM.liveTitle,
                        desc:shareDesc.content,
                        title:shareDesc.title,
                        url:shareDesc.url,
                        pics:$ROOM.anchorUserPicURL
                    };
                    Share.init(option, {channel: cmd});
                    $('#shareModal #wx-share-qrcode').hide();
                }
            }
        });
    };
    //canvas to base64
    room.push_phone = function () {
        var sharePhone = $('.nav_shareopt .sharephone');
        sharePhone.hover(
            function () {
                $(this).addClass('onhover');
                $('#sharePhoneModal').removeClass('none');
            },
            function () {
                $(this).css('height', '');
                $(this).removeClass('onhover');
                $('#sharePhoneModal').addClass('none');
            }
        );
        $('#qrCode').qrcode({render: 'canvas', text: $conf.domain + 'h5share/live.php?u='+$ROOM.anchorUserID, width: 110, height: 110});
        $('#qrCode').html(convertCanvasToImage($('#qrCode canvas')[0]));

    };

    //right
    room.initGiftInfo = function () {
        var gift = $('.liveroom_opt .gift_box .gift');
        var giftImg = $conf.domain + '../static/img/gift/';
        var item = [];
        var itemInfo = {
            matchs: '',
            type: '',
            sImg: '',
            bImg: '',
            name: '',
            xp: '',
            contribution: '',
            desc: ''
        };
        var value = [1, 2, 60, 1000, 6000];
        var nameList = ['欢朋豆', '欢朋特饮', '滑板', '黄色小面包', '飞碟'];
        var descList = [
            '快来给主播送欢朋豆吧',
            '累了，困了，喝欢朋特饮',
            '一步两步，一步两步',
            '没时间解释了，快上车',
            '一起去遨游太空吧'
        ];

        for (var i = 0; i < 5; i++) {
            var index = i + 1;
            var itemInfo = {};
            itemInfo.matchs = '.gift_item_' + index;
            itemInfo.type = i == 0 ? 1 : 2;
            itemInfo.sImg = giftImg + 'gift-' + i + '.png';
            itemInfo.bImg = giftImg + 'gift-' + i + '-big.gif';
            itemInfo.name = nameList[i];
            itemInfo.value = value[i];
            itemInfo.xp = value[i];
            itemInfo.contribution = value[i];
            itemInfo.desc = descList[i];
            item.push(itemInfo);
        }

        for (var i in item) {
            initTheList(item[i]);
        }


        function initTheList(item) {
            var giftItem = gift.find(item.matchs);
            var price = item.type == 1 ? item.name : item.name + "(" + item.value + "欢朋币)";
            //giftItem.find('.contribution').text('贡献值 ＋' + item.contribution + ' 经验值 +' + item.xp);
            giftItem.find('.contribution').text( ' 经验值 +' + item.xp);
            giftItem.find('.desc').text(item.desc);
            giftItem.find('.price').text(price);
            if (item.type == 2) {
                giftItem.find('.item_left img').attr('src', giftItem.bImg);
            }
        }
        //ID 336
        var beanDesc = gift.find('.gift_item_1 .contribution');
        beanDesc.text('100欢朋豆，经验值+1');
    };

    //right
    room.gift = function () {
        $('.gift li').hover(function () {
            $(this).addClass('item_onhover');
        }, function () {
            $(this).removeClass('item_onhover');
        });

        //送礼
        $('.gift li .lw_item').bind('click', function () {
            if (gift_switch != 1) {
                return false;
            }
            var giftid = $(this).parent().data('giftid');
            var gifttype = $(this).parent().data('gifttype');
            if (gifttype == 2) {
                if (!check_login()) {
                    return false;
                }
                var data = {
                    gtype: gifttype,
                    giftid: giftid,
                    num: 1
                };
                chat_obj.sendGift(data);
            }
        });


        $('.gift li.gift_item_1 .gift_item_hover .numSetBtnGroup span').bind('click', function () {
            if (!check_login()) {
                return false;
            }
            if (gift_switch != 1) {
                return false;
            }
            var giftid = $(this).parents('.item_onhover').data('giftid');
            var gifttype = $(this).parents('.item_onhover').data('gifttype');
            var num = parseInt($(this).text()) || 0;

            var index = $(this).index();
            var beanArray = [50,100,200,520,666,888,999,1000,1314];

            if(num != beanArray[index]){
                tips('赠送欢朋豆数额不合法哦');
                return;
            }

            if (gifttype == 1 && beanArray[index]) {
                var data = {
                    giftid: giftid,
                    num: beanArray[index]
                };
                chat_obj.sendBean(data);
            }
        });
    };

    room.get_bean = function () {
        a('#get_rem').on('click', 'a.receive', function () {
            gBean_obj.receive();
        });

        a('#box_show').click(function () {
            if(a('#get_rem').hasClass('to-none')){
                a('#get_rem').css('left','50%').removeClass('to-none');
            }else{
                a('#get_rem').css('left', '-1000px').addClass('to-none');
            }
        });
        a('#get_rem .close_rem_box').bind('click', function () {
            a('#get_rem').css('left', '-1000px').addClass('to-none');
        });

    };
    //waiting
    room.lr_treasure = function () {
        if ($ROOM.treasure.count > 0) {
            var tr_list = $ROOM.treasure.list;
            if(tr_list.length){
                var v = tr_list.shift();
                treasure_obj.initWaitList(tr_list);
                treasure_obj.init(v.trid, v.uid, v.ctime, v.nick, parseInt($ROOM.treasure.timeOut));
            }

        }
        $('#treasure_box_div').delegate('#treasure-box-content', 'click', function () {
            if (!check_login()) {
                return;
            }
            if(!check_phoneStatus(pageUser.user.phonestatus)){
                return;
            }
            if (!$(this).hasClass('receive')) {
                return;
            }
            treasure_obj.receive();
        });
    };

    room.taskInit = function () {
        $('.task').bind('click', function () {
            if (!check_login()) {
                return;
            }
            if(!check_phoneStatus(pageUser.user.phonestatus)){
                return;
            }
            taskBox.open();
        });

    };
    room.lr_other = function () {
        room.recommend_live_list();
        room.anchor_video_list();
        room.anchor_bulletin();
    };

    /*请求一次 waiting*/
    room.recommend_live_list = function () {
        var requestUrl = $conf.api + 'other/homePageGameList.php';
        // var requestUrl = './homePageGameList.php';
        var requestData = {
            uid:getCookie('_uid'),
            encpass:getCookie('_enc'),
            gameID:$ROOM.gameID,
            size:12
        };
        if(a('.liveroom_other .videoList:eq(0)').find('.liveOne').length == 0){
            ajaxRequest({url:requestUrl,data:requestData},function(d){
                var liveList = d.list;
                var html = '';
                $.each(liveList, function(key,val){
                    html += return_liveList_html(val);
                });
                if (!html) {
                    html = '<div class="nodata" style="width:260px;height:260px;margin: 50px auto;"><img src="'+$conf.domain+'../static/img/src/zone/no-live.png'+'" alt="">';
                    a('.liveroom_other .videoList:eq(0)').html(html);
                    return;
                }
                a('.liveroom_other .videoList:eq(0)').html(html);
                room.initAngleImage();
                room.lr_videolist_resize();
            });
        }
    };
    /*right*/
    room.anchor_video_list = function () {
        var requestUrl = $conf.api + 'video/getVideoList.php';
        // var requestUrl = './getVideoList.php';
        var requestData = {
            luid:$ROOM.anchorUserID,
            size:8
        };
        $('.liveRoomother_tab>li:eq(1)').click(function () {
            if(a('.liveroom_other .videoList:eq(1)').find('.liveOne').length == 0){
                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    //tips('只请求一次,视频列表');
                    if(d.total > 0){
                        var list = d.list;
                        var html = '';
                        for(var i in list){
                            html += return_videoList_html(list[i]);
                        }
                        if (html) {a('.liveroom_other .videoList:eq(1)').html(html);}
                        room.initAngleImage();
                        room.lr_videolist_resize();
                    }else{
                        var html = '<div class="nodata" style="width:260px;height:260px;margin: 50px auto;"><img src="'+$conf.domain+'../static/img/src/zone/no-video.png'+'" alt="">';
                        a('.liveroom_other .videoList:eq(1)').html(html);
                    }
                });
            }
        });

    };
    /*请求一次 waiting right*/
    room.anchor_bulletin = function () {
        var requestUrl = $conf.api + 'user/info/shamApi_getLiveBulletin.php';
        var requestData = {
            uid:$ROOM.anchorUserID
        };
        $('.liveRoomother_tab>li:eq(2)').click(function () {
            var f = $('.liveroom_other .bulletin');
            if(f.html() == ''){
                ajaxRequest({url:requestUrl,data:requestData},function(d){
                    if(d.status == 1){
                        f.html(createBulletin(d));
                    }else{
                        f.html('<div class="nodata" style="width:260px;height:225px;margin: 50px auto;"><img src="'+$conf.domain+'../static/img/logo/nodata-bulletin.png'+'" alt=""></div>');
                    }
                });
            }
        });
        function  createBulletin(d) {
            var tpl = '<p>'+d.message+'</p>';
            return tpl;
        }
    };

    room.lr_rank_list = function () {
        var tab = a('.lr_contribution_tab li');
        var tab_con = a('.lr_contribution .tabCon');
        tab.bind('click', function () {
            tab.removeClass('selected');
            tab_con.addClass('none');
            tab_con.eq(a(this).index()).removeClass('none');
            $(this).addClass('selected');

            var type = parseInt(a(this).index()) + 1;
            set_rank_list(type);
        });
    };

    room.chatopt = function () {
        room.chat_emoji();
        room.chat_clearMsg();
        room.chat_lockScreen();
        room.chat_sendMessage();
        room.chat_shieldGift();
    };
    room.chat_emoji = function () {
        var options = {
            id: 'facebox',
            path: '../static/img/emoji/',
            assign: 'inwrite',
            tip: 'em_',
            position: 'top',
            allCount: 22,
            rowCount: 8
        };
        var selector = '.emoji';
        Emoji.init(selector, options);
    };
    room.chat_clearMsg = function () {
        var clearMsg = a('.chatopt .clearMsg');
        clearMsg.bind('click', function () {
            a('#lr_chat_ul li').remove();
        });
    };
    room.chat_lockScreen = function () {
        var lockScreen = a('.chatopt .blockScreen');
        lockScreen.bind('click', function () {
            if (lockScreen.hasClass('locked')) {
                lockScreen.removeClass('locked');
                isLockScreen = false;
            } else {
                lockScreen.addClass('locked');
                isLockScreen = true;
            }
        });
    };
    room.chatMessScroll = function () {
        var lockScreen = a('.chatopt .opt_left .blockScreen');
        if (lockScreen.hasClass('locked')) {
            return false;
        }
        var scrolltop = a('.lr_chat li:last').position().top - $('.lr_chat').position().top + $('.lr_chat').scrollTop();
        $('.lr_chat').animate({scrollTop: scrolltop}, 10);
    };
    room.chat_sendMessage = function () {
        a('.lr_insend').bind('click', function () {
            var msg = a('.lr_incon').val();
            if (!check_login()) {
                return false;
            }
            if (!msg) {
                return false;
            }
            chat_obj.sendMessage(msg);
        });
        a('#inwrite').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                var msg = a('.lr_incon').val();
                if (!check_login()) {
                    return false;
                }
                if (!msg) {
                    return false;
                }
                chat_obj.sendMessage(msg);
                return false;
            }
        });
    };
    room.chat_shieldGift = function(){
        var optGroup = $("#setup");
        var shieldDiv = $(".shieldDiv");
        var shieldDiv_checkLable = shieldDiv.find('.checkbox-label');

        optGroup.bind('click', function () {
            setOptBtnActive(!$(this).hasClass('active'));
            return false;
        });
        $(document).bind('click',function(){
            setOptBtnActive(0);
        });
        optGroup.find('.opt-panel').bind('click', function(){
            return false;
        });
        shieldDiv.bind('click', function(){
            var dom = $(this).find('.checkbox-label');
            var checked = dom.hasClass('checked');
            setShieldActive(!checked);
            return false;
        });

        optGroup.find('.body .checkbox-div').bind('click', function(){
            var dom = $(this).find('.checkbox-label');
            var checked = dom.hasClass('checked');
            var domID = $(this).attr('id');

            if(checked){
                setValue(domID,false);
                dom.removeClass('checked');
            }else{
                setValue(domID,true);
                dom.addClass('checked');
            }
            if(domID =='shieldChatBanner' && shieldGift){
                if(shieldChatBanner){
                    $("#giftBetter_item1").hide();
                }else{
                    $("#giftBetter_item1").show();
                }
            }
            if(!shieldGift){
                setShieldActive(!shieldGift);
            }

            return false;
        });

        function setOptBtnActive(status){
            var method = ['removeClass','addClass'][Number(status)];
            optGroup[method]('active');
        }

        function setShieldActive(status){
            status = Number(status);
            var method = ['removeClass', 'addClass'][status];

            optGroup[method]('checked');
            shieldDiv_checkLable[method]('checked');
            shieldGift = Boolean(status);

            if(shieldGift && shieldChatBanner)
                $("#giftBetter_item1").hide();
            else
                $("#giftBetter_item1").show();
        }

        function setValue(name, value){
            try {
                var param = name.toString();
                var value = value.toString();
                eval('if( window.' + param +'!= undefined ){ window.' + param +'=' + value +'; }');
            }catch(e){
                console.log('un catch param:'+e);
            }
        }
    };
    room.chat_menushow = function () {
        var itemopen = '<div style="display:none"id="itemopen"class="user_manager"><div class="user_wrap"><span class="wrap_block"style="display: none;"><span class="anchor_icon jyIcon"></span><a class="jy"id="black_img"rel="0"onclick="black_obj.black_show(); return false;"style="">禁言</a><div class="clear"></div></span><span class="wrap_block"><span class="anchor_icon reportIcon"></span><a class="report"id="user_report">举报该弹幕</a><div class="clear"></div></span><span class="wrap_block"><span class="anchor_icon pbIcon"></span><a class="pb"id="user_black"rel="0">屏蔽该用户</a><div class="clear"></div></span><span class="wrap_block"><span class="anchor_icon rmIcon"></span><a class="rm"id="adminsetup">管理员任命</a><div class="clear"></div></span></div></div>';

        $('body').append(itemopen);
        $('#user_black').on('click', function () {
            if ($(this).attr('rel') == 0) {
                black_obj.black_myblacklist_add();
            } else {
                if ($(this).attr('rel') == 1) {
                    black_obj.black_myblacklist_del();
                }
            }
            return false;
        });
        $("#user_report").on('click', function () {
            black_obj.chat_report();
        });

        $('#lr_chat_ul').delegate('li span.lr_username', 'click', function (t) {

            clickuid = $(this).data('uid');
            var d = pageUser.isLogin ? pageUser.user.userID : gustid;
            if (!pageUser.user.userID) {
                return false;
            }

            if (d != clickuid) {
                var c = $(this).offset();
                menu_show(this);
                $('#itemopen').show().css({
                    top: c.top - $('#itemopen').outerHeight(true),
                    left: c.left
                });
            }

            return false;
        });
        $('.user_wrap').on('mouseleave', function () {
            $('#itemopen').hide();
        });
        $('#adminsetup').on('click', function () {

            if ($(this).attr('rel') == 1) {
                admindel()
            } else {
                if ($(this).attr('rel') == 4) {
                    adminreg();
                }
            }
            $('#itemopen').hide();
            return false;
        });
        $('#black_img').parent().on('click', function () {
            silencedUser();
        });
    };

    // resize window
    room.resize_win_bind = function () {
        $('.live-allcontent,.liveroom-container').height($(window).height());
        var liveRight = $('.liveroom-right');
        var LivePaR = liveRight[0].style.right;
        if(LivePaR !=  '-326px'){
            room.resize_right();
        }
    };
    room.mcscroll_bar_bind = function () {
        $('.lr_chat').mCustomScrollbar({
            scrollInertia:0,
            autoHideScrollbar:true,
            callbacks:{
                onUpdate:function(){
                    if(isLockScreen) {return;}
                    $('.lr_chat').mCustomScrollbar('scrollTo','bottom');
                }
            }
        });
        $('#liveroom-container').mCustomScrollbar({
            scrollbarPosition: "outside",
            scrollInertia:100
        });
    };
    room.live_video_notice = function () {

        $('.liveRoomother_tab>li').click(function () {
            $('.liveRoomother_tab>li').removeClass('selected');
            $(this).addClass('selected');

            var index_li = $(this).index();
            $('.tab_con').addClass('none');
            $($('.tab_con')[index_li]).removeClass('none');
        })

    };
    room.lr_videolist_resize = function () {
        var one = $('.liveroom_other .liveOne');

        if (!one[0]){
            return false;
        }

        var lvOther = $('.liveroom_other').width();
        var lvOne = 0;
        if (lvOther < 855 && lvOther >= 700) {
            one.css('width', '330px');
            one.find('.imagecontainer').css("width", '305px');
            one.find('.imagecontainer').css("height", '164px');
            one.find('.live_anchor_name').css('width', '305px');
            lvOne = 332;
        } else {
            one.css('width', '285px');
            one.find('.imagecontainer').css("width", '260px');
            one.find('.imagecontainer').css("height", '140px');
            one.find('.live_anchor_name').css('width', '260px');
            lvOne = 287;
        }

        var count = parseInt(lvOther / lvOne);

        if (count == 2) {
            var c = lvOther - count * lvOne;
            c = c / 6;

            $(".liveOne").each(function () {
                if ($(this).index() % 2 == 0) {
                    $(this).css('margin-left', c + "px");
                    $(this).css('margin-right', 2 * c + "px");
                } else {
                    $(this).css('margin-left', 2 * c + "px");
                    $(this).css('margin-right', c + 'px');
                }
            });
        } else if (count > 2) {
            var c = lvOther - count * lvOne;
            c = c / (2 * count + 1);
            $(".liveOne").each(function () {
                $(this).css({
                    'margin-left': c + 'px',
                    'margin-right': c + 'px'
                });
            });
        }
    };
    room.resize_right = function () {
        var video_fl  =   $('.liveroom_video');
        var banner  =   $('.banner');
        var lr_chat  =   $('.lr_chat');
        var Live_right =   $('.liveroom-right');

        var nav_main = $('.topfixed .nav_main');

        if($(window).width() <= 1080 && $(window).width() >= 1000){
            nav_main.addClass('roomheader_1000');
        }else if($(window).width() < 1000){
            nav_main.removeClass('roomheader_1000');
            nav_main.addClass('roomheader_980');
        }else{
            nav_main.removeClass('roomheader_1000');
            nav_main.removeClass('roomheader_980');
        }

        //flash 尺寸
        video_fl.height(video_fl.width() * 9 / 16 + "px");

        //直播content 内容
        $('.live-allcontent,.liveroom-container').height($(window).height());

        //banner
        if(banner.css('display') == 'block'){
            lr_chat.height(Live_right.height() - 475);
        }else{
            lr_chat.height(Live_right.height() - 331);
        }

        var liveroom_opt_width = $('.liveroom_opt').width();

        if( check_user_login() && liveroom_opt_width <= 730 ){
            $('.anchor_money').hide();
        }else if(check_user_login() && liveroom_opt_width > 730){
            $('.anchor_money').show();
        }
        if(liveroom_opt_width <= 730){
            $('.login_btn').hide();
        }else{
            $('.login_btn').show();
        }
        if($(window).width() <= 1150 && $('.banner').css('display') == 'none'){
            $('#liveroom-content').css('padding','0 15px 0 0');
        }else if(right_btn_flag == 0 && $(window).width() > 1150 && $('.banner').css('display') != 'none'){
            $('#liveroom-content').css('padding','0 375px 0 0');
        }

        room.lr_videolist_resize();
    };
    room.room_right_bind = function () {
        var closeBtn = $('.closeBanner');
        var banner = $('.banner');
        var rArrow_btn = $('.enlarge-block');

        $('.liveroom-container').height($(window).height());

        closeBtn.click(function () {
            banner.hide();
            room.resize_right();
        });
        //右边栏的点击事件
        rArrow_btn.click(function () {
            var liveRight = $('.liveroom-right');
            var LivePaR = liveRight[0].style.right;
            var liveRoomContent = $('#liveroom-content');
            var rightBlockDiv = $('.right_block_div');
            var toLeft = $('.toLeft');
            var arrowRight = $('.arrow_right');

            if(LivePaR ==  '-326px'){
                liveRoomContent.animate({padding:'0 375px 0 0'},200,function () {
                    right_btn_flag = 0;
                    room.resize_right();
                    rightBlockDiv.css('visibility','visible');
                    liveRight.animate({right:'24px'},200);
                    toLeft.removeClass('toLeft').addClass('arrow_right');
                });

            }
            else{
                liveRight.animate({right:'-326px'},200,function(){
                    liveRoomContent.animate({padding:'0 15px 0 0'},200,function () {
                        right_btn_flag = 1;
                        room.resize_right();
                        rightBlockDiv.css('visibility','hidden');
                        arrowRight.removeClass('arrow_right').addClass('toLeft');
                    });
                });
            }
        });

        //排行榜 日 周 总 切换
        var cont_li = $('.lr_contribution_tab>li');
        var orderList = $('.orderList');

        cont_li.click(function () {
            var index = $(this).index();

            cont_li.removeClass('selected');
            $(this).addClass('selected');

            orderList.addClass('none');
            $(orderList[index]).removeClass('none');
        });

    };


    //聊天socket登录
    room.chat_login = function (ip, port) {
        chat_obj.login(ip, port);
    };

    room.gust_status = function () {
        $('.lr_incon').remove();
        $('.get_hpbean').children().remove();
        $('.get_hpbean').html('<a class="login_get_hpbean" href="javascript:;" onclick="loginFast.login(0)"><span class="login_img"></span><span class="login_btn" id="login_btn">登录领取欢朋豆</span></a>');
        $('.anchor_money').hide();
        $('.lr_insend').before('<div class="lr_incon" style="height: 32px;width: 238px;overflow-y: hidden;"><p style="margin: 9px;color:#969798; text-align: center;">直播间发言请先 <a style="color: #FF7800;cursor: pointer;" onclick="loginFast.login(0)">登录</a>或<a style="color: #FF7800;cursor: pointer;"  onclick="loginFast.login(1)">注册</a></p></div>');
    };
    room.user_status = function () {
        $('.anchor_money_right>a').attr('href',$conf.person+'recharge.php');
        $('.lr_incon').remove();
        $('.get_hpbean #bos_show').show();
        $('.get_hpbean .login-get-hpbean').show();
        $('.anchor_money').show();
        $('.lr_insend').before('<textarea id="inwrite" placeholder="这里输入聊天内容" class="lr_incon" maxlength="30"></textarea>');
        set_user_hpbean(pageUser.user.hpbean);
        set_user_hpcoin(pageUser.user.hpcoin);

        pageUser.isAnchor && (pageUser.user.userID == $ROOM.anchorUserID) && $_GET['to_open_live'] && pushLiveBox.openTheClient.init();
    };
    room.anchorToLive = function () {


        $('.live-left .request-box a').bind('click', function () {
            if ($(this).data('liveStatus') == 'pushLive') {
                if(!isWindows()){
                    alert('请在windows上操作，当前系统不支持此功能');
                    return;
                }
                pushLiveBox.openTheClient.init();
            }
        });
    };
    room.initSwfObbject = function () {
        var anchorUserID = parseInt($ROOM.anchorUserID);
        var baseSWFObjectConf = {
            flashVersion: '9.0.0',
            install: 'expressInstall.swf',
            width: '1px',
            height: '1px',
            flashVar: {},
            params: {},
            attrbuite: {}
        };
        var embedSwfObject = [
            {
                id:'imProxy',
                file:$conf.domain +'static/chatProxy.swf'
            },
            {
                id:'rtmpplayer_room',
                file:$conf.domain + 'static/flash/rtmpplayer_js.swf',
                width:'100%',
                height:'100%',
                flashVar:{
                    'urlb': $conf.domain + 'static/flash/barrage.swf',
                    'urlw': $conf.domain + 'static/flash/wait.swf',
                    'urld': $conf.domain + 'static/flash/dot.swf',
                    'loadingURL':$conf.domain + 'static/flash/loading.swf',
                    'UIButtonURL':$conf.domain + 'static/flash/UIButton.swf',
                    'giftURL':$conf.domain + 'static/flash/gift.swf',
                    'maxCharacterNumber':50,
                    'minTimeInterval':2,
                    'UID':pageUser.isLogin ? pageUser.user.userID : 0,
                    'hostID':anchorUserID?anchorUserID:0,
                    "recommendPHP":$conf.api + 'other/flashRecommend.php',
                    'urlPHP' : $conf.api + 'live/getStreamList.php',
                    "LiveRecommendURL":$conf.domain + 'static/flash/LiveRecommend.swf'
                },
                params :{
                    quality: 'high',
                    bgcolor: '#1a1a1a',
                    allowScriptAccess: 'always',
                    allowFullScreen: 'true',
                    allowFullScreenInteractive: 'true',
                    WindowlessVideo: '1',
                    wMode: 'Opaque',
                    isLoggedIn:Number(!pageUser.isLogin)
                },
                attrbuite:{
                    allowScriptAccess: 'always',
                    allowFullScreen: 'true',
                    allowFullScreenInteractive: 'true',
                    name: 'rtmpplayer_room',
                    align: 'middle'
                }
            },
            {
                id:'getVoiceDevice',
                file:$conf.domain + 'static/flash/symvm.swf'
            }
        ];
        for (var i in embedSwfObject) {
            var item = $.extend({}, baseSWFObjectConf, embedSwfObject[i]);
            swfobject.embedSWF(item.file, item.id, item.width, item.height, item.flashVersion, item.install, item.flashVar, item.params, item.attrbuite);
        }
        //initPlayer('rtmpplayer_room',$ROOM.anchorUserID);
        runSwfFunction('rtmpplayer_room', 'setHostID',parseInt($ROOM.anchorUserID));
        //jx
        if(!swfobject.hasFlashPlayerVersion("9.0.0")){
            $('#install-flash').show();
        }
    };

    room.initAngleImage = function (){
        angleImage($conf.angleImage);
    };

    room.init();
}(jQuery);

$(window).resize(function () {
    room.resize_right();
});
