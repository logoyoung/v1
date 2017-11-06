'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/** Created by junxiao on 2017/7/28. */
$(function () {
    var END_FIXED = '\r\n';
    var HEART_CONTENT = 'y8vPLwAA'; // 心跳码
    var HEART_TIMER = 15 * 1000; //15s 正常16s 发送心跳、避免延迟 提高一秒
    var LINK_TIMER = 5 * 1000; //5000ms 重连
    var COMMAND_MAP = {
        'result': 'sendCallback',
        'receivemessage': 'receiveMessage',
        'network.error': 'netError',
        'SecurityError': 'securityError'
    };
    var DEFAULT_MESS_MAP = {
        'login.success': '登陆成功',
        'send.success': '发送成功'
    };
    var SOCKET_CODE_MAP = {
        '501': 'welcome',
        '502': 'msgShow',
        '503': '',
        '504': 'giftMsg',
        '505': 'silenceMsg',
        '506': 'userExit',
        '507': 'follow', //关注
        '508': 'share', //分享
        '511': 'treasureopen',
        '535': 'sendTheAir',
        '540': 'liveNotice',
        '541': 'liveStop',
        '542': 'killUser',
        '601': 'liveStart',
        '602': 'liveEnd',
        '701': 'rRankList',
        '1100': 'sendCallBack',
        '1102': 'sendBeanCallBack',
        '1103': 'sendGiftCallBack',
        '1104': 'succEnterCallBack'
    };
    var ROOM_CODE_ERROR = {
        '-31': "用户欢朋币余额不足",
        '-32': "用户欢朋豆余额不足",
        '-33': "尚未认证手机",
        '-3001': '聊天室初始化失败',
        '-3002': "聊天室进入失败",
        '-3003': "聊天室退出失败",
        '-3004': "聊天室心跳失败",
        '-3005': "聊天室消息类型非法",
        '-3006': "聊天室消息发送失败",
        '-3007': "聊天室点赞失败",
        '-3008': "聊天室游客不能说话",
        '-3009': "用户被禁言，不能发言",
        '-3501': "消息表写入失败",
        '-3502': "用户发言失败",
        '-3503': "获取发言用户信息失败",
        '-3504': "获取直播信息失败",
        '-3505': "更新直播赞失败",
        '-3506': "点赞消息发送失败",
        '-3507': "获取主播信息失败",
        '-3508': "礼物全局通知信息发送失败",
        '-3509': "礼物房间信息发送失败",
        '-3510': "服务器繁忙",
        '-3511': "送礼请求参数错误",
        '-3512': "输入字符不能为空",
        '-3513': "输入字符不能超过20个字",
        '-3530': "输入字符含有敏感词汇"
    };
    var webSocketUrl = '';
    var giftList = {};
    /**
     * ROOM 工具类
     */

    var ROOM = function () {
        function ROOM() {
            _classCallCheck(this, ROOM);
        }

        _createClass(ROOM, null, [{
            key: 'chat_ms_check',
            value: function chat_ms_check(a) {
                if (!DEFAULT_MESS_MAP[a.content]) {
                    if (a.enc == 'no' && COMMAND_MAP[a.command]) {
                        return;
                    }
                    // (解密 socket)
                    if (a.command == 'receivemessage' && a.content) {
                        if (a.enc == 'yes') {
                            var data = recive_ms_decode(a.content);
                            //需 func 处理 JSON串
                            this.chat_ms_trans(data);
                        } else if (a.enc == 'no') {
                            var _data = base64.decode(a.content);
                            this.chat_ms_trans(_data);
                        }
                    }
                }
            }
        }, {
            key: 'chat_ms_trans',
            value: function chat_ms_trans(d) {
                if (d) {
                    var data = toJSON(d);
                    if (SOCKET_CODE_MAP[data.t]) {
                        eval('this.' + SOCKET_CODE_MAP[data.t] + '(' + d + ')');
                    } else {}
                }
            }
        }, {
            key: 'welcome',
            value: function welcome(d) {
                if (d.uid == ROOMID || d.nn == '') {
                    return;
                }
                console.log(d);
                var tpl = ROOM.welcomeDom(d);
                $('#chat-div').append(tpl);
                domScroll();
            }
        }, {
            key: 'msgShow',
            value: function msgShow(d) {
                /*if (!barragerLock) {
                 createBarrager(toEncode(d.msg));
                 }*/
                console.log(d);
                var tpl = ROOM.msgDom(d);
                $('#chat-div').append(tpl);
                domScroll();
            }
        }, {
            key: 'giftMsg',
            value: function giftMsg(d) {
                //手机 房管 Level 身份标识  msgid  wait
                console.log(d);
                var tpl = ROOM.giftDom(d);
                $('#chat-div').append(tpl);
                domScroll();
            }
        }, {
            key: 'silenceMsg',
            value: function silenceMsg(d) {
                console.log(d);
                var tpl = ROOM.silenceDom(d);
                $('#chat-div').append(tpl);
                domScroll();
            }
        }, {
            key: 'userExit',
            value: function userExit(d) {/*console.log('one person exit');*/}
        }, {
            key: 'follow',
            value: function follow(d) {
                console.log(d);
                var tpl = ROOM.followDom(d);
                $('#chat-div').append(tpl);
                domScroll();
            }
        }, {
            key: 'share',
            value: function share(d) {
                console.log(d);
            }
        }, {
            key: 'treasureopen',
            value: function treasureopen(d) {
                // console.log(d);
                //宝箱开启 暂无
            }
        }, {
            key: 'sendTheAir',
            value: function sendTheAir(d) {
                //console.log(d);
                //送飞船动画 暂无
            }
        }, {
            key: 'liveNotice',
            value: function liveNotice(d) {
                //console.log(d);
                //H5直播提示 暂无
            }
        }, {
            key: 'liveStop',
            value: function liveStop(d) {
                console.log(d);
                ROOM.liveEnding();
            }
        }, {
            key: 'killUser',
            value: function killUser(d) {
                console.log(d);
                ROOM.liveEnding();
            }
        }, {
            key: 'liveStart',
            value: function liveStart(d) {
                console.log(d);
                ROOM.anchorStart();
            }
        }, {
            key: 'liveEnd',
            value: function liveEnd(d) {
                console.log(d);
                var tpl = ROOM.liveEnding();
                $('#chat-div').append(tpl);
                domScroll();
                setTimeout(function () {
                    location.href = location.href;
                    //config();
                }, 3000);
            }
        }, {
            key: 'rRankList',
            value: function rRankList(d) {
                console.log(d);
                //排行榜暂无
            }
        }, {
            key: 'sendCallBack',
            value: function sendCallBack(d) {
                console.log(d);
                if (ROOM_CODE_ERROR[d.e]) {
                    mobileTips(ROOM_CODE_ERROR[d.e], 3);
                }
            }
        }, {
            key: 'sendBeanCallBack',
            value: function sendBeanCallBack(d) {
                //giftLock = false;
                console.log(d);
                setProperty(d);
                if (ROOM_CODE_ERROR[d.e]) {
                    mobileTips(ROOM_CODE_ERROR[d.e], 3);
                }
            }
        }, {
            key: 'sendGiftCallBack',
            value: function sendGiftCallBack(d) {
                //giftLock = false;
                console.log(d);
                setProperty(d);
                if (ROOM_CODE_ERROR[d.e]) {
                    mobileTips(ROOM_CODE_ERROR[d.e], 3);
                }
            }
        }, {
            key: 'succEnterCallBack',
            value: function succEnterCallBack(d) {
                console.log(d);
            }
        }, {
            key: 'welcomeDom',
            value: function welcomeDom(d) {
                return '<li class="chat-cell"><img class="welcome" src="images/emoji/welcome.png"><span class="text">&nbsp;欢迎&nbsp;</span>' + userLvIcon(d) + '<span class="person-name normal">' + d.nn + '</span><span class="text">&nbsp;进入直播间</span></li>';
            }
        }, {
            key: 'msgDom',
            value: function msgDom(d) {
                return '<li class="chat-cell">' + userGroup(d) + userLvIcon(d) + sayUser(d) + sayMessage(d) + '</li>';
            }
        }, {
            key: 'giftDom',
            value: function giftDom(d) {
                return '<li class="chat-cell">' + userGroup(d) + userLvIcon(d) + sendUser(d) + sendContent(d) + sendTimes(d) + '</li>';
            }
        }, {
            key: 'followDom',
            value: function followDom(d) {
                return '<li class="chat-cell">' + userGroup(d) + userLvIcon(d) + followUser(d) + '</li>';
            }
        }, {
            key: 'silenceDom',
            value: function silenceDom(d) {
                return '<li class="chat-cell" style="color:#f44336;">' + silenceM(d) + '</li>';
            }
        }, {
            key: 'liveEnding',
            value: function liveEnding() {
                return '<li class="chat-cell" style="color:#f44336;"><span class="message" style="color:#f44336;">系统广播：直播结束</span></li>';
            }
        }, {
            key: 'anchorStart',
            value: function anchorStart() {
                location.href = location.href;
                //config();
            }
        }]);

        return ROOM;
    }();

    var connect = function connect() {
        //websocket Events override
        var webSocket = new WebSocket(webSocketUrl);
        webSocket.onopen = function (e) {
            console.log('与服务器端建立连接');
            var LOGIN_MESSAGE = 'command=login' + END_FIXED + 'uid=' + UID + END_FIXED + 'encpass=' + ENCPASS + END_FIXED + 'roomid=' + ROOMID + END_FIXED;
            //尝试登陆
            webSocket.send(LOGIN_MESSAGE);
        };
        webSocket.onclose = function (e) {
            setTimeout(function () {
                connect();
                console.log('正在重连...');
            }, LINK_TIMER);
            console.log('链接已断开');
        };
        webSocket.onerror = function (e) {
            connect();
        };
        webSocket.onmessage = function (e) {
            var mes = QuerySocketStr(e.data);
            if (mes.content.includes('login.success')) {
                openLock = true;
                webSocket.heartBeat();
            } else if (mes.content.includes('login.fail')) {
                openLock = false;
                webSocket.onclose();
            }
            ROOM.chat_ms_check(mes);
        };
        //heartbeat WebSocket心跳
        webSocket.heartBeat = function (e) {
            var HEART_MESSAGE = 'command=sendmessage' + END_FIXED + 'content=' + HEART_CONTENT + END_FIXED;
            setInterval(function () {
                webSocket.send(HEART_MESSAGE);
            }, HEART_TIMER);
        };

        // 发送消息
        $('#sendMessage-btn').on('tap', function () {
            if (!loginEvent()) {
                return;
            }

            var messageContent = $('#message-content');
            var value = messageContent.val();
            if (value != '') {

                /***/
                webSocket.send(send_ms_encode(value));
                $('.mask').hide() && $('.chat-box, .body').removeClass('int') && $('.chat-btn .input').blur() && $('#message-content').val('');
            } else {
                return;
            }
        });
        //送礼
        $('.lw_item').on('tap', function () {
            if (!loginEvent()) {
                return;
            }
            var gid = $(this).attr('data-gid') ? $(this).attr('data-gid') : null;
            var num = $(this).attr('data-num') ? $(this).attr('data-num') : 1;
            if (gid && num) {
                webSocket.send(send_gift_encode(gid, num));
                //giftLock = true;
            }
        });
    };

    /**
     *@param: 礼物锁、socket锁
     */
    var giftLock = false,
        openLock = false,
        beanLock = false,
        geetestLock = false,
        beanTime = 0,
        bean_level = 1,
        UID = null,
        ENCPASS = null,
        ROOMID = null,
        LIVEID = null,
        $ROOM = null;
    //配置UID ENC LUID等变量
    var config = function config() {
        UID = getCookie('_uid') || 3000000000;
        ENCPASS = getCookie('_enc') || 'gustuserenterencpass';
        ROOMID = QueryLocation().luid || QueryLocation().u || ''; //luid = ROOMID
        LIVEID = QueryLocation().liveid || '';
        if (ROOMID == '') {
            return;
        }
        var requestUrl = $conf.api + 'room/getLiveRoomInfo.php';
        var requestData = {
            uid: getCookie('_uid') || '',
            encpass: getCookie('_enc') || '',
            luid: ROOMID
        };
        ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
            console.log(responseData);
            /*if(IsPC()){
                location.href = $conf.domain + responseData.roomID;
                return;
            }*/
            $ROOM = responseData;
            ROOMID = $ROOM.luid; //覆盖ROOMID
            LIVEID = $ROOM.liveID; //覆盖LIVEID
            var index = parseInt(Math.random().toFixed(0));
            console.log('webSoket[' + index + ']');
            if (!$ROOM.web_socket[index]) {
                webSocketUrl = 'ws://' + $ROOM.web_socket[0];
            } else {
                webSocketUrl = 'ws://' + $ROOM.web_socket[index];
            }

            LiveRoom.init();
        }, function (responseData) {
            mobileTips(responseData.desc, 3);
        });
    };
    //初始化 房间信息
    var initInfo = function initInfo() {

        //初始化标题
        $('header>span').html($ROOM.title);
        document.title = $ROOM.title;
        //初始化 直播状态及封面图
        if ($ROOM.isLiving == '0') {
            $('.liveroom-content').find('.play-btn').hide();
            $('.liveroom-content').find('.nolive').show();

            $('.liveroom-content').find('.poster').show().addClass('filter').attr('src', $ROOM.anchor.head);
        } else if ($ROOM.isLiving == '1') {
            $('.liveroom-content').find('.nolive').hide();
            $('.liveroom-content').find('.play-btn').show();
            $('.liveroom-content>.video').attr('poster', $ROOM.poster);
            if ($ROOM.orientation == '0' && $ROOM.poster != '') {
                $('.liveroom-content').find('.poster').removeClass('filter').removeClass('landscape').addClass('portrait').attr('src', $ROOM.poster);
            } else if ($ROOM.orientation == '1' && $ROOM.poster != '') {
                $('.liveroom-content').find('.poster').removeClass('filter').removeClass('portrait').addClass('landscape').attr('src', $ROOM.poster);
            } else {
                $('.liveroom-content').find('.poster').hide();
            }
        }

        //初始化 主播信息
        //头像
        $('.anchor-info').find('.room-head').attr('src', $ROOM.anchor.head);
        //昵称
        $('.anchor-info').find('.anchor-desc').html('<span class="ms-lv lv' + $ROOM.anchor.level + '"></span><span class="room-title">' + $ROOM.anchor.nick + '</span>');
        //在线人数
        $('.anchor-info').find('#viewCount').html(numberFormat($ROOM.viewCount));
        //直播游戏名称
        $('.anchor-info').find('.room-game').html($ROOM.gameName);
        //初始化 关注状态
        followStatus($ROOM.isFollow);
        //初始化 个人信息
        initUserInfo($ROOM);
        //初始化 流地址
        initStream(ROOMID);
    };

    //初始化 个人信息
    var initUserInfo = function initUserInfo(a) {
        if (!a || !a.user || !getCookie('_uid') || !getCookie('_enc')) {
            return;
        }
        var userInfo = a.user;
        if (userInfo.hpbean && userInfo.hpcoin) {
            setUserCoin(userInfo.hpcoin);
            setUserBean(userInfo.hpbean);
            $('.gift-content').find('.userMoney').addClass('show');
        }
    };

    //初始化 流地址
    var initStream = function initStream(ROOMID, status) {
        var requestUrl = $conf.api + 'live/getHlsStreamList.php';
        var requestData = {
            luid: ROOMID
        };
        ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
            if (responseData.stream != '') {
                //流地址前缀
                var streamFix = responseData.streamList[0] + '/';
                //待拼接流地址
                var waitStream = responseData.stream;
                //找到拼接位置
                var streamIndex = responseData.stream.indexOf('?');
                //待拼接内容
                var oldUrl = responseData.stream.substr(0, streamIndex);
                var rep = new RegExp(oldUrl);
                //替换地址
                var successUrl = waitStream.replace(rep, oldUrl + '/playlist.m3u8');
                //拼接
                var m3u8URL = 'http://' + streamFix + successUrl;

                $('.video').attr('src', m3u8URL);

                if (status == 1) {
                    var video = $('video')[0];
                    enableInlineVideo(video);
                    video.play();
                    $('.video').addClass('play');
                } else {
                    return;
                }
            } else {
                /* 下播状态 */

            }
        }, function (responseData) {
            //error
        });
    };

    // config => initGift =>webSocket => initInfo => initStream
    //配置UID等 =>  socket服务登录 => 初始化房间信息 => 初始化 播放流地址
    config();

    var LiveRoom = {
        init: function init() {
            this._initLogin(); //登录
            this._initLayer(); //界面
            this._initBindVideoEvent(); //直播视频相关事件
            this._initVideoPlayAddress(); //直播地址相关事件
            this._initChangeView(); //聊天 评论View切换
            this._initAnchorInfo(); //主播信息及事件
            this._initBindChatEvent(); //聊天相关事件
            this._initBeanTime();
            this._initBindBeanEvent(); //领豆相关事件
            this._initBindGiftEvent(); //送礼相关事件
        },
        _initLogin: function _initLogin() {},
        _initLayer: function _initLayer() {
            //返回上层页面
            bindGesture($('#backToView'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                $(this).removeClass('high').addClass('normal');
            }, function () {
                if (history.length == 1 || window.history.length == 1) {
                    location.href = $conf.domain + 'mobile/index.html';
                } else {
                    window.history.go(-1);
                }
            });

            //emoji
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination'
            });
            //emoji int
            $('#emoji-content').find('.emoji').on('tap', function () {
                var index = $(this).index() + 1;
                var emojiIndex = '[em_' + index + ']';

                var textObj = $('#message-content').get(0);
                if (document.all && textObj.createTextRange && textObj.caretPos) {
                    var caretPos = textObj.caretPos;
                    caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? emojiIndex + '' : emojiIndex;
                } else if (textObj.setSelectionRange) {
                    var rangeStart = textObj.selectionStart;
                    var rangeEnd = textObj.selectionEnd;
                    var tempStr1 = textObj.value.substring(0, rangeStart);
                    var tempStr2 = textObj.value.substring(rangeEnd);
                    textObj.value = tempStr1 + emojiIndex + tempStr2;
                    textObj.focus();
                    var len = emojiIndex.length;
                    textObj.setSelectionRange(rangeStart + len, rangeStart + len);
                    textObj.blur();
                } else {
                    textObj.value += emojiIndex;
                }
            });
        },
        _initBindVideoEvent: function _initBindVideoEvent() {
            //播放 按钮 事件
            bindGesture($('.liveroom-content>.play-btn'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                //touchend
                $(this).removeClass('high').addClass('normal');
            }, function () {
                //tap
                $('.liveroom-content>.video')[0].play();
                $('.video').addClass('play');
                if (window.navigator.userAgent.match(/OS [9]_\d[_\d]* like Mac OS X/i) || window.navigator.userAgent.match(/OS [8]_\d[_\d]* like Mac OS X/i)) {
                    $('.liveroom-content>.video')[0].play();
                    $('.video').addClass('play');
                    $('.liveroom-content>.video')[0].webkitEnterFullScreen();
                }
                setTimeout(function () {
                    $('.liveroom-content>.play-btn').hide();
                    $('.video-bar').addClass('show');
                    setTimeout(function () {
                        $('.video-bar').removeClass('show');
                    }, 5000);
                    $('.liveroom-content>.poster').hide();
                }, 1000);
            });

            //video-bar display
            $('.liveroom-content>.video').tap(function () {
                var videoBar = $('.video-bar');
                var videoPlayBtn = $('.liveroom-content>.play-btn');
                if (videoPlayBtn.css('display') == 'none') {
                    videoBar.hasClass('show') ? videoBar.removeClass('show') : videoBar.addClass('show') && setTimeout(function () {
                        videoBar.removeClass('show');
                    }, 5000);
                }
            });

            //video pause
            bindGesture($('.video-bar>.video-pause'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                //touchend
                $(this).removeClass('high').addClass('normal');
            }, function () {
                //tap
                $('.liveroom-content>.video')[0].pause();
                $('.video-bar').removeClass('show') && $('.play-btn').show();
            });

            $('.video-refresh').on('tap', function () {
                initStream(ROOMID, 1);
            });

            //video webkit-fullscreen
            bindGesture($('.video-bar>.video-full'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                //touchend
                $(this).removeClass('high').addClass('normal');
            }, function () {
                //tap
                $('.liveroom-content>.video')[0].webkitEnterFullScreen();
            });
        },
        _initVideoPlayAddress: function _initVideoPlayAddress() {
            var video = $('video').get(0);
            enableInlineVideo(video);
        },
        _initChangeView: function _initChangeView() {
            // 聊天 推荐 下划线 事件添加
            $('#chatView-btn').on('tap', function () {
                $('.sec-box li span').removeClass('sel');
                $(this).addClass('sel');
                $('.commend-selbox').hide() && $('.chat-selbox, .chat-box').show();
            });
            $('#commendView-btn').on('tap', function () {
                $('.sec-box li span').removeClass('sel');
                $(this).addClass('sel');
                $('.chat-selbox, .chat-box').hide() && $('.commend-selbox').show();
                if ($('.commend-selbox .live-list').find('.live-one').length == 0) {
                    var requestUrl = $conf.api + 'other/guessYouLike.php';
                    var requestData = {
                        uid: getCookie('_uid') || '',
                        encpass: getCookie('_enc') || '',
                        size: 6
                    };
                    var htmlArr = [];
                    ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
                        if (responseData.list && responseData.list.length > 0) {
                            $('.commend-selbox .commend-title').show();
                        }
                        var resList = responseData.list;
                        console.log(resList);
                        for (var i = 0; i < resList.length; i++) {
                            htmlArr.push(CreateDom(resList[i]));
                        }

                        $('.commend-selbox').find('.live-list').html(htmlArr);
                    }, function (responseData) {
                        mobileTips(responseData.desc, 3);
                    });
                    $('.commend-selbox .live-list').html(htmlArr);
                }
            });

            function CreateDom(item) {
                if (item.uid == ROOMID) {
                    return '';
                }
                if (item.orientation == 1) {
                    return '<li class="live-one">\
                            <a href="' + $conf.domain + 'mobile/room/room.html?luid=' + item.uid + '">\
                            <div class="poster">\
                            <div class="snapshot">\
                            <img class="horizontal" src="' + item.poster + '">\
                            <p class="game-title">' + item.gameName + '</p>\
                        </div>\
                        <div class="profile-photo">\
                            <img src="' + item.head + '" alt="">\
                            </div>\
                            <div class="poster-info">\
                            <p class="poster-name">' + item.title + '</p>\
                        <p class="audience">' + numberFormat(item.userCount) + '人</p>\
                        </div>\
                        <section class="room-name">' + item.nick + '</section>\
                        </div>\
                        </a>\
                        </li>';
                } else {
                    return '<li class="live-one">\
                            <a href="' + $conf.domain + 'mobile/room/room.html?luid=' + item.uid + '">\
                            <div class="poster">\
                            <div class="snapshot">\
                            <img class="vertical" src="' + item.poster + '">\
                            <p class="game-title">' + item.gameName + '</p>\
                        </div>\
                        <div class="profile-photo">\
                            <img src="' + item.head + '" alt="">\
                            </div>\
                            <div class="poster-info">\
                            <p class="poster-name">' + item.title + '</p>\
                        <p class="audience">' + numberFormat(item.userCount) + '人</p>\
                        </div>\
                        <section class="room-name">' + item.nick + '</section>\
                        </div>\
                        </a>\
                        </li>';
                }
            }
        },
        _initAnchorInfo: function _initAnchorInfo() {
            //主播信息 按钮 事件
            bindGesture($('.anchor-display'), function (e) {
                //touchstart
                e.preventDefault();
                $('.anchor-info').hasClass('none') ? $(this).removeClass('hide').addClass('hide-high') : $(this).removeClass('show').addClass('show-high');
            }, function () {
                //touchend
                $('.anchor-info').hasClass('none') ? $(this).removeClass('hide').addClass('hide-high') : $(this).removeClass('show-high').addClass('show');
            }, function () {
                //tap
                $('.anchor-info').hasClass('none') ? $('.anchor-info, .chat-container').removeClass('none') && $(this).removeClass('hide').removeClass('hide-high').addClass('show') : $('.anchor-info, .chat-container').addClass('none') && $(this).removeClass('show').removeClass('show-high').addClass('hide');
            });
            //关注主播 按钮事件
            $('.follow-btn').on('tap', function () {
                if (!loginEvent()) {
                    return;
                    //立即登录
                }
                if (UID && ENCPASS && ROOMID) {
                    var requestUrl = '';
                    if (!$(this).hasClass('cancel')) {
                        requestUrl = $conf.api + 'room/followUser.php';
                        var requestData = {
                            uid: UID,
                            encpass: ENCPASS,
                            luid: ROOMID
                        };
                        ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
                            followStatus(1);
                        });
                    } else {
                        requestUrl = $conf.api + 'room/followUserCancel.php';
                        var requestData = {
                            uid: UID,
                            encpass: ENCPASS,
                            luids: ROOMID
                        };
                        ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
                            followStatus(0);
                        });
                    }
                } else {
                    //else login
                }
            });
        },
        _initBindChatEvent: function _initBindChatEvent() {
            //聊天 按钮事件
            bindGesture($('.chat-btn .input'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                //touchend
                $(this).removeClass('high').addClass('normal');
                $('.emoji-content').hasClass('show') ? $('.emoji-content').removeClass('show') : '';
                $(this).focus() && $('.mask').show();
            }, function () {
                //tap
                $(this).focus();
            });

            $('.chat-btn .input').focus(function () {
                $('.chat-box ,.body').addClass('int');
            }).blur(function () {
                $('.body').removeClass('int');
            });
            //mask 取消input事件
            $('.mask').on('tap', function () {
                $(this).hide() && $('.chat-box, .body').removeClass('int') && $('.chat-btn .input').blur();
            });

            $('#emoji-content .delete-btn').on('tap', function () {
                $('#message-content').val('');
            });

            //emoji 按钮事件
            bindGesture($('.emoji-btn'), function (e) {
                e.preventDefault();
            }, function () {}, function () {
                if (!$('.emoji-content').hasClass('show')) {
                    $('.emoji-content').addClass('show') && $('.chat-btn .input').blur();
                } else {
                    $('.emoji-content').removeClass('show') && $('.chat-btn .input').focus();
                }
            });
        },
        _initBeanTime: function _initBeanTime() {
            if (!getCookie('_uid') || !getCookie('_enc')) {
                return;
            }
            var requestUrl = $conf.api + 'room/shamApi_gb_enter.php';
            var requsetData = {
                uid: getCookie('_uid'),
                encpass: getCookie('_enc'),
                luid: ROOMID,
                event: 'enter'
            };
            ajaxRequest({ url: requestUrl, data: requsetData }, function (responseData) {
                console.log(responseData);
                bean_level = responseData.lvl;
                if (bean_level == 6) {
                    return;
                }
                if (responseData.time > 0) {
                    beanLock = false;
                    beanTime = responseData.time;
                    timeSort();
                } else {
                    beanLock = true;
                }
            }, function (responseData) {
                mobileTips(responseData.desc, 3);
            });
        },
        _initBindBeanEvent: function _initBindBeanEvent() {
            //领豆 按钮事件
            bindGesture($('.bean-btn'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                //touchend
                $(this).removeClass('high').addClass('normal');
            }, function () {
                console.log(beanLock);
                if (!loginEvent()) {
                    return;
                }
                if (bean_level == 6) {
                    var dom = '已经领取6次咯~';
                    mobileTips(dom, 3);
                    return;
                }
                if (!beanLock && beanTime > 0) {
                    var minute = parseInt(beanTime / 60);
                    var second = parseInt(beanTime % 60);

                    if (minute > 0) {
                        var time = minute + ' 分钟后可领取';
                        mobileTips(time, 1);
                        return;
                    } else {
                        var time = second + ' 秒钟后可领取';
                        mobileTips(time, 1);
                        return;
                    }
                } else {
                    if (!loginEvent()) {
                        return;
                    }
                    if (!geetestLock || $('.gt_holder').hasClass('gt_hide')) {
                        geetestLock = true;
                        geetest({ product: 'popup', append: '#geetest' }, function (data) {
                            geetestLock = false;
                            var requestData = $.extend({
                                uid: getCookie('_uid'),
                                encpass: getCookie('_enc'),
                                luid: ROOMID,
                                event: 'pick',
                                lvl: bean_level,
                                vcode: 0,
                                type: 'gt'
                            }, data);

                            var requestUrl = $conf.api + 'room/shamApi_gb_enter.php';
                            ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
                                console.log(responseData);
                                var recieve_bean = '领取了 <span>' + responseData.revCount + '</span> 个欢朋豆';
                                mobileTips(recieve_bean, 2);
                                setProperty(responseData);
                                bean_level = responseData.lvl;
                                beanTime = responseData.time;
                                timeSort();
                                beanLock = false;
                            }, function (responseData) {
                                mobileTips(responseData.desc, 3);
                            });
                        });
                    } else {
                        return;
                    }
                }
            });
        },
        _initBindGiftEvent: function _initBindGiftEvent() {
            //initGift List
            var requestUrl = $conf.api + 'gift/getGiftShowList.php';
            var requestData = {
                client: 1,
                luid: ROOMID
            };
            var CreateLi = function CreateLi(obj) {
                return '<li class="lw_item" data-gid="' + obj.id + '" data-num="' + obj.num + '">\
                          <div class="img">\
                            <img src="' + obj.poster + '">\
                            </div>\
                            <p class="desc">' + obj.giftname + '</p>\
                            <p class="price">' + obj.cost + obj.unit + '</p>\
                        </li>';
            };
            var giftEvent = function giftEvent(responseData) {
                var htmlArr = [];
                var lists = responseData.list;

                for (var i = 0; i < lists.length; i++) {
                    var listOne = lists[i];
                    giftList[listOne.id] = listOne.poster;
                    htmlArr.push(CreateLi(listOne));
                }
                $('.gift-content').find('.gift-box').html(htmlArr);
                connect();
                initInfo();
            };

            ajaxRequest({ url: requestUrl, data: requestData }, function (responseData) {
                giftEvent(responseData);
            }, function (responseData) {
                mobileTips(responseData.desc, 3);
            });
            //gift modal
            $('.modal').on('tap', function () {
                $(this).hide();
                $('.gift-content').removeClass('show');
            });
            //送礼 按钮事件
            bindGesture($('.gift-btn'), function (e) {
                //touchstart
                e.preventDefault();
                $(this).removeClass('normal').addClass('high');
            }, function () {
                $(this).removeClass('high').addClass('normal');
            }, function () {
                $('.gift-content').addClass('show') && $('.modal').show();
            });
        }
    };

    /**
     * [送礼 加密]
     * @param  {[string]} gid [需加密文字]
     */
    function send_gift_encode(gid, num) {
        //gid   礼物 个数 (gid 暂定31-39)
        if (!gid || gid < 30 || gid > 40) {
            return;
        }
        if (gid == '31' && num != '') {
            return send_bean_encode(num);
        }
        if (!getCookie('_uid') || !getCookie('_enc')) {
            return false;
            /**/
        }
        var content = {
            t: 103,
            enc: ENCPASS,
            gid: gid,
            liveid: LIVEID,
            identity: '1'
        };
        return str_encode(content);
    }
    function send_bean_encode(num) {
        if (!num || num < 1 || num > 10000) {
            return;
        }
        var content = {
            t: 102,
            enc: ENCPASS,
            gid: '31', //欢朋豆 暂定 31
            liveid: LIVEID,
            num: num,
            identity: '1'
        };
        return str_encode(content);
    }
    /**
     * [发送消息 加密]
     * @param  {[string]} str [需加密文字]
     */
    function send_ms_encode(str) {
        if (!str) {
            return;
        }
        if (!getCookie('_uid') || !getCookie('_enc')) {
            return false;
            /**/
        }
        var content = {
            t: 100,
            uid: UID,
            msg: toUnicode(str),
            mid: new Date().getTime(),
            way: 0
        };
        return str_encode(content);
    }
    /**
     * [接收消息 解密]
     * @param  {[string]} str [需解密文字]
     */
    function recive_ms_decode(str) {
        //解密
        var compress = String_fixed(str, 'decode');
        var recive_str = zip_inflate(base64.decode(compress));
        return recive_str;
    };
    function userGroup(a) {
        if (!a || !a.group) {
            return;
        }
        var uGp = parseInt(a.group);
        if (uGp == 1) {
            return '';
        } else if (uGp == 4) {
            return '<span class="mg">房管</span>';
        } else if (uGp == 5) {
            return '<span class="an">主播</span>';
        }
    };
    function userLvIcon(a) {
        if (!a || !a.level) {
            return;
        }
        return '<span class="us-lv lv' + a.level + '"></span>';
    };
    function sayUser(a) {
        if (!a || !a.cunn) {
            return;
        }
        return '<span class="person-name normal">' + a.cunn + ':</span>';
    };
    function sayMessage(a) {
        if (!a || !a.msg) {
            return;
        }
        return '<span class="message">' + toEncode(replace_em(a.msg)) + '</span>';
    };
    function replace_em(str) {
        str = str.replace(/\</g, '&lt;');
        str = str.replace(/\>/g, '&gt;');
        str = str.replace(/\n/g, '<br/>');
        str = str.replace(/\[em_([0-9]*)\]/g, function (word) {

            var num = word.match(/\d{1,2}/g);
            num = num[0] ? num[0] : '';
            var str = word;
            num && num < 23 && (str = '<img class="emoji" src="images/emoji/' + num + '.png" >');
            return str;
        });
        return str;
    };
    function sendUser(a) {
        if (!a || !a.ounn) {
            return;
        }
        return '<span class="person-name gift">' + a.ounn + ':</span>';
    };
    function sendContent(a) {
        //礼物img
        return '<span class="message"> 赠送给主播</span><span class="person-name gift">' + a.gname + '</span>' + giftIcon(a) + '';
    };
    function giftIcon(a) {
        if (!a || !a.gid) {
            return;
        }
        // a.gid
        return '<img class="img" src="' + giftList[a.gid] + '" >';
    };
    function sendTimes(a) {
        var time = a.timer ? '<span class="message gift">' + a.timer + '</span><span class="message fixed">连击</span>' : '<span class="message gift">' + a.gnum + '</span><span class="message fixed">粒</span>';
        return time;
    };
    function followUser(d) {
        if (!d.nick || d.nick == '') {
            return;
        }
        return '<span class="person-name normal">' + d.nick + '</span><span class="message">关注了主播</span>';
    };
    function followStatus(a) {
        if (a == '1') {
            $('.follow-btn').addClass('cancel').text('已关注');
        } else {
            $('.follow-btn').removeClass('cancel').text('关注');
        }
    };
    function silenceM(a) {
        if (!a || !a.admin || !a.adminNick) {
            return;
        }
        return '<span class="message" style="color:#f44336;">系统广播：用户</span><span class="message" style="color:#f44336;">' + a.targetNick + '</span><span class="message" style="color:#f44336;">被房管</span><span class="message" style="color:#f44336;">' + a.adminNick + '</span><span class="message" style="color:#f44336;">禁言一小时</span>';
    };
    $.fn.scrollTo = function (options) {
        var defaults = {
            toT: $('.chat-container')[0].scrollHeight, //滚动目标位置
            durTime: 1000, //过渡动画时间
            delay: 10, //定时器时间
            callback: function callback() {
                return;
            } //回调函数
        };
        var opts = $.extend(defaults, options),
            timer = null,
            _this = this,
            curTop = _this.scrollTop(),
            //滚动条当前的位置
        subTop = opts.toT - curTop,
            //滚动条目标位置和当前位置的差值
        index = 0,
            dur = Math.round(opts.durTime / opts.delay),
            smoothScroll = function smoothScroll(t) {
            index++;
            var per = Math.round(subTop / dur);
            if (index >= dur) {
                _this.scrollTop(t);
                window.clearInterval(timer);
                if (opts.callback && typeof opts.callback == 'function') {
                    opts.callback();
                }
                return;
            } else {
                _this.scrollTop(curTop + index * per);
            }
        };
        timer = window.setInterval(function () {
            smoothScroll(opts.toT);
        }, opts.delay);
        return _this;
    };
    function domScroll() {
        $('.chat-container').scrollTo();
        if ($('#chat-div').find('li').length > 88) {
            $('#chat-div').find('li').eq(0).remove();
        }
        // $('.chat-container').scrollTop($('.chat-container')[0].scrollHeight);
    };
    /* 设置 金豆金币余额 */
    function setUserCoin(a) {
        $('.gift-content').find('.userMoney').find('.coinMoney').text(digitsFormat(a));
    };
    function setUserBean(a) {
        $('.gift-content').find('.userMoney').find('.beanMoney').text(digitsFormat(a));
    };
    function setProperty(a) {
        if (!a || !a.coin || !a.bean) {
            return;
        }
        setUserCoin(a.coin);
        setUserBean(a.bean);
    };
    /* mobileTips  1:loading 2:success 3:fail*/
    function mobileTips(a, b) {
        if (b == 1) {
            $('.mobile-success, .mobile-fail').hide();
            $('.mobile-loading .loading').html(a);
            $('.mobile-tip, .mobile-loading').show();
            setTimeout(function () {
                $('.mobile-loading .loading').html('');
                $('.mobile-tip, .mobile-loading').hide();
            }, 1500);
        } else if (b == 2) {
            $('.mobile-loading, .mobile-fail').hide();
            $('.mobile-success .success').html(a);
            $('.mobile-tip, .mobile-success').show();
            setTimeout(function () {
                $('.mobile-success .success').html('');
                $('.mobile-tip, .mobile-success').hide();
            }, 1500);
        } else if (b == 3) {
            $('.mobile-success, .mobile-loading').hide();
            $('.mobile-fail .fail').html(a);
            $('.mobile-tip, .mobile-fail').show();
            setTimeout(function () {
                $('.mobile-fail .fail').html('');
                $('.mobile-tip, .mobile-fail').hide();
            }, 1500);
        }
    };
    function timeSort() {
        var interval = setInterval(function () {
            if (beanTime <= 0) {
                beanLock = true;
                clearInterval(interval);
                return;
            }
            beanTime--;
            console.log(beanTime);
        }, 1000);
    };

    /* not Login */
    function loginEvent() {
        if (!getCookie('_uid') || !getCookie('_enc')) {
            mobileTips('您还没有登录哦~', 3);
            setTimeout(function () {
                location.href = $conf.domain + 'mobile/h5login/index.html?t=' + new Date().getTime();
            }, 1200);
            return false;
        } else {
            return true;
        }
    };
});
//# sourceMappingURL=liveroom.js.map