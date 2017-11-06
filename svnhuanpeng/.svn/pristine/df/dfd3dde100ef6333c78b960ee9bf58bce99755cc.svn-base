/**
 * Created by hantong on 16/6/23.
 */

var interrupt = [];
var msgToClient = {};

!function () {
    var status = -1;
    var local = 'http://127.0.0.1';
    var portList = [8764, 8765, 8766, 8767, 8768, 8769, 8770, 8771, 8772, 8773];
    var file = '/status';//'/php/test.php';
    //var http = swfobject.getObjectById('http_proxy');

    var timeOut = 10;
    var timer = 0;

    //var httpConf = {};
    var port = 0;


    var interruptBlock = {};

    //设置关闭直播按钮
    function initStopLiveBtn(){
        $('.sidebar_list.apply_anchor a').data('liveStatus', 'closeLive').html('<span class="anchor_icon closeLiveIcon"></span>关直播');

    }
    //设置发直播按钮
    function initPushLiveBtn(){
        $('.sidebar_list.apply_anchor a').data('liveStatus', 'pushLive').html('<span class="anchor_icon pushLiveIcon"></span>发直播');
    }


    //loading tips
    var loadID = "connect-client-loading";
    var loadingShow = true;
    function loading(text, notShow){
        if(!loadingShow || notShow){
            loadingShow = !notShow;
            if(dialog.get(loadID)){
                dialog.get(loadID).close().remove();
            }
            return false;
        }
        var d = initNotice();
        var node = d.node;
        $(node).find('.loading-text').text(text);
        return d;

        function initNotice(){
            if(!dialog.get(loadID)){
                var d = dialog({
                    skin:'err-notice',
                    id:loadID,
                    content:'<span class="loading-gif"></span><p class="loading-text"></p>',
                    fixed:true
                });
                var node = dialog.get(loadID).node;
                $(node).find('.loading-gif').css({
                    'min-width': '20px',
                    height: '20px',
                    'background-image': 'url("../img/gif/load.gif")',
                    'background-repeat': 'no-repeat',
                    'display': 'inline-block',
                    'vertical-align': 'middle',
                    'background-position': '-1px 2px',
                    'margin-right':'5px'
                });
                $(node).find('.loading-text').css({
                    'display':'inline-block',
                    'margin':'0',
                    'vertical-align':'middle',
                    'line-height':'20px'
                });
                $(node).find('.ui-dialog-body').css('padding','20px 0 20px 0')
                d.showModal();
                return dialog.get(loadID);
            }
            return dialog.get(loadID)
        }
    }

    function connect() {
        loading('正在和客户端进行连接...');
        for (var i in portList) {
            msgToClient.sendMsg({
                url: local + ':' + portList[i] + file,
                data: {
                    order: 0,
                    port: portList[i]
                }
            });
        }
    }

    function start() {
        var self = msgToClient;
        msgToClient.sendMsg({
            url: local + ':' + port + file,
            dataType: 'json',
            data: {
                order: 1,
                stream: self.httpData.stream,
                server: self.httpData.server,
                quality: self.httpData.quality,
                title: self.httpData.title,
                gameName: self.httpData.gameName,
                audio: self.httpData.audio,
                uid:getCookie('_uid'),
                encpass:getCookie('_enc')
            }
        });
    }

    function run() {
        var self = msgToClient;
        self.sendMsg({
            url: local + ':' + port + file,
            data: {
                order: 2,
                isLiving: self.httpData.isLiving
            }
        });
    }

    function closeClient() {
        var self = msgToClient;
        self.sendMsg({
            url: local + ':' + port + file,
            data: {
                order: 4
            }
        });
    }

    function reset() {
        var self = msgToClient;
        self.sendMsg({
            url: local + ':' + port + file,
            data: {
                order: 5
            }
        });
    }

    var setLiveStart = false;

    function requestSetLiveStart() {
        if (setLiveStart) {
            return;
        }
        setLiveStart = true;
        $.ajax({
            url: $conf.api + 'client/liveStart.php',
            type: 'post',
            dataType: 'json',
            data: {
                uid: getCookie('_uid'),
                encpass: getCookie('_enc'),
                liveid: $ROOM.liveID
            },
            success: function (d) {
                loading('', true);
                if (d.isSuccess == 1) {
                    //设置正在直播;
                    msgToClient.httpData.isLiving = 1;
                    status = 2;
                    initStopLiveBtn();
                } else {
                    tips('直播开启失败');
                    msgToClient.order = 4;
                }
            }
        });
    }
    msgToClient.interval = '';
    msgToClient.init = function () {
        var self = this;
        self.interval = setInterval(function () {
            if (timer >= timeOut) {
                self.timeOutHandle();
                //msgToClient.timeOutHandle();
                //alert('与客户端连接超时');
                clearInterval(self.interval);
            } else {
                if (self.order === 0) {
                    connect();
                } else if (self.order === 1) {
                    start();
                } else if (self.order == 2) {
                    run()
                } else if (self.order == 4) {
                    closeClient();
                } else if (self.order == 5) {
                    reset();
                }

            }
            timer++;
        }, 3000);
    }

    msgToClient.order = -1;


    msgToClient.httpData = {
        isLiving: 0,
        stream: '',
        server: '',
        title: '',
        quality: '',
        gameName: '',
        audio: ''
    };

    //直播开启
    msgToClient.run = function (d) {

        var self = this;
        self.order = 0;
        self.httpData.isLiving = 0;
        self.httpData.stream = d.stream;
        self.httpData.server = d.server;
        self.httpData.title = d.title;
        self.httpData.gameName = d.gameName;
        self.httpData.quality = d.quality;
        self.httpData.audio = d.audio;
        self.init();
    };

    //关闭直播
    msgToClient.close = function (d) {
        var self = this;
        self.order = 4;
    }

    // 复位
    msgToClient.reset = function () {
        var self = this;
        self.order = 5;
    }

    msgToClient.sendMsg = function (d) {
        console.log(d);
        //http.send(httpConf, 'http_proxy_callBack');
        $.ajax($.extend({}, d, {
            dataType: 'jsonp',
            jsonp: 'jsoncallback',
            success: function (d) {
                msgToClient.callBackFunc(d);
            },
            error: function (d) {
                console.log(d);
            }
        }));
    }


    msgToClient.callBackFunc = function (d) {
        var self = this;
        var clientStat = d.status;
        var error = d.error;
        var res = d.data;

        //计时器清零
        timer = 0;

        //错误处理
        if( error.code != 0) {
            //invaild status
            if(error.code == 38){
                //网页端崩溃问题
                console.log('page was break');
                if(self.order == 0 && status == -1){
                    interruptBlock.time = new Date().getTime();
                    interruptBlock.order = self.order;
                    interruptBlock.status = status;
                    interruptBlock.clientStat = clientStat;
                    interruptBlock.requestData = self.httpData;
                    interrupt.push(interruptBlock);

                    if(res.port){
                        port = res.port;
                    }

                    if(clientStat == 1 || clientStat == 2){
                        status = 2;
                        self.order = 4;
                    }else if(clientStat > 0){
                        status = 4;
                        self.order = 5;
                    }
                }
            }
        }else{
            if (clientStat == 0 && status == -1) {
                port = res.port;
                status = 0;
                self.order = 1;
            }

            if (clientStat == 1 && status == 0) {

                status = 1;
            }

            if(clientStat == 1 && status == 1){
                loading('点击客户端‘开始直播’按钮，即可开始直播');
            }

            if (clientStat == 2) {
                if (status == 1) {
                    //开始直播
                    requestSetLiveStart();
                } else if (status == 2) {
                    if (setLiveStart) setLiveStart = false;
                }

                self.order = 2;
            }

            //客户端停止直播
            if (clientStat == 4 && (status == 1 || status == 2)) {
                status = 4;
                self.order = 5;
            }

            //reset
            if(clientStat == 0 && status == 4 && self.order == 5){
                self.resetStatus();

                //after reset , should be check if has interrupt block
                if(interrupt.length >= 1){
                    var block = interrupt.pop();
                    self.order = block.order;
                    status = block.status;

                    self.run(block.requestData);
                }

            }
        }
    }

    msgToClient.timeOutHandle = function(){
        var self = this;
        //与客户端链接超时
        if(status == -1){
            tips('未能找到客户端，请确保客户端开启');
            loading().close().remove();
        }else if (status == 0){
            tips('与客户端连接断开');
            loading().close().remove();
        }else{
            tips('与客户端连接断开');
        }

        self.resetStatus();
    }

    msgToClient.errorHandle = function(){
        var self = this;
        //show the error message

        self.resetStatus()
    }

    msgToClient.resetStatus = function(){
        var self = this;
        status = -1;
        self.order = -1;
        clearInterval(self.interval);
        self.interval = '';
        loadingShow = true;
        loading().close().remove();
        timer = 0;
        port = 0;
        self.httpData = {
            isLiving: 0,
            stream: '',
            server: '',
            title: '',
            quality: '',
            gameName: '',
            audio: ''
        };
        initPushLiveBtn();
    }
}();


var pushLiveBox = {};
!function (a) {
    var gameList = {};
    gameList.all = ["梦幻西游", "炉石传说", "FIFA 15", "太极熊猫", "乱斗西游", "全民奇迹", "我叫MT2", "狂野飙车8", "Real Racing 3", "Temple Run 2", "天天酷跑", "混沌与秩序对决", "刀塔传奇", "现代战争5", "Dead Trigger 2", "海岛奇兵", "部落冲突", "开心消消乐", "糖果传奇", "欢乐斗地主", "网球精英 3", "刀塔西游", "皇室战争", "火影忍者", "梦三国", "球球大作战", "全名超神", "全名枪战", "热血传奇", "时空召唤", "天天飞车", "天天炫斗", "王牌对决", "王者荣耀", "我的世界", "侠盗猎车手", "虚荣", "自由之战", "CF枪战王者"];//pageUser.gameHistoryList || [];
    gameList.hot = ["梦幻西游", "炉石传说", "FIFA 15", "太极熊猫", "乱斗西游"];//$ROOM.hotGameList || [];
    gameList.history = ["Real Racing 3", "Temple Run 2", "天天酷跑", "混沌与秩序对决", "刀塔传奇"];//$ROOM.allGame || [];

    var anchorObj = {};
    anchorObj.uid = getCookie('_uid');
    anchorObj.enc = getCookie('_enc');
    anchorObj.liveID = $ROOM.liveID;

    var searchResult = {
        index: 0,
        currVal: '',
        clear: function () {
            this.index = 0;
            this.currVal = '';
        }
    }

    function stopLiveRequest(callBack) {
        a.ajax({
            url: $conf.api + 'stopLive.php',
            type: 'post',
            dataType: 'json',
            data: {
                uid: anchorObj.uid,
                encpass: anchorObj.enc,
                liveID: $ROOM.liveID
            },
            success: function (d) {
                if (d.isSuccess) {
                    if (typeof callBack == 'function')
                        callBack();
                }
            }
        });
    }

    function initUserLiveInfo(d){
        $('.live_nav_player .player_gamedesc').text(d.title);
        $('.live_nav_player .player_otherdesc .game_name').text(d.gameName);
        room.resize();
    }

    pushLiveBox.startLive = {
        init: function () {
            var self = this;
            //check if the anchor is living
            a.ajax({
                url: $conf.api + 'isLiving.php',
                type: 'post',
                dataType: 'json',
                data: {
                    uid: anchorObj.uid,
                    encpass: anchorObj.enc,
                    // notice this is the test, in the publish version
                    // the deviceid get from the live push client
                    deviceid: anchorObj.enc
                },
                success: function (d) {
                    if (d.isSuccess) {
                        var stat = parseInt(d.stat);
                        if (stat == 1) {
                            pushLiveBox.pushLive.init(d.info);
                        } else if (stat == 0) {
                            $ROOM.liveID = d.liveid;
                            self.continueLive(d.info);
                        } else if (stat == 2) {
                            $ROOM.liveID = d.liveid;
                            self.differentLive();
                        }
                    }
                }
            });
        },
        //继续直播
        continueLive: function (data) {
            var qualityArray = ['normal', 'high'];

            var diaLog = dialog({
                skin: 'pushBox box-content err-notice',
                title: '提示',
                content: '<p>您现在正在直播，是否继续直播</p>',
                cancelValue: '取消',
                cancel: function () {
                    stopLiveRequest(function () {
                        pushLiveBox.pushLive.init(data);
                    });
                },
                okValue: '继续直播',
                ok: function () {
                    a.ajax({
                        url: $conf.api + 'continueLiving.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            uid: anchorObj.uid,
                            encpass: anchorObj.enc,
                            liveid: $ROOM.liveID,
                            deviceid: anchorObj.enc,
                            liveTitle: data.title,
                            gameName: data.gamename,
                            gameTypeID: data.gametid,
                            gameID:data.gameid,
                            videoQuality: data.quality,
                            orientation: data.orientation
                        },
                        success: function (d) {
                            if (d.isSuccess == 1) {
                                msgToClient.run({
                                    title: data.title,
                                    gameName: data.gamename,
                                    quality: qualityArray[data.quality],
                                    orientation: data.orientation,
                                    server: d.server[0],
                                    stream: d.stream
                                });
                            }
                        }
                    })
                }
            });
            diaLog.showModal();
            $('.pushBox.box-content').find('ui-dialog-close').text('');
        },
        //异地登录
        differentLive: function (data) {
            var diaLog = dialog({
                skin: 'pushBox box-content err-notice',
                title: '提示',
                content: '<p>您现在正在直播，是否继续直播</p>',
                cancelValue: '取消',
                cancel: function () {
                },
                okValue: '结束直播',
                ok: function () {
                    stopLiveRequest(function () {
                        pushLiveBox.pushLive(data);
                    });
                }
            });

            diaLog.showModal();
            $('.pushBox.box-content').find('ui-dialog-close').text('');
        }
    };

    pushLiveBox.pushLive = {
        init: function () {
            var self = this;
            var htmlObject = pushLiveBox.content;
            var content = htmlObject.pushContent() + htmlObject.share + '<div class="clear"></div>';
            var diaLog = dialog({
                title: '开始直播',
                skin: 'pushBox box-content err-notice',
                content: content,
                okValue: '下一步',
                ok: function () {
                    self.liveStart();
                }
            });
            diaLog.showModal();
            $('.game-list li span').each(function (i, e) {
                $(e).css('margin-top', -parseInt($(e).height()) / 2);
            });

            var boxModal = $('.pushBox.box-content');
            boxModal.find('.ui-dialog-close').text('');
            boxModal.find('.ui-dialog-footer').append('<a style="margin-left: 20px; color: #0064b4; float: left; margin-top: 11px" href="">直播教程</a><div class="clear"></div>').find('.ui-dialog-button').append('<div class="clear"></div>');
            if (gameList.history) {
                boxModal.find('#history-game .game-list li:eq(0)').addClass('selected');
                boxModal.find('#input-gameName').val(gameList.history[0]);
            }
            self.initGameSelectEvent();
        },
        liveStart: function () {
            var title = $('#input-liveTitle').val();
            var game = $('#input-gameName').val();
            var quality = 'high';
            var audio = 'xadsfsd';
            var orientation = 1;

            $.ajax({
                url: $conf.api + 'client/createLive.php',
                type: 'post',
                dataType: 'json',
                data: {
                    uid: anchorObj.uid,
                    encpass: anchorObj.enc,
                    deviceid: anchorObj.enc,
                    title: title,
                    gameName: game,
                    quality: quality,
                    orientation: orientation
                },
                success: function (d) {
                    if (d.liveid) {
                        $ROOM.liveID = d.liveid;
                        initUserLiveInfo({
                            title:title,
                            gameName:game
                        });
                        msgToClient.run({
                            title: title,
                            gameName: game,
                            quality: quality,
                            audio: audio,
                            server: d.server,
                            stream: d.stream
                        });
                    }
                }
            });
        },
        initGameSelectEvent: function () {
            var self = this;
            var gameDom = {
                selectItem: $('.game-select-div .game-list li'),
                input: $('#input-gameName'),
                searchResult: $(".input-search-result")
            }
            gameDom.selectItem.on('click', function () {
                gameDom.selectItem.removeClass('selected').find('.personal_icon').remove();
                $(this).addClass('selected').append('<span class="personal_icon sel"></span>');

                var game = $.trim($(this).text());
                gameDom.input.val(game);
            });

            gameDom.input.on({
                'input propertychange': function () {
                    var game = $.trim($(this).val());
                    //when write the val we should clear the searchResule data
                    searchResult.clear();
                    searchResult.currVal = game;
                    self.initSearchResult(game);
                },
                keydown: function (event) {
                    event = event ? event : window.event;

                    gameDom.searchResultItem = gameDom.searchResult.find('li');
                    var length = gameDom.searchResultItem.get().length;
                    if (event.which == 38) {
                        searchResult.index--;
                        selectItemMove();
                        return false;
                    }
                    if (event.which == 40) {
                        console.log(searchResult.index);
                        searchResult.index++;
                        selectItemMove();
                        return false;
                    }
                    if (event.which == 13) {
                        var game = $.trim(gameDom.input.val());
                        self.searchResultListClose(true);
                        self.searchSelectList(game);
                    }
                    function selectItemMove() {
                        if (length <= 0) return;

                        var tmp = (searchResult.index) % (length + 1);
                        console.log(tmp);
                        if (tmp == 0) {
                            gameDom.searchResultItem.removeClass('hover');
                            gameDom.input.val(searchResult.currVal);
                        } else {
                            tmp = tmp < 0 ? tmp : tmp - 1;
                            gameDom.searchResultItem.removeClass('hover').eq(tmp).addClass('hover');
                            gameDom.input.val(gameDom.searchResultItem.eq(tmp).text());
                        }
                    }
                }
            });
            gameDom.searchResult.delegate('li', 'mouseenter', function () {
                gameDom.searchResult.find('li').removeClass('hover');
                $(this).addClass('hover');
            }).delegate('li', 'mouseleave', function () {
                gameDom.searchResult.find('li').removeClass('hover');
            });

        },
        initSearchResult: function (game) {
            var self = this;
            var resultDom = $('.input-search-result');
            var resultList = returnSearchGameResultList(game);
            var inputDom = $('#input-gameName');

            self.searchResultListClose();
            resultDom.removeClass('none');
            if (!resultList) return;

            for (var i in resultList) {
                resultDom.append('<li>' + resultList[i] + '</li>');
            }

            resultDom.find('li').bind('click', function () {
                var game = $.trim($(this).text());
                inputDom.val(game);
                self.searchResultListClose(true);
                self.searchSelectList(game);
            });

            function returnSearchGameResultList(game) {
                var gameall = gameList.all;
                var list = [];
                if (!game) return list;

                for (var i in gameall) {
                    var reg = RegExp(game);
                    if (reg.test(gameall[i]))
                        list.push(gameall[i]);
                }

                return list;
            }
        },
        searchResultListClose: function (clearData) {
            $(".input-search-result").addClass('none').find('li').remove();
            if (clearData)
                searchResult.clear();
        },
        searchSelectList: function (game) {
            $('.game-list li').removeClass('selected').find('.personal_icon').remove();
            var selectObj = {
                dom: '',
                list: []
            }
            if (gameList.history) {
                selectObj.dom = $('#history-game .game-list li');
                selectObj.list = gameList.history;
            } else {
                selectObj.dom = $('#hot-game .game-list li');
                selectObj.list = gameList.hot;
            }
            var index = selectObj.list.indexOf(game);
            if (index > -1)
                selectObj.dom.eq(index).addClass('selected').append('<span class="personal_icon sel"></span>');
        },
        share: function () {

        }

    };

    pushLiveBox.stopLive = {
        stopBox: null,
        init: function () {
            var self = this;
            var diaLog = dialog({
                skin: 'pushBox box-content err-notice',
                title: '提示',
                content: '<p>是否要结束直播</p>',
                cancelValue: '点错了',
                cancel: function () {
                },
                okValue: '结束直播',
                ok: function () {
                    self.stopNotify();
                }
            });

            diaLog.showModal();
            self.stopBox = diaLog;
            var boxModal = $('.pushBox.box-content');
            boxModal.find('.ui-dialog-close').text('');
        },
        stopNotify: function () {
            var self = this;
            self.stopBox.close().remove();
            a.ajax({
                url: $conf.api + 'stopLive.php',
                type: 'post',
                dataType: 'json',
                data: {
                    uid: anchorObj.uid,
                    encpass: anchorObj.enc,
                    liveID: anchorObj.liveID
                },
                success: function (d) {
                    if (d.isSuccess == 1) {
                        msgToClient.close();
                        requestMyLiveInfo();
                    }
                }
            });

            function requestMyLiveInfo() {
                a.ajax({
                    url: $conf.api + 'endLiveForApp.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: anchorObj.uid,
                        encpass: anchorObj.enc,
                        liveId: anchorObj.liveID
                    },
                    success: function (d) {
                        var data = d.info;
                        var content = pushLiveBox.content.stopNotify(data);
                        var diaLog = dialog({
                            skin: 'pushBox box-content err-notice live-stop-notify',
                            title: '结束直播',
                            content: content,
                            okValue: '完成',
                            ok: function () {
                                if (!$('#isAutoPublish').is(':checked')) {
                                    requestAutoPublish();
                                }
                                self.share();
                            }
                        });
                        diaLog.show();
                        $('.pushBox .box-content').find('ui-dialog-close').text('');
                        $('.pushBox .box-content .ui-dialog-footer .ui-dialog-button').before('<div class="auto-publish-div"> <div class="checkbox-div"> <input id="isAutoPublish" type="checkbox" class="none" checked="checked"/> <label class="checkbox-label" for="isAutoPublish"></label> </div> <a href="javascript:;">点播视频生成后自动发布</a> </div>');

                        $('.auto-publish-div').bind('click', function () {
                            var checkbox = document.getElementById('isAutoPublish');
                            if (!checkbox) return;

                            checkbox.checked = !checkbox.checked;
                        });
                    }
                });
            }

            function requestAutoPublish() {
                a.ajax({
                    url: $conf.api + 'app/liveEnd.php',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        uid: anchorObj.uid,
                        encpass: anchorObj.enc,
                        liveId: anchorObj.liveID,
                        publish: 0
                    }
                });
            }
        },
        share: function () {

        }

    }

    pushLiveBox.content = {
        share: '<div class="dialog-right share-to-friend"> <p class="share-title">通知好友前来围观</p> <div class="share-channel"> <ul> <li class="share-weibo"></li> <li class="share-qq"></li> <li class="share-qzone"></li> <li class="share-weixin"><div class="shareToWx none"> <div class="qrcode"> <img src="../img/src/qrcode/qrcode_home.png" alt=""/> </div> <span class="share_icon wx-icon"></span> </div></li> </ul> </div> </div>',
        pushContent: function () {
            return '<div class="dialog-left"> <div class="control-group"> <div class="control-label">直播标题：</div> <div class="controls"> <input id="input-liveTitle" type="text"/> </div> </div> <div class="control-group"> <div class="control-label">选择游戏：</div> <div class="controls"> <input id="input-gameName" type="text"/> <ul class="input-search-result none"> </ul></div> </div><div class="row-fluid live-game-select">' + return_historyHtml() + return_hotHtml() + ' </div> </div>';

            function return_historyHtml() {
                var his = gameList.history;
                if (his.length <= 0)
                    return '';

                var html = '';
                for (var i in his) {
                    html += '<li> <span>' + his[i] + '</span></li>';
                }

                return '<div class="row-fluid game-select-div" id="history-game"><div class="line-border"><span class="title">历史纪录</span></div><ul class="game-list">' + html + '</ul></div>';
            }

            function return_hotHtml() {
                var hot = gameList.hot;
                if (hot.length <= 0) {
                    return '';
                }

                var html = '';
                for (var i in hot) {
                    html += '<li> <span>' + hot[i] + '</span></li>';
                }

                return '<div class="row-fluid game-select-div" id="hot-game"> <div class="line-border"><span class="title">热门游戏</span></div><ul class="game-list">' + html + '</ul></div>';
            }
        },
        stopNotify: function (data) {
            var user = {};
            user.face = data.pic;
            user.nick = data.nick;
            user.level = 'anchorLvl-icon lv' + data.level;
            user.fans = data.follow + '人关注';

            var anchorInfo = '<div class="anchor-info"> <div class="face"> <img src="' + user.face + '" alt=""/> </div> <div class="nick-content"> <span class="' + anchor.level + '"></span> <span class="nick">' + user.nick + '</span> </div> <div class="fansCount">' + user.fans + '</div></div>';

            var live = {};
            live.time = data.livelong;
            live.viewer = data.peak;
            live.coin = data.coin;
            live.bean = data.bean;

            var liveinfo = '<div class="live-info"> <div class="info-one"> <div class="num">' + live.time + '</div> <div class="label">直播时常</div> </div> <div class="info-one"> <div class="num">' + live.viewer + '</div> <div class="label">观众人数</div> </div> <div class="info-one" style="margin-top: 40px"> <div class="num">' + live.coin + '</div> <div class="label">金币收益</div> </div> <div class="info-one" style="margin-top: 40px"> <div class="num">' + live.bean + '</div> <div class="label">金豆收益</div> </div> <div class="clear"></div> </div>';

            return '<div class="dialog-left live-end-notify">' + anchorInfo + liveinfo + '</div>';
        }
    };
}(jQuery)




