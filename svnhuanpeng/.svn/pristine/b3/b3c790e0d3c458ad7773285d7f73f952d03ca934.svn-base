/**
 * Created by hantong on 16/7/11.
 */
+function(a) {
    console.log("the page load time is : " + new Date().getTime());

    var flashVersion = '9.0.0';
    var install = 'expressInstall.swf';
    var id = '';
    var file = '';

    id = "imProxy";
    file = './static/chatProxy.swf';

    var chatProxy = a.createElement('div');
    chatProxy.id = id;
    a.body.appendChild(chatProxy);

    swfobject.embedSWF(file, id, '0px', '0px', flashVersion, install);

    var chatIp = $ROOM.chatServer[0].split(':')[0] || '';
    var chatPort = $ROOM.chatServer[0].split(':')[1] || '';
    +function (ip, port) {
        var proxy;
        var uid = "<?php echo $_COOKIE['_uid'];?>" || 3000000000;
        var encpass = "<?php echo $_COOKIE['_enc']?>" || 'gustuserenterencpass';
        var roomid = $ROOM.anchorUserID;

        var loginInterval = setInterval(function () {
            if (swfobject.getObjectById('imProxy')) {
                proxy = swfobject.getObjectById('imProxy');
                var interval = setInterval(function () {
                    try {
                        if (proxy.PercentLoaded() == 100) {
                            proxy.login(ip, port, uid, encpass, roomid, 'proxyCallBack');
                            clearInterval(interval);
                            console.log('finish load time:' + new Date().getTime());
                        }
                    } catch (e) {
                        console.log('chatObjectError:' + e);
                    }
                }, 10);
                clearInterval(loginInterval);
            }
        }, 10);
    }(chatIp, chatPort);

}(document);

window.proxyCallBack  = function(a,b){
    console.log(a);
    console.log(b);
    var obj = {};
    obj.result={
        'login.success':'loginSucc',
        'login.failed':'loginFailed',
        'sendmessage.failed':'',
        'send.failed':''
    };
    obj.receivemessage={
        '501':'welcome',
        '502':'msgShow',
        '503':'',
        '504':'giftMsg',
        '505':'silenceMsg',
        '506':'userExit',
        '511':'treasureopen',
        '535':'sendTheAir',
        '601':'liveStart',
        '602':'liveEnd',
        '701':'rRankList',
        '1100':'sendCallBack',
        '1102':'sendBeanCallBack',
        '1103':'sendGiftCallBack'
    };
    var isFunction = function(a,b,obj){
        if(obj[a] && obj[a][b] && typeof chat_obj[obj[a][b]] == 'function'){
            return true;
        }
        return false;
    };
    var h,j;
    if(a == 'receivemessage'){
        h = eval('('+b+')');
        j = h.t;
    }else if(a == 'result'){
        h = b;
        j = b;
    }
    if(isFunction(a,j,obj)){
        var runFunction = chat_obj[obj[a][j]];
        runFunction(h);
    }
    room.chatMessScroll();
};