//系统配置文件
var conf = {};
(function(){

    var _protocol = location.protocol+'//';

    var env = 'pro';
    var _domain = _protocol + 'www.huanpeng.com/';
    var cookiepath = '/';

    if( document.domain == 'dev.huanpeng.com' )
    {
        env = 'dev';
        _domain = _protocol + document.domain + '/';
        cookiepath = '/';
    }
    else if( document.domain == 'pre.huanpeng.com')
    {
        env = 'pre';
        _domain = _protocol + document.domain + '/';
        cookiepath = '/';
    }
    else if( document.domain != 'www.huanpeng.com' || document.domain != 'huanpeng.com')
    {
        env        = 'dev';
        _domain    = _protocol + document.domain + '/';
        cookiepath = '/';
    }

    // if(document.domain == 'dev.huanpeng.com'){
    //     var env = 'dev';
    //     var _domain = _protocol + document.domain + '/';
    //     var cookiepath = '/';
    // }else{
    //     var env = 'pro';
    //     var _domain = _protocol + 'www.huanpeng.com' + '/';
    //     var cookiepath = '/';
    // }

    var _api = _domain + 'api/';
    var _person = _domain + 'personal/';

    console.log(env);

    var img = [];
    img['dev'] = _protocol + 'dev-img.huanpeng.com';
    img['pre'] = _protocol + 'pre-img.huanpeng.com';
    img['pro'] = _protocol + 'img.huanpeng.com';

    var apiUrl = {

    };

    var imgSize = 2 * 1024 * 1024;
    conf = {
        getConf:function(){
            return{
                angleImage:'angle_class',
                domain : _domain,
                api : _api,
                img : img[env],
                person:_person,
                pushRoomID:1,
                maxUid:3000000000,
                group:{
                    own:5,
                    admin:4,
                    user:1
                },
                taskUrl:{
                    6:_person,
                    12:_person + 'mp/certify_email/',
                    30:_person + 'recharge.php',
                    36:_domain + 'download.php'
                },
                modifyNickCost:600,
                uploadImgSize: imgSize,
                video:{
                    WAIT:0,
                    CHECK:1,
                    PUBLISH:2
                },
                defaultFace:img[env] + "/5/e/5e49f1310263dae8f0bc3f484860f2ad.png",
                defaultUserPic:_domain+"static/img/userface.png",
                certStatus:{
                    mail:{
                        not:0,
                        wait:1,
                        pass:2
                    },
                    phone:{
                        not:0,
                        pass:1
                    },
                    ident:{
                        not:0,
                        wait:1,
                        unpass:100,
                        pass:101
                    },
                    bank:{
                        not:0,
                        wait:1,
                        unpass:100,
                        pass:101
                    }
                },
                cookie:['_uid', '_enc', '_uinfo','_uproperty', '_unick','_uface','_loginway'],
                isIE:navigator.appVersion.indexOf('MSIE') > 0 || navigator.appVersion.indexOf("Trident/7.0") > 0,
                isIE7:navigator.appVersion.indexOf('MSIE 7.0') > 0,
                isFF:navigator.appVersion.indexOf('Firefox') > 0,
                cookiepath:cookiepath
            }
        }
    }

}());

var $conf = conf.getConf();

//资源配置
var $src;
(function(){
	$src = function(){
		return {
			'live_fav_980':'',
			'live_fav_1180':'',
			'live_list_980':'',
			'live_list_1180':'',
			'user_pic':''
		};
	};
}())


function geetest(conf, callBack,hideCallBack){
    function handler(captchaObj){
        captchaObj.appendTo(conf.append);
        if(conf.product == 'popup'){
            captchaObj.onReady(function(){
                captchaObj.show();
            });
            captchaObj.hide(function(){
                hideCallBack && typeof hideCallBack == 'function' && (hideCallBack());
            });
        }
        captchaObj.onSuccess(function(){
            captchaObj.hide();
            callBack && typeof callBack =='function' && (callBack(captchaObj.getValidate()));
        });
    }
    $.ajax({
        url: $conf.api + "code/geetest_api.php?rand="+Math.round(Math.random()*100),
        type:'get',
        dataType:'json',
        success:function(data){
            // 使用initGeetest接口
            // 参数1：配置参数，与创建Geetest实例时接受的参数一致
            // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                product: conf.product, // 产品形式
                offline: !data.success
            }, handler);
        }
    });
};


function ajaxRequest(conf, successFn, failedFn, errorFn){
    var defaultConf = {
        url:'',
        type:'post',
        dataType:'json',
        success:function(d){
            if(d.status == 1){
                successFn && typeof successFn=="function" && successFn(d.content);
            }else{
                failedFn && typeof failedFn=='function' && failedFn(d.content);
            }
        },
        error:function(d){
            errorFn && typeof errorFn == 'function' && errorFn();
        }
    };

    return $.ajax($.extend({},defaultConf, conf));
}

//huanpeng template
;(function(window){

    //取得浏览器环境的baidu命名空间，非浏览器环境符合commonjs规范exports出去
    //修正在nodejs环境下，采用baidu.template变量名
    var huanpeng = typeof module === 'undefined' ? (window.huanpeng = window.huanpeng || {}) : module.exports;

    //模板函数（放置于huanpeng.template命名空间下）
    huanpeng.template = function(str, data){

        //检查是否有该id的元素存在，如果有元素则获取元素的innerHTML/value，否则认为字符串为模板
        var fn = (function(){

            //判断如果没有document，则为非浏览器环境
            if(!window.document){
                return bt._compile(str);
            };

            //HTML5规定ID可以由任何不包含空格字符的字符串组成
            var element = document.getElementById(str);
            if (element) {

                //取到对应id的dom，缓存其编译后的HTML模板函数
                if (bt.cache[str]) {
                    return bt.cache[str];
                };

                //textarea或input则取value，其它情况取innerHTML
                var html = /^(textarea|input)$/i.test(element.nodeName) ? element.value : element.innerHTML;
                return bt._compile(html);

            }else{

                //是模板字符串，则生成一个函数
                //如果直接传入字符串作为模板，则可能变化过多，因此不考虑缓存
                return bt._compile(str);
            };

        })();

        //有数据则返回HTML字符串，没有数据则返回函数 支持data={}的情况
        var result = bt._isObject(data) ? fn( data ) : fn;
        fn = null;

        return result;
    };

    //取得命名空间 baidu.template
    var bt = huanpeng.template;

    //标记当前版本
    bt.versions = bt.versions || [];
    bt.versions.push('1.0.6');

    //缓存  将对应id模板生成的函数缓存下来。
    bt.cache = {};

    //自定义分隔符，可以含有正则中的字符，可以是HTML注释开头 <! !>
    bt.LEFT_DELIMITER = bt.LEFT_DELIMITER||'<%';
    bt.RIGHT_DELIMITER = bt.RIGHT_DELIMITER||'%>';

    //自定义默认是否转义，默认为默认自动转义
    bt.ESCAPE = true;

    //HTML转义
    bt._encodeHTML = function (source) {
        return String(source)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/\\/g,'&#92;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;');
    };

    //转义影响正则的字符
    bt._encodeReg = function (source) {
        return String(source).replace(/([.*+?^=!:${}()|[\]\\])/g,'\\$1');
    };

    //转义UI UI变量使用在HTML页面标签onclick等事件函数参数中
    bt._encodeEventHTML = function (source) {
        return String(source)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;')
            .replace(/\\\\/g,'\\')
            .replace(/\\\//g,'\/')
            .replace(/\\n/g,'\n')
            .replace(/\\r/g,'\r');
    };

    //将字符串拼接生成函数，即编译过程(compile)
    bt._compile = function(str){
        var funBody = "var _template_fun_array=[];\nvar fn=(function(__data__){\nvar _template_varName='';\nfor(name in __data__){\n_template_varName+=('var '+name+'=__data__[\"'+name+'\"];');\n};\neval(_template_varName);\n_template_fun_array.push('"+bt._analysisStr(str)+"');\n_template_varName=null;\n})(_template_object);\nfn = null;\nreturn _template_fun_array.join('');\n";
        return new Function("_template_object",funBody);
    };

    //判断是否是Object类型
    bt._isObject = function (source) {
        return 'function' === typeof source || !!(source && 'object' === typeof source);
    };

    //解析模板字符串
    bt._analysisStr = function(str){

        //取得分隔符
        var _left_ = bt.LEFT_DELIMITER;
        var _right_ = bt.RIGHT_DELIMITER;

        //对分隔符进行转义，支持正则中的元字符，可以是HTML注释 <!  !>
        var _left = bt._encodeReg(_left_);
        var _right = bt._encodeReg(_right_);

        str = String(str)

            //去掉分隔符中js注释
            .replace(new RegExp("("+_left+"[^"+_right+"]*)//.*\n","g"), "$1")

            //去掉注释内容  <%* 这里可以任意的注释 *%>
            //默认支持HTML注释，将HTML注释匹配掉的原因是用户有可能用 <! !>来做分割符
            .replace(new RegExp("<!--.*?-->", "g"),"")
            .replace(new RegExp(_left+"\\*.*?\\*"+_right, "g"),"")

            //把所有换行去掉  \r回车符 \t制表符 \n换行符
            .replace(new RegExp("[\\r\\t\\n]","g"), "")

            //用来处理非分隔符内部的内容中含有 斜杠 \ 单引号 ‘ ，处理办法为HTML转义
            .replace(new RegExp(_left+"(?:(?!"+_right+")[\\s\\S])*"+_right+"|((?:(?!"+_left+")[\\s\\S])+)","g"),function (item, $1) {
                var str = '';
                if($1){

                    //将 斜杠 单引 HTML转义
                    str = $1.replace(/\\/g,"&#92;").replace(/'/g,'&#39;');
                    while(/<[^<]*?&#39;[^<]*?>/g.test(str)){

                        //将标签内的单引号转义为\r  结合最后一步，替换为\'
                        str = str.replace(/(<[^<]*?)&#39;([^<]*?>)/g,'$1\r$2')
                    };
                }else{
                    str = item;
                }
                return str ;
            });


        str = str
            //定义变量，如果没有分号，需要容错  <%var val='test'%>
            .replace(new RegExp("("+_left+"[\\s]*?var[\\s]*?.*?[\\s]*?[^;])[\\s]*?"+_right,"g"),"$1;"+_right_)

            //对变量后面的分号做容错(包括转义模式 如<%:h=value%>)  <%=value;%> 排除掉函数的情况 <%fun1();%> 排除定义变量情况  <%var val='test';%>
            .replace(new RegExp("("+_left+":?[hvu]?[\\s]*?=[\\s]*?[^;|"+_right+"]*?);[\\s]*?"+_right,"g"),"$1"+_right_)

            //按照 <% 分割为一个个数组，再用 \t 和在一起，相当于将 <% 替换为 \t
            //将模板按照<%分为一段一段的，再在每段的结尾加入 \t,即用 \t 将每个模板片段前面分隔开
            .split(_left_).join("\t");

        //支持用户配置默认是否自动转义
        if(bt.ESCAPE){
            str = str

                //找到 \t=任意一个字符%> 替换为 ‘，任意字符,'
                //即替换简单变量  \t=data%> 替换为 ',data,'
                //默认HTML转义  也支持HTML转义写法<%:h=value%>
                .replace(new RegExp("\\t=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':huanpeng.template._encodeHTML($1),'");
        }else{
            str = str

                //默认不转义HTML转义
                .replace(new RegExp("\\t=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':$1,'");
        };

        str = str

            //支持HTML转义写法<%:h=value%>
            .replace(new RegExp("\\t:h=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':huanpeng.template._encodeHTML($1),'")

            //支持不转义写法 <%:=value%>和<%-value%>
            .replace(new RegExp("\\t(?::=|-)(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':$1,'")

            //支持url转义 <%:u=value%>
            .replace(new RegExp("\\t:u=(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':encodeURIComponent($1),'")

            //支持UI 变量使用在HTML页面标签onclick等事件函数参数中  <%:v=value%>
            .replace(new RegExp("\\t:v=(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':huanpeng.template._encodeEventHTML($1),'")

            //将字符串按照 \t 分成为数组，在用'); 将其合并，即替换掉结尾的 \t 为 ');
            //在if，for等语句前面加上 '); ，形成 ');if  ');for  的形式
            .split("\t").join("');")

            //将 %> 替换为_template_fun_array.push('
            //即去掉结尾符，生成函数中的push方法
            //如：if(list.length=5){%><h2>',list[4],'</h2>');}
            //会被替换为 if(list.length=5){_template_fun_array.push('<h2>',list[4],'</h2>');}
            .split(_right_).join("_template_fun_array.push('")

            //将 \r 替换为 \
            .split("\r").join("\\'");

        return str;
    };

})(window);

// 基础类 定义了一些通用的方法
var setCookie = function(name, value){
    var exp = new Date();
    exp.setTime( exp.getTime() + 30 * 24 * 3600 * 1000 );
    document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + "; path="+$conf.cookiepath+";domain=" + document.domain + ";";
};

var getCookie = function(name){
    var a  = document.cookie.match(new RegExp("(?:^|;)\\s*" + name + "=([^;]*)"));
    return (a) ? decodeURIComponent(a[1]) : null;
};

var deleteCookie = function(name){
    var exp = new Date();
    exp.setTime(exp.getTime() - 3600);

    var cval = getCookie(name);

    if(cval != null){
        document.cookie = name + "=" + encodeURIComponent(cval) + ";expires=" + exp.toGMTString() + "; path="+$conf.cookiepath+";domain=" + document.domain + ";";
    }
}

function getProperty(){
    var property = getCookie('_uproperty');
    var tmp = property.split(':');

    return {
        'bean':tmp[0],
        'coin':tmp[1]
    };
}
function setProperty(bean, coin){
    var cookieStr = bean+":"+coin;
    setCookie('_uproperty', cookieStr);
}

var logout_submit = function(){
    console.log('logout');
    for(var i in $conf.cookie){
        deleteCookie($conf.cookie[i]);
    }
    location.href = location.href;
}

var check_user_login = function(){
    var c = new Array();
    c.uid = getCookie('_uid') || 0;
    c.enc = getCookie('_enc') || '';
    if(!c.uid || !c.enc || c.uid >= $conf.maxUid){
        return false;
    }else{
        return c;
    }
}

var check_login = function(){
    var c = check_user_login();
    if(!c){
        loginFast.login(0);
        return false;
    }
    return true;
}

var check_phoneStatus = function(status){
    status = status == undefined ? Number(getCookie('_phonestatus')) : Number(status);
    if(status != 1){
        loginFast.bindingMobile();
        return false;
    }
    return true;
}

function checkMobile(mobile){
    if(mobile.length != 11)
        return false;

    var p = /(13|14|15|16|18|19)[0-9]\d{8}|17[0-9]\d{8}/;
    return p.test(mobile);
}

function identityCodeVaild(code){
    var tip = '';
    var pass = true;
    var city = [11,12,13,14,15,
        21,22,23,
        31,32,33,34,35,36,37,
        41,42,43,44,45,46,
        50,51,52,53,54,
        61,62,63,64,65,
        71,81,82,91
    ];
    var identReg = /^[1-9]\d{5}((18|19|20)\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dx]$/i;

    if(!code || !identReg.test(code)){
        pass = false;
        tip = '格式错误';
    }else if(city.indexOf(parseInt(code.substr(0,2))) == -1 ){
        pass = false;
        tip = '地区编码错误';
    }else{
        //检查校验位
        code = code.split('');

        //∑(ai×Wi)(mod 11)
        //加权因子
        var factor = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
        //校验位
        var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
        var sum = 0,
            ai = 0,
            wi = 0;
        for(var i = 0; i < 17; i++){
            ai = code[i];
            wi = factor[i];
            sum += ai * wi;
        }
        if(parity[sum % 11] != code[17].toUpperCase()){
            pass =  false;
            tip = '校验位错误';
        }
    }
    if(!pass) console.log(tip);
    return pass;
}

function isLeapYear(year){
    if(((year % 4) == 0) && ((year % 100) != 0 || (year % 400) == 0)){
        return true
    }
    return false;
}

function checkDate(y, m, d){

    var year_reg = /^(1[89]|20)\d{2}$/;
    var month_reg = /^0[1-9]|1[0-2]$/;

    if(!year_reg.test(y))
        return false;

    if(!month_reg.test(m))
        return false;

    var reg = '';
    if(/^0[13578]|1[02]$/.test(m)){
        reg = /^0[1-9]|[12]\d{1}|3[01]/;
    }else{
        if(m == '02'){
            var leapYear = isLeapYear(parseInt(y));
            reg = leapYear ? /^0[1-9]|1\d{1}|2[0-9]$/ : /^0[1-9]|1\d{1}|2[0-8]/;
        }else{
            reg = /^0[1-9]|[12]\d{1}|30/;
        }
    }

    return reg.test(d);
}

var playerStatusConf = {
    retry2Stop:{
        limit:20,
        list:['NetStream.Play.StreamNotFound', 'NetConnection.Connect.Failed']
    },
    requestRtmpURL:{
        limit:10,
        list:['NetStream.Play.UnpublishNotify']
    }
}

//流请求XMLHttpRequest对象
var myXMLHttpRequest;
function initPlayer(obj, luid){
    if(myXMLHttpRequest != null){
        //console.log('killed');
        myXMLHttpRequest.abort();
    }
    var liveRoom = (typeof arguments[2] == 'string') ? arguments[2] : '';
    var requestUrl = $conf.api + 'live/getStreamList.php';
    var requestData = {
        luid:luid
    };
    myXMLHttpRequest = ajaxRequest({url:requestUrl,data:requestData},function(d){
        var streamList = d.streamList;
        var orientation = d.orientation;
        var stream = d.stream;

        /*var rtmp = streamList[0] ? 'rtmp://' + streamList[0] : '';
        if(!streamList || !stream || !streamList[0]){
            runSwfFunction(obj,'liveEnd',0);
            runSwfFunction(obj,'liveEnd',0);

            return;
        }*/
        var rtmp = streamList.length != 0 ? 'rtmp://' + streamList : '';
        if(!streamList || !stream || !streamList){
            runSwfFunction(obj,'liveEnd',0);
            runSwfFunction(obj,'liveEnd',0);

            return;
        }
        if(liveRoom){
            //runSwfFunction(obj,'inputURL', stream,rtmp,liveRoom);
            runSwfFunction(obj,'inputURL',liveRoom);
        }else{
            runSwfFunction(obj,'inputURL', stream,rtmp);
            /*Do not delete this commit*/
            setTimeout(function () {
                if(getCookie('hostLiveID')){
                    var hostLiveID = getCookie('hostLiveID');
                    if(hostLiveID && d.liveID == hostLiveID){
                        runSwfFunction(obj, 'setVolumeAuthority',0,1);
                        deleteCookie('hostLiveID');
                    }
                }
            },1000);
        }
		if(orientation == 0){
			runSwfFunction(obj,'angle',1);
		}else{
			runSwfFunction(obj,'angle',0);
		}
		runSwfFunction(obj,'setHostID', luid);
    },function(){

    });

    var timer = 0;
    var currcommand = '';
    window.netStatus = function(command){
        if(playerStatusConf.retry2Stop.list.indexOf(command) > -1){
            console.log(command);
            resetTimer(command);
            if(timer==playerStatusConf.retry2Stop.limit){
                runSwfFunction(obj, 'liveEnd', 1);
                runSwfFunction(obj, 'liveEnd', 1);
                timer = 0;
            }else{
                timer ++;
            }
        }
        if(playerStatusConf.requestRtmpURL.list.indexOf(command) > -1){
            resetTimer(command);
            if(timer == playerStatusConf.requestRtmpURL.limit){
                initPlayer(obj, luid);
                timer = 0;



            }else{
                timer ++;
            }
        }

        function resetTimer(command){
            if(command != currcommand)
                timer = 0;
            currcommand = command;
        }
    }
}


function backHostID(luid,uid){

}

function calTime(time){
    var now = Date.parse(new Date()) / 1000;
    time = parseInt(time);

    var d = now - time;
    var minute = 60;
    var hour = 60 * minute;
    var day = 24 * hour;
    var month = 30 * day;
    var year = 12 * month;

    //如果先算 d / year 的话 在  d < 30 时候 会越界
    var timearr = {
        0:minute,
        1:hour,
        2:day,
        3:month,
        4:year
    };
    var x = 0;
    for(var i = 0; i < 5; i++){
        if(d >= timearr[i]){
            x = i
        }else{
            break;
        }

    }
    var t = [];
    for(var j=x ;j>=0;j--){
        var tmp = parseInt(d/timearr[j]);
        t.push(tmp);
        d = d - tmp * timearr[j];
    }

    t.push(d);
    var len = t.length;
    for(;len < 6; len ++ ){
        t.unshift(0);
    }

    return t;
}

function calVisitTime(time){
    var str = [
        '年',
        '月',
        '天',
        '小时',
        '分钟',
    ];
    var t = calTime(time);
    //console.log(t);
    for(var i in t)
        if(t[i] && i < 5)
            return t[i] + str[i] + "前观看";

    return '刚刚';
}

function initPlaytimeHtml(time) {
    if(time){
        var t = calTime(time);
        var str = [
            '年',
            '个月',
            '天',
            '小时',
            '分钟',
            '秒'
        ];
        for (var i in t)
            if (t[i])
                return "已播<em class='time'>" + t[i] + "</em>" + str[i];
    }else{
        return;
    }

}



function replace_em(str){
    str = str.replace(/\</g,'&lt;');
    str = str.replace(/\>/g,'&gt;');
    str = str.replace(/\n/g,'<br/>');
    str = str.replace(/\[em_([0-9]*)\]/g, function(word){

        var num = word.match(/\d{1,2}/g);
        num = num[0] ? num[0] : '';
        var str = word;
        num && num < 23 && (str='<img src="../static/img/emoji/'+num+'.png" border="0" />');
        return str;
    });//'<img src="static/img/emoji/$1.png" border="0" />'
    return str;
}

function personalCenter_sidebar(selector){
    var page = {
        'personal':'li-personal',
        'msg':'li-msg',
        'follow':'li-follow',
        //'invite':4,
        'beanchor':'li-beanchor',
        'giftHistory':'li-gift',
        'zone':'li-zone',
        'property':'li-property',
        'admin':'li-admin',
        'recharge':'li-recharge',
        'homepage':"li-homepage"
    };
    if(!page[selector])
        return;

    $('.sidebar_center ul').find('li.'+page[selector]).addClass('currentpage');
}
var NoticeBox = function(){
    function createBox(b){
        if($('#noticeBox')[0]){
            removeBox();
        }
        Mask.creates();
        Mask.box.css('background-color','rgba(0,0,0,0)');
        $('<div/>',{
            id:'noticeBox',
            'class':'noticeBox',
            style:'position:fixed; left:50%;top:320px;z-index:1000',
            html:b
        }).appendTo(document.body);
    }
    function removeBox(){
        if(!$('#noticeBox')[0])
            return;
        Mask.remove();
        $("#noticeBox").remove();
    }
    return {
        create:function(b){
            createBox(b);
        },
        remove:function(){
            removeBox();
        }
    }
}();

function thisMovie(obj){

    if (navigator.appName.indexOf("Microsoft") != -1) {
        return window[obj];
    } else {
        return document[obj];
    }
}

function checkUploadImage(fileInput, size){
    if(!fileInput || !size) return -3;

    var filePath = fileInput.value;
    var fileExt = filePath.substring(filePath.lastIndexOf('.')).toLowerCase();

    if(!checkFileExt(fileExt)){
        return -1;
    }

    if(fileInput.files && fileInput.files[0]){
        if(fileInput.files[0].size > size){
            return -2;
        }
        return 0;
    }else{
        //todo ie support
        //fileInput.select();
        //var url = document.selection.createRange().text;
        //try {
        //    var fso = new ActiveXObject("Scripting.FileSystemObject");
        //} catch (e) {
        //    return -3;
        //}
        //if(fso.GetFile(url).size > size){
        //  return -2
        //}
        return 0;
    }

    function checkFileExt(ext){
        if(!ext.match(/.jpg|.jpeg|.png|.gif/i))
            return false;

        return true;
    }
}

/**
 * 格式化时间
 * @param mask
 * @param unixtime
 * @returns {XML|*|string|void}
 */
function js_date_format(mask, unixtime){
    var d = new Date();
    if(unixtime){
        d = new Date(parseInt(unixtime) * 1000);
    }
    var zeroize = function (value, length) {
        if (!length) length = 2;
        value = String(value);
        for (var i = 0, zeros = ''; i < (length - value.length); i++)
        {
            zeros += '0';
        }
        return zeros + value;
    };

    return mask.replace(/"[^"]*"|'[^']*'|\b(?:d{1,4}|m{1,4}|yy(?:yy)?|([hHMstT])\1?|[lLZ])\b/g, function ($0) {
        switch ($0) {
            case 'd': return d.getDate();
            case 'dd': return zeroize(d.getDate());
            case 'ddd': return ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'][d.getDay()];
            case 'dddd': return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][d.getDay()];
            case 'M': return d.getMonth() + 1;
            case 'MM': return zeroize(d.getMonth() + 1);
            case 'MMM': return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()];
            case 'MMMM': return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][d.getMonth()];
            case 'yy': return String(d.getFullYear()).substr(2);
            case 'yyyy': return d.getFullYear();
            case 'h': return d.getHours() % 12 || 12;
            case 'hh': return zeroize(d.getHours() % 12 || 12);
            case 'H': return d.getHours();
            case 'HH': return zeroize(d.getHours());
            case 'm': return d.getMinutes();
            case 'mm': return zeroize(d.getMinutes());
            case 's': return d.getSeconds();
            case 'ss': return zeroize(d.getSeconds());
            case 'l': return zeroize(d.getMilliseconds(), 3);
            case 'L': var m = d.getMilliseconds();
                if (m > 99) m = Math.round(m / 10);
                return zeroize(m);
            case 'tt': return d.getHours() < 12 ? 'am' : 'pm';
            case 'TT': return d.getHours() < 12 ? 'AM' : 'PM';
            case 'Z': return d.toUTCString().match(/[A-Z]+$/);
            // Return quoted strings with the surrounding quotes removed
            default: return $0.substr(1, $0.length - 2);
        }
    });

}

function exchangeToBean(hpbean){
    return hpbean/1000;
}
function exchangeToHpBean(bean){
    return bean * 1000;
}
var Emoji;
(function(){

    var label = {
        1:'呲牙',
        2:'亲亲',
        3:'色',
        4:'衰',
        5:'睡觉',
        6:'疑问',
        7:'友尽',
        8:'晕',
        9:'咒骂',
        10:'抓狂',
        11:'鄙视',
        12:'闭嘴',
        13:'崇拜',
        14:'奋斗',
        15:'高兴',
        16:'哈欠',
        17:'害羞',
        18:'汗',
        19:'僵尸',
        20:'抠鼻',
        21:'哭',
        22:'酷'
    }

    var a = jQuery;
    Emoji = {
        options:{
            id:'facebox',
            path:'face/',
            assign:'content',
            tip:'em_',
            position:'bottom',
            allCount:75,
            rowCount:15
        },
        selector:'',
        init:function(selector,option){
            var self = this;
            self.options = a.extend(self.options, option);
            self.selector = a(selector) || false;
            self.eventHandle();
        },
        eventHandle:function(){
            var self = this;
            var assign = $('#' + self.options.assign);
            var id = self.options.id;
            var path = self.options.path;
            var tip = self.options.tip;
            //console.log(assign);
            if(assign.length <= 0){
                //console.log('err : 缺少表情赋值对象');
                return false;
            }
            if(!self.selector){
                //console.log('err : 缺少选择器或者选择器不存在');
                return false;
            }

            self.selector.bind('click',function(e){
                if(a('#'+id)[0]){
                    $('#'+id).hide();
                    a('#'+id).remove();
                    return;
                }

                var strFace, labFace;
                if(a('#'+id).length <= 0){
                    strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="hpemoji">' +
                    '<table border="0" cellspacing="0" cellpadding="0"><tr>';

                    for(var i=1; i <= self.options.allCount; i++){
                        labFace = '['+tip+i+']';
                        strFace += '<td data-emoji="'+i+'" title="'+label[i]+'"><img src="'+path+i+'.png"/></td>';
                        if( i % self.options.rowCount == 0 ) strFace += '</tr><tr>';
                    }
                    strFace += '</tr></table></div>'
                }
                a(this).parent().append(strFace);
                a('#'+id).find('tbody td').bind('click',function(){
                    var i = a(this).attr('data-emoji');
                    var textFeild = '['+tip+i+']';
                    self.setCaret();
                    self.insertAtCaret(textFeild);
                    $("#"+self.options.assign).focus();
                });
                var offset = $(this).position();
                var top = offset.top + $(this).outerHeight();

                if(self.options.position == 'top'){
                    var top = $('#' + id).height() + 2 * parseInt($('#'+id).css('padding-top')) + 2 + 14;

                    $('#'+id).css('top', -top+'px').show();
                    e.stopPropagation();
                    return;
                    var faceBoxHeight = $('#' + id).height() || 120;
                    top = offset.top - $(this).outerHeight() - faceBoxHeight;
                }
                $('#'+id).css('top',top);
                $('#'+id).css('left',offset.left);
                $('#'+id).show();
                e.stopPropagation();
            });
            a(document).click(function(){
                $('#'+id).hide();
                $('#'+id).remove();
            });
        },
        setCaret:function(){
            a.browser = {};
            a.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
            if(!a.browser.msie) return false;
            var self = this;
            var assign = $('#' + self.options.assign);

            var initSetCaret = function(){
                var textObj = $(this).get(0);
                //console.log(textObj);
                textObj.caretPos = document.selection.createRange().duplicate();
            };
            assign.click(initSetCaret).select(initSetCaret).keyup(initSetCaret);
        },
        insertAtCaret:function(textFeildValue){
            var self = this;
            var textObj = a('#' + self.options.assign).get(0);
            if(document.all && textObj.createTextRange && textObj.caretPos){
                var caretPos=textObj.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ?
                textFeildValue+'' : textFeildValue;
            } else if(textObj.setSelectionRange){
                var rangeStart=textObj.selectionStart;
                var rangeEnd=textObj.selectionEnd;
                var tempStr1=textObj.value.substring(0,rangeStart);
                var tempStr2=textObj.value.substring(rangeEnd);
                textObj.value=tempStr1+textFeildValue+tempStr2;
                textObj.focus();
                var len=textFeildValue.length;
                textObj.setSelectionRange(rangeStart+len,rangeStart+len);
                textObj.blur();
            }else{
                textObj.value+=textFeildValue;
            }
        }
    }
}());

var Mask;
(function(){
    var a = jQuery;
    Mask = {
        // ie6: a.browser.msie && a.browser.version == "6.0",
        creates:function(){
            if(this.box){
                this.remove();
            }
            this.box = a('<div/>',{
                id:"page_Mask",
                'class':'promptbg'
            }).css({
                visibility: "visible",
                width: "100%",
                height: "100%",//a(window).height(),
                position: (this.ie6 && "absolute" || "fixed"),
                top: 0,
                left: 0,
                display: "block",
                zIndex: 99,
                background: "#000",
                opacity: 0.3//\D/.test(b) && 0.3 || b
            }).appendTo(document.body);
            a(window).bind("resize", this.set_size);
            // if (this.ie6) {
            //     this.set_pos();
            //     a(window).bind("scroll", this.set_pos)
            // }
        },
        remove:function(){
            if(!this.box){
                return;
            }
            a(window).unbind('resize', this.set_size);
            a(window).unbind('scroll', this.set_pos);
            this.box.remove();//***
            this.box = null;
        },
        set_size:function(){
            Mask.box.css('height',a(window).height());
        },
        set_pos:function(){
            Mask.box.css('top', a(window).scrollTop());
        }
    }
}());


function toInitCookieURL(ref_url){
    location.href = $conf.domain + 'initCookie.php?login=1&ref_url=' + ref_url;
}

var loginFast;
(function(){
    var a = jQuery;
    var codeErrorStruct = {
        '-1003':{id:'password', type:'length'},
        '-1004':{id:'username', type:'format'},
        '-4031':{id:"identCode", type:'error'},
        '-4060':{id:"username", type:'used'},
        '-4061':{id:'password', type:'error'},
        '-4058':{id:'username', type:'format'},
        '-4056':{id:'username', type:'empty'},
        '-996':{id:'password', type:'error'},
        '-4059':{id:'username',type:'notFound'},
        '-1009':{id:'username', type:'notFound'},
        '-4010':{id:'usernick', type:'length'}
    };
    var htmlFunc = null;
    var bindMobileHtml = null;
    var isPage = false;
    loginFast = {

        set_pos:function(){//设置位置
            var f = a('#loginModal');
            var width = f.width() + parseInt(f.css('padding-left')) + parseInt(f.css('padding-right'));
            f.css('margin-left', -width/2 +'px');
            $('#loginModal').find('#bind_form').show();
        },
        createModal:function(b,conf){
            var confDefault = {
                style:''
            };
            conf = $.extend({},confDefault,conf);
            this.cancel();
            Mask.creates();
            a('<div/>',{
                id:"loginModal",
                'class':'loginModal',
                style:'position:fixed; left:50%;top:100px; z-index:1000;'+conf.style,
                html:b
            }).appendTo(document.body);
            this.set_pos();
        },
        login:function(c, jqueryObj, isPages){
            var b = this;
            //var d = c == 1 ? b.ModalHtml.regHtmlStr() : b.ModalHtml.loginHtmlStr();
            //d = '<div class="loginCon">' + d + b.ModalHtml.loginConRight() +'</div>';
            htmlFunc = htmlFunc ? htmlFunc : huanpeng.template('jsTemplate-loginModal');
            var html = htmlFunc({loginModal:!c});
            if(!jqueryObj){
                //this.createModal(this.ModalHtml.loginHeader());
                //a("#loginModal").html(b.ModalHtml.loginHeader() + d + b.ModalHtml.loginFooter());
                this.createModal(html);
            }else{
                //jqueryObj.html(b.ModalHtml.loginHeader() + d + b.ModalHtml.loginFooter());
                jqueryObj.html(html);
                jqueryObj.find('.loginModal-close').remove();
            }

            isPage = isPages ? true : false;

            b.initHeader(jqueryObj);
            inputPlaceholder();

            if(c == 1){
                b.initReg();
                $('#reg_form').show();
            }else{
                b.initLogin();
                $('#login_form').show();
            }
        },
        bindingMobile:function(){
            bindMobileHtml = bindMobileHtml ? bindMobileHtml:huanpeng.template('jsTemplate-bindMobileModal');

            this.createModal(bindMobileHtml(),{style:'width:400px;'});
            this.initHeader();
            this.initBindingMobile();
        },
        initBindingMobile:function(){
            var bindingForm = $('.loginModal #bind_form');
            var self = this;
            var submit = $('#bindSubmit');
            var bindingDom = {
                username:bindingForm.find('#username'),
                mobileCode:bindingForm.find('#mobileCode'),
                getCode:$('#bind-getMobileCode'),
                password:bindingForm.find('#password'),
                password2:bindingForm.find('#password2')
            };
            bindingDom.username.bind('input prooertychange',function () {
                if($(this).val().length == 11){
                    self.checkPhoneNumber($(this).val());
                }
            });
            var lockGetMobileCode = 0;
            var getMobileGeetestCodeLock = 0;
            bindingDom.getCode.bind('click', function(){
                if(bindingDom.username.val() == ''){
                    $('#reg_form .login-form-item:eq(0)').addClass('error');
                    $('#reg_form .login-form-item:eq(0) .input-item-error-text').text('请输入手机号');
                    return;
                }
                if(lockGetMobileCode==1 || getMobileGeetestCodeLock == 1){
                    return;
                }
                getMobileGeetestCodeLock = 1;
                var username = bindingDom.username.hasClass('placeholder') ? '':bindingDom.username.val();
                if(!username){
                    self._error('username','empty');
                    getMobileGeetestCodeLock = 0;
                    return;
                }
                if(bindingDom.username.parents('.login-form-item').hasClass('error')){
                    getMobileGeetestCodeLock = 0;
                    return;
                }
                var iTime = 59;
                var Account;
                geetest({product:'popup',append:'#binding-captcha'}, function (data) {
                    getMobileGeetestCodeLock = 0;
                    var data = $.extend({mobile:username,type:'gt',from:'1'},data);
                    var url = $conf.api + 'code/mobileCode.php';
                    ajaxRequest({url:url,data:data},function(d){
                        lockGetMobileCode = 1;
                        RemainTime();

                        function RemainTime(){
                            var btn = bindingDom.getCode;
                            btn.addClass('disabled');
                            var iSecond, iMinute, sSecond = "", sTime = "";
                            if(iTime >= 0){
                                iSecond = parseInt(iTime % 60);
                                iMinute = parseInt(iTime / 60);

                                if(iSecond > 0) {
                                    if (iMinute > 0) {
                                        sSecond = iMinute + "分钟" + iSecond + "s"
                                    } else {
                                        sSecond = iSecond + "s后重发";
                                    }
                                }
                                sTime = sSecond;
                                if(iTime == 0){
                                    clearTimeout(Account);
                                    sTime = "获取验证码";
                                    iTime = 59;
                                    lockGetMobileCode = 0;
                                    btn.removeClass('disabled');
                                }else{
                                    Account = setTimeout(RemainTime, 1000);
                                    iTime = iTime - 1;
                                }
                            }else{
                                btn.removeClass('disabled');
                                sTime = '获取验证码';
                            }
                            btn.text(sTime);
                        }
                    },function (d) {
                        if(codeErrorStruct[d.code]){
                            var error = codeErrorStruct[d.code];
                            self._error(error.id, error.type);
                        }
                        if(d.code == '-4060'){
                            $('#loginModal').find('#login-error-text-username').text('该手机号已被注册');
                            $('#loginModal').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                        }
                    });
                },function(){
                    getMobileGeetestCodeLock = 0;
                })
            });
            bindingForm.find('input').focus(function () {
                self._clearError($(this).parent('.login-form-item'));
            });
            bindingForm.find('input').blur(function () {
                var val = $(this).hasClass('placeholder') ? '' : $(this).val();
                var id = $(this).attr('id');
                if(!val){
                    self._error(id, 'empty');
                }else{
                    if(id == 'username'){
                        self.checkPhoneNumber(val);
                    }
                }
            });
            bindingDom.password2.blur(function () {
                var password = bindingDom.password.hasClass('placeholder') ? '' : bindingDom.password.val();
                var password2 = $(this).hasClass('placeholder') ? '' : $(this).val();
                if(password2 != password){
                    self._error('password2','notEqual');
                }
            });
            submit.bind('click',function () {
                var tmpData = {
                    username:'mobile',
                    mobileCode:'mobileCode',
                    password:'password',
                    password2:'password2'
                }
                var error = false;
                var data = {};
                for(var index in tmpData){
                    data[tmpData[index]] = bindingDom[index].hasClass('placeholder') ? '' : bindingDom[index].val();
                    if(!data[tmpData[index]]){
                        self._error(index, 'empty');
                        self.enableForm();
                        error = true;
                    }
                }
                if(data['password']!=data['password2']){
                    self._error('password2','notEqual');
                    self.enableForm();
                    error = true;
                }
                if(error) return;
                var url = $conf.api + 'user/bindingMobile.php';

                data = $.extend({uid:getCookie('_uid'),encpass:getCookie('_enc')},data);
                ajaxRequest({url:url,data:data},function(){

                    toInitCookieURL(encodeURIComponent(location.href));
                },function (d) {
                    tips(d.desc);
                });
            })
        },
        cancel:function(){
            if(!a("#loginModal")[0]){
                return;
            }
            Mask.remove();
            a("#loginModal").remove();
        },
        initHeader:function(jqueryObj){
            var b = this;
            a('.login_select_tab li').click(function(){
                if(isPage){
                    var url = $(this).index() ? 'register.php' : 'login.php';
                    var ref_url =  $_GET['ref_url'] ?  '?ref_url=' + $_GET['ref_url'] : '';
                    location.href = url + ref_url;
                }else{
                    b.login($(this).index(), jqueryObj);
                }
            });
            a('.loginModal-close').bind('click', function(){
                b.cancel()
            });
        },
        initReg:function(){
            a('.login_select_tab li').removeClass('selected').eq(1).addClass('selected');
            var regForm = $('#reg_form');
            var self = this;
            var _domain = document.domain;
            var submit = a("#regsubmit");
            var regDom = {
                username:regForm.find('#username'),
                identCode:regForm.find('#identCode'),
                mobileCode:regForm.find('#mobileCode'),
                getCode:a('#reg-getMobileCode'),
                usernick:regForm.find('#usernick'),
                password:regForm.find('#password')
            };
            if( Number( $_GET['cid'] ) )
            {
                $( '.login-threeParty p a' ).attr( 'href', 'javascript:;');
            }

            function toReg(){
                var tmpData = {
                    username:'mobile',
                    //identCode:'identCode',
                    mobileCode:'mobileCode',
                    usernick:'nick',
                    password:'password'
                }
                var companyError = -4096;

                var error = false;
                var data = {};
                for(var index in tmpData){
                    data[tmpData[index]] = regDom[index].hasClass('placeholder') ? '' : regDom[index].val();
                    if(!data[tmpData[index]]){
                        self._error(index, 'empty');
                        self.enableForm();
                        error = true;
                    }
                }
                if(error) return;
                self._clearError();
                var requestUrl = $conf.api + 'user/registered.php';
                var requestData = data;

                //company register
                if(Number($_GET['cid']))
                {
                    data.cid = Number($_GET['cid']);
                }

                ajaxRequest({url:requestUrl,data:requestData},function (d) {
                    var uid = d.uid;
                    var encpass = d.encpass;
                    setCookie('_uid',uid);
                    setCookie('_enc',encpass);
                    if(isPage){
                        var ref_url = $_GET['ref_url'] ? decodeURIComponent($_GET['ref_url']) : decodeURIComponent($conf.person);
                        // location.href = ref_url;
                        toInitCookieURL(ref_url);
                    }else{
                        toInitCookieURL(decodeURIComponent(location.href));
                        // location.href = location.href;
                    }
                },function (d) {
                    self.enableForm();
                    self._clearError();
                    var code = d.code;

                    if( code == companyError )
                    {
                        tips(d.desc);
                        return;
                    }
                    if (code == '-4031'){
                        tips('验证码已过期');
                        return;
                    }
                    if(codeErrorStruct[code]){
                        self._error(codeErrorStruct[code].id,codeErrorStruct[code].type);
                    }
                });
            };



            $('.login-form-item ').on('click','.input-item-error-text',function () {
                $(this).parents('.login-form-item').find('.input-item-text').focus();
            });

            var lockGetMobileCode = 0;
            var getMobileGeetestCodeLock = 0;
            regDom.getCode.bind('click', function(){
                if(lockGetMobileCode == 1 || getMobileGeetestCodeLock == 1){
                    return;
                }
                getMobileGeetestCodeLock = 1;
                var regUserName = regDom.username.hasClass('placeholder') ? '' :regDom.username.val();

                if(!regUserName){
                    $('#reg_form').find('#login-error-text-username').text('手机号不能为空');
                    $('#reg_form').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                    getMobileGeetestCodeLock = 0;
                    return;
                }
                if(regUserName.length < 11 || !checkMobile(regUserName)){
                    $('#reg_form').find('#login-error-text-username').text('手机号格式错误');
                    $('#reg_form').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                    getMobileGeetestCodeLock = 0;
                    return;
                }
                if(regDom.username.parents('.login-form-item').hasClass('error')){
                    getMobileGeetestCodeLock = 0;
                    return;
                }
                //if(!regIdentCode){
                //    self._error('identCode', 'error');
                //    return;
                //}
                var iTime = 59;
                var Account;


                geetest({product:'popup',append:'#reg-captcha'}, function(data){
                    getMobileGeetestCodeLock = 0;
                    lockGetMobileCode = 0;

                    var requestData = $.extend({mobile:regUserName, type:'gt',from:0}, data);
                    var requestUrl = $conf.api + 'code/mobileCode.php';

                    ajaxRequest({url:requestUrl,data:requestData},function(d){
                        lockGetMobileCode = 1;
                        RemainTime();
                    },function(d){
                        if(d.code && codeErrorStruct[d.code]){
                            self._error(codeErrorStruct[d.code].id, codeErrorStruct[d.code].type);
                        }
                        if(d.code == '-4060'){
                            $('#loginModal').find('#login-error-text-username').text('该手机号已被注册');
                            $('#loginModal').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                        }
                    });

                    function RemainTime(){
                        var btn = regDom.getCode;
                        //btn.attr('disabled', 'disabled');
                        btn.addClass('disabled');

                        var iSecond, iMinute, sSecond = "", sTime = "";
                        if(iTime >= 0){
                            iSecond = parseInt(iTime % 60);
                            iMinute = parseInt(iTime / 60);

                            if(iSecond > 0) {
                                if (iMinute > 0) {
                                    sSecond = iMinute + "分钟" + iSecond + "s"
                                } else {
                                    sSecond = iSecond + "s后重发";
                                }
                            }
                            sTime = sSecond;
                            if(iTime == 0){
                                clearTimeout(Account);
                                sTime = "获取验证码";
                                iTime = 59;
                                lockGetMobileCode = 0;
                                btn.removeClass('disabled');
                            }else{
                                Account = setTimeout(RemainTime, 1000);
                                iTime = iTime - 1;
                            }
                        }else{
                            btn.removeClass('disabled');
                            sTime = '获取验证码';
                        }
                        btn.text(sTime);
                    }
                },function(){
                    //close modal event handle
                    getMobileGeetestCodeLock = 0;
                });
                getMobileGeetestCodeLock = 0;
            });

            var regVCodesrc = regForm.find('.login-identCode img').attr('src');
            regForm.find('.login-identCode').bind('click', function(){
                var obj = regForm.find('.login-identCode img');
                obj.attr('src',regVCodesrc + '?nowtime' + new Date().getTime());
            });


            regForm.find('input').focus(function(){
                self._clearError($(this).parents('.login-form-item'));
            });

            regForm.find('.input-item-text').blur(function(){
                var val = $(this).hasClass('placeholder') ? '' : $(this).val();
                var id = $(this).attr('id');
                if(!val){
                    self._error(id, 'empty');
                }else{
                    if(id == 'username'){
                        self.checkPhoneNumber(val);
                    }else if(id == 'usernick'){
                        if(val.length>=3 && val.length<=12){
                            self.checkUserNick(val);
                        }else{
                            $('#login-error-text-usernick').parent().addClass('error');
                            $('#login-error-text-usernick').text('昵称长度3-12位');
                        }

                    }
                }
            });

            submit.bind('click', function(){
                //self.disableForm();
                toReg();
            });

            $('.loginopt .toLogin').bind('click', function(){
                self.login(0);
            });
        },
        initLogin:function(){
            var loginForm = $('#login_form');
            var self = this;
            var submit = a("#loginsubmit");

            var loginDom = {
                username:loginForm.find('#username'),
                password:loginForm.find('#password')
            };

            loginForm.find('input').focus(function () {
                self._clearError($(this).parent('.login-form-item'));
            });

            loginDom.username.blur(function () {
                //jx
                var val = $(this).val();
                if(!checkMobile(val)){
                    self._error('username','format');
                }

            });

            var identCodeOpen = false;
            var geeLoginValidateCode = false;

            var initIdentCode = function(){
                var url = $conf.api + 'code/logInCode.php';
                if(!geeLoginValidateCode) {
                    clearIdentCode();
                    $('#login_form #password').parents('.login-form-item').after('<div class="login-form-item identCode-item"></div>');
                    geetest({product: 'float', append: '.identCode-item'}, function (data) {
                        geeLoginValidateCode = data;
                    });
                    identCodeOpen = true;
                }
            };
            var clearIdentCode = function(){
                loginForm.find('.identCode-item').remove();
                identCodeOpen = false;
            };

            var toLogin = function(){
                self._clearError();
                var tmpData = {
                    username:'mobile',
                    password:'password'
                };
                var data = {};
                for(var index in tmpData){
                    data[tmpData[index]] = loginDom[index].hasClass('placeholder') ? '' : loginDom[index].val();
                    if(!data[tmpData[index]]){
                        self._error(index, 'empty');
                        self.enableForm();
                        return;
                    }
                }
                if(identCodeOpen){
                    if(geeLoginValidateCode){
                        data = $.extend(data,{type:'gt'}, geeLoginValidateCode);
                    }else{
                        return;
                    }
                }
                var requestUrl = $conf.api + 'user/logIn.php';
                var requestData = data;
                ajaxRequest({url:requestUrl,data:requestData},function(d){
                    var uid = d.uid;
                    var encpass = d.encpass;
                    setCookie('_uid',uid);
                    setCookie('_enc',encpass);
                    deleteCookie('_login_identCode_open');
                    if(isPage){
                        var ref_url = $_GET['ref_url'] ? decodeURIComponent($_GET['ref_url']) : decodeURIComponent($conf.person);
                        toInitCookieURL(ref_url);
                        // location.href =  $conf.domain + 'initCookie.php?login=1&ref_url=' + ref_url;
                    }else{
                        toInitCookieURL(location.href);
                        // location.href =  $conf.domain + 'initCookie.php?login=1&ref_url=' + location.href;
                    }
                },function(d){

                    self.enableForm();
                    var code = d.code;

                    if(codeErrorStruct[code]){
                        self._error(codeErrorStruct[code].id,codeErrorStruct[code].type);
                    }
                    if(code == '-4031' || code == -4061){
                        setCookie('_login_indetCode_open',1);
                        //$(".login_reg.l,.to_log").trigger('click');
                        identCodeOpen = false;
                        geeLoginValidateCode = false;
                        initIdentCode();
                    }

                });
            };
            submit.bind('click',function(o){
                self.disableForm();
                toLogin();
            });
            a("#password").bind('keypress', function(o){
                if(o.which == 13){
                    self.disableForm();
                    toLogin();
                    a('#password').blur();
                }
            });

            a('#login_form .input-item-text').focus(function(){
                self._clearError($(this).parents('.form-login-item'));
            });
            loginForm.find('.input-item-text').blur(function(){
                var val = $(this).hasClass('placeholder') ? '' : $(this).val();
                var id = $(this).attr('id');
                if(!val){
                    self._error(id, 'empty');
                }else{
                    if(id == 'username'){
                        var requestUrl = $conf.api + 'check/checkMobileIsUsed.php';
                        if(val.length == 11){
                            var requestData = {
                                mobile:val
                            };
                            ajaxRequest({url:requestUrl,data:requestData},function (d) {
                                if(d.isUsed == '1'){
                                    //已存在
                                }
                                if(getCookie('_login_identCode_open') == 1){
                                    initIdentCode();
                                }else{
                                    clearIdentCode();
                                }
                            });
                        }
                    }
                }

            });
            loginForm.find('.toRegister').bind('click', function(){
                //self.login(1);
                $('.login_select_tab li').trigger('click');
            });
        },
        enableForm:function(){
            this._formState(false);
        },
        disableForm:function(){
            this._formState(true);
        },
        _formState:function(b){
            var regForm = a('#reg_form');
            var loginForm = a('#login_form');
            var submitBtn;
            if(regForm[0]){
                regForm.find("input").prop("disabled", b);
                submitBtn = regForm.find("#regsubmit");
                if(b){
                    //submitBtn.val('注册中...');
                    submitBtn.addClass('reg-disabled');
                }else{
                    submitBtn.val('注册');
                    submitBtn.removeClass('reg-siabled');
                }
            }
            if(loginForm[0]){
                loginForm.find('input').prop('disabled', b);
                submitBtn = loginForm.find('#loginsubmit');
                if(b){
                    //submitBtn.val('登陆中...');
                    submitBtn.addClass('login-disabled');
                }else{
                    submitBtn.val('登录');
                    submitBtn.removeClass('login-disabled');
                }
            }
        },
        checkPhoneNumber:function(mobile){

            if(mobile.length < 11 || !checkMobile(mobile)){
                $('#reg_form').find('#login-error-text-username').text('手机号格式错误');
                $('#reg_form').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                return;
            }else{
                var requestUrl = $conf.api + 'check/checkMobileIsUsed.php';
                if(mobile.length == 11){
                    var requestData = {
                        mobile:mobile
                    };
                    ajaxRequest({url:requestUrl,data:requestData},function (d) {
                        if(d.isUsed == 1){
                            $('#reg_form').find('#login-error-text-username').text('该手机号已被注册');
                            $('#reg_form').find('#login-error-text-username').parent('.login-form-item').addClass('error');
                        }
                    });
                }
            }
        },
        checkUserNick:function(nick){
            var self = this;
            var requestUrl = $conf.api + 'check/checkNickIsUsed.php';
            if(nick.length >= 3 && nick.length <= 12){
                var requestData = {
                    nick:nick
                };
                ajaxRequest({url:requestUrl,data:requestData},function(d){
                    if(d.isUsed == 1){
                        self._error('usernick','used');
                    }
                },function(d){
                    var code = d.code;
                    if(codeErrorStruct[code]){
                        self._error(codeErrorStruct[code].id,codeErrorStruct[code].type);
                    }
                });
            }
        },
        _err:function(obj, text){
            obj.text(text);
            obj.parents('.login-form-item').removeClass('error').addClass('error');
        },
        _error:function(id, type){
            var text = this._errorID[id] + this._errorType[type];
            $('#'+id).attr('placeholder','');
            this._err(errorDom($('#'+id)), text);

            function errorDom(obj){
                return obj.next('.input-item-error-text');
            }
        },
        _clearError:function(item){
            if(item){
                item.removeClass('error').find('.input-item-error-text').text('');
            }else{
                $('.login-form-item').removeClass('error').find('.input-item-error-text').text('');
            }
        },
        _errorID:{
            username:'手机号',
            password:'密码',
            password2:'密码',
            identCode:'验证码',
            mobileCode:'验证码',
            usernick:'昵称'
        },
        _errorType:{
            'empty':'不能为空',
            'error':'错误',
            'format':'格式错误',
            'used':'已被占用',
            'length':'长度3-12位',
            'length2':'长度3-10位',
            'notFound':'不存在',
            'notEqual':'不一致'
        }

    }
}());


(function(a){
    var b = {
        init:function(d, c){
            return (function(){
                b.fillHtml(d, c);
                b.bindEvent(d, c);
            })();
        },
        fillHtml:function(d, c){
            return (function(){
                d.empty();
                var max = c.pageMax;
                var l = max - 1;
                var r = max - 2;
                //console.log(c.current);
                if(c.pageCount > 1){
                    if(c.current > 1){
                        d.append('<a href="javascript:;" class="prevPage"><上一页</a>');
                    }else{
                        d.remove(".prevPage");
                        d.append('<span class="disabled"><上一页</span>');
                    }
                    if(c.pageCount <= max && c.current <= max){
                        for(var f = 1; f<= c.pageCount; f++){
                            if(f != c.current){
                                d.append('<a href="javascript:;" class="pageNum">' + f + "</a>");
                            }else{
                                d.append('<span class="current">' + f + "</span>");
                            }
                        }
                    }
                    if(c.current < l && c.pageCount > max){
                        for(var f = 1; f <= l; f++){
                            if(f != c.current ){
                                d.append('<a href="javascript:;" class="pageNum">' + f + "</a>");
                            }else{
                                d.append('<span class="current">' + f + "</span>");
                            }
                        }
                        d.append('<span class="point">...</span>');
                        d.append('<a href="javascript:;" class="pageNum">' + c.pageCount + "</a>");
                    }
                    if(c.current >= l && c.current <= c.pageCount - r){
                        if(c.current != 1 && c.current >= r && c.pageCount != 4){
                            d.append('<a href="javascript:;" class="pageNum">' + 1 + "</a>");
                        }
                        if(c.current - 2 > 2 && c.current <= c.pageCount && c.pageCount > 5){
                            d.append('<span class="point">...</span>')
                        }
                        var g = c.current - 2,
                            e = c.current + 2;

                        if((g > 1 && c.current < l) || c.current ==1 ){
                            e++;
                        }
                        if(c.current > c.pageCount - r && c.current >= c.pageCount){
                            g--;
                        }
                        for(;g <= e; g++){
                            if(g != c.current){
                                d.append('<a href="javascript:;" class="pageNum">' + g + "</a>");
                            }else{
                                d.append('<span class="current">' + g + "</span>");
                            }
                        }
                        if(c.current + 2 < c.pageCount -1 && c.current >= 1 && c.pageCount >5){
                            d.append('<span class="point">...</span>');
                        }
                        if(c.current != c.pageCount && c.current < c.pageCount - 2 && c.pageCount != r){
                            d.append('<a href="javascript:;" class="pageNum">' + c.pageCount + "</a>");
                        }
                    }
                    if(c.current > c.pageCount - 4 && c.pageCount > 6 && c.current > 4){
                        d.append('<a href="javascript:;" class="pageNum">' + 1 + "</a>");
                        d.append('<span class="point">...</span>');
                        for(var f = c.pageCount - 4; f <= c.pageCount; f++){
                            if(f != c.current){
                                d.append('<a href="javascript:;" class="pageNum">' + f + "</a>");
                            }else{
                                d.append('<span class="current">' + f + "</span>")
                            }
                        }
                    }
                    if(c.current < c.pageCount){
                        d.append('<a href="javascript:;" class="nextPage">下一页></a>');
                    }else{
                        d.remove('.nextPage');
                        d.append('<span class="disabled">下一页></span>');
                    }
                }else if(c.pageCount == 1){
                    //d.append('<span class="disabled"><上一页></span>');
                    d.append('<span class="current">1</span>')
                    //d.append('<span class="disabled">下一页></span>');
                }
            })();
        },
        bindEvent: function(d, c){

            return(function(){
                //console.log(d);
                d.on('click','a.pageNum', function(){
                    var e = parseInt(a(this).text());
                    b.fillHtml(d,{
                        current:e,
                        pageCount: c.pageCount,
                        pageMax: c.pageMax

                    });
                    if(typeof (c.backFn) == "function"){
                        c.backFn(e);
                    }
                });
                d.on('click','a.prevPage', function(){
                    var e = parseInt(d.children("span.current").text());
                    b.fillHtml(d,{
                        current:e - 1,
                        pageCount: c.pageCount,
                        pageMax: c.pageMax


                    });
                    if (typeof(c.backFn) == "function") {
                        c.backFn(e - 1)
                    }
                });
                d.on("click", "a.nextPage", function() {
                    var e = parseInt(d.children("span.current").text());
                    //console.log(e);
                    b.fillHtml(d, {
                        current: e + 1,
                        pageCount: c.pageCount,
                        pageMax: c.pageMax

                    });
                    if (typeof(c.backFn) == "function") {
                        //console.log('nextPage call back' + e);
                        c.backFn(e + 1)
                    }
                });
            })();
        }
    }
    a.fn.createPage = function(d){
        var c = a.extend({
            pageCount:10,
            current:1,
            pageMax:6,
            backFn:function(){}
        },d);
        b.init(this, c);
    }
}(jQuery));

/*获取get参数 －－$_GET['param']*/
var $_GET = $_GET||(function(){
	var url = window.document.location.href.toString();
	var paramStr = url.split('?');
	var _get = {};
	if(typeof(paramStr[1]) == 'string'){
		var paramArr = paramStr[1].split('&');
		for(var i in paramArr){//console.log(paramArr);
			var param = paramArr[i].split('=');
			_get[param[0]] = param[1];
		}
		return _get;
	}else{
		return _get;
	}
})();

/*加载动画*/
/*
 * 自定义样式loadAnimate.selector().set({CSS:sty});可选  不选为默认样式
 * loadAnimate.selector().showLoad(); 显示动画
 * loadAnimate.selector().closeLoad();关闭动画
 * 支持链式操作 但selector需先操作选择选择器
 * */
var loadAnimate = {};
(function(){
	var selector = {};
    //默认几种样式样式 可以通过设置函数添加样式或者外层css设置样式
	var defaultSty = {
		    minWidth:'20px',
            height:'20px',
            display:'none',
            backgroundRepeat:'no-repeat',
            backgroundImage:'url('+$conf.domain+'static/img/gif/load.gif)'
			};
	var getElementById = function(id){
	    selector = document.getElementById(id);
	    return this;
		}
	var setDefault = function(sty){
	    if (typeof(sty)=='undefined'||sty==''||sty==null)
		    return;
	    for(var key in sty)
		    defaultSty[key] = sty[key];
	    return this;
		};
	//var gifLoad =
	var showLoad  = function(){
	    if(typeof selector !=='object')
		    return false;
	    //加载样式
	    for(var attr in defaultSty)
		    selector.style[attr] = defaultSty[attr];
	    selector.style.display   = 'block';
	    return this;
		};
		//关闭加载动画
	var closeLoad = function(){
	    selector.style.display   = 'none';
	    return this;
		};
	return loadAnimate = {
			selector:getElementById,
		    set:setDefault,
		    showLoad:showLoad,
		    closeLoad:closeLoad
			};
}());


function tips(text, selector){
    var time = arguments[2] ? arguments[2] : 2000;
    var content = '<p>'+text+'</p>';
    var diaLog = dialog({
        content:content,
        skin:'alert-tips',
        fixed:true
    });
    if(selector)
        diaLog.show(selector);
    else
        diaLog.show();
    var interval = setInterval(function(){
        diaLog.close().remove();
        clearInterval(interval);
    },time);
}

/*
 * 浮层加载 包含模态和非模态
 * 加载模态   dialog.selector().show(tpl,true)
 * 加载非模态 dialog.selector().show(tpl,false)
 * 关闭      dialog.selector().close()
 * */
//var dialog = {};
//(function(){
//	var modal = {//模态框设置
//		    width:'100%',
//		    height:'100%',
//		    backgroundColor:'#999',
//		    zIndex:'9999'
//			};
//	var selector = {};
//    var select = function(id){
//        selector =  document.getElementById(id);
//        return this;
//        };//绑定对象
//    var css = {
//    	    top:'0px',
//    	    left:'0px',
//    	    position:'absolute',
//    	    display:'block',
//    	    };//默认CSS绑定
//    var show = function(tpl,mode){
//        $(selector).load(tpl);
//        if(mode==true){//选用模态框
//            for( var attr in modal )
//                selector.style[attr] = modal[attr];
//        }
//        for( var defAttr in css )
//            selector.style[defAttr] = css[defAttr];
//        return this;
//        };
//	var close = function(){
//		selector.style.display = 'none';
//		return this;
//		};
//
//	dialog = {
//		    selector:select,
//		    show:show,
//		    close:close
//			};
//})();

//解决IE8之类不支持getElementsByClassName
if (!document.getElementsByClassName) {
    document.getElementsByClassName = function (className, element) {
        var children = (element || document).getElementsByTagName('*');
        var elements = new Array();
        for (var i = 0; i < children.length; i++) {
            var child = children[i];
            var classNames = child.className.split(' ');
            for (var j = 0; j < classNames.length; j++) {
                if (classNames[j] == className) {
                    elements.push(child);
                    break;
                }
            }
        }
        return elements;
    };
}

//解决IE8不兼容 placeholder
 function inputPlaceholder(){
     if(!('placeholder' in document.createElement('input'))){
         $('input[placeholder][type=text], textarea[placeholder]').each(function () {
             var that = $(this);
             var text = that.attr('placeholder');
             if(that.val() == ''){
                 that.val(text).addClass('placeholder');
             }

             that.focus(function () {
                 if(that.val() === text){
                     that.val('').removeClass('placeholder');
                 }
             });

             that.blur(function () {
                if(that.val() === ''){
                    that.val(text).addClass('placeholder');
                }
             });
         });
     }
 }

 var numberFormat;
 (function(){
 	var units_array = ['','十','百','千','万','十万','百万','千万','亿'];
 	var numberFn = function(number,units,decimal){
 		//number=数字 units＝量级1,2... decimal＝保留小数点后几位
 		decimal = (typeof decimal == 'undefined')?0:decimal;
 		var retStr = (number/(Math.pow(10,units))).toFixed(decimal);
 		return retStr + units_array[units];
 	}
 	numberFormat = function(number,decimal){
 		var decimal = (typeof(decimal)!=null)?decimal:0;
 		if(typeof number != 'number')
 			return number;
 		if(number<=10000)
 			return digitsFormat(number);
 		else if(number>10000&&number<=100000000)
 			return numberFn(number,4,decimal);
 		else if(number>100000000)
 			return numberFn(number,8,decimal);
 	}
 }())

function digitsFormat(num, float){
    var fixed = arguments[2] ? arguments[2] : 2;
    num = float ? parseFloat(num).toFixed(fixed) + '' : num + '';
    var tmp = num;
    var num = tmp.split('.')[0];
    var decimals = tmp.split('.')[1];
    //console.log(decimals);
    var out = num.length > 3 ? num.length % 3 : 0;
    var pre = num.slice(0, out);
    var num = num.slice(out);

    pre = out ? pre + ',' : '';
    decimals = float ? "." + decimals : '';

    return pre + num.replace(/\d{1,3}(?=(\d{3})+(\.\d*)?)/g, '$&,') + decimals;
}

function angleImage(node){
	var image = (typeof node!='object')?$('.'+$conf.angleImage):node.find('.'+$conf.angleImage);

	var width = typeof(arguments[1])!='undefined'?arguments[1]:parseInt(image.width());

    var height = width * 16 / 9;

    var marginTop = -(height / 2);

    image.css({
        'height':height + 'px',
        'position':'relative',
        'top' : '50%' ,
        'margin-top' : marginTop + 'px'
    });
}
//加载错误处理函数
function imgLoadErr(key){
	var img = event.srcElement;
	img.src = $src[key];
	mg.onerror=null;//防止加载循环
}



//运行swf 方法
function runSwfFunction(objName, callfunc){
    var param = [].slice.call(arguments, 2);
    var swfobj = swfobject.getObjectById(objName);
    if(swfobj){
        console.log('right now to get swfobject ' + objName + ' in time ' + new Date().getTime());
        percentLoad(swfobj);
    }else{
        //return false;
        var interval = setInterval(function(){
            if(swfobject.getObjectById(objName)){
                clearInterval(interval);
                swfobj = swfobject.getObjectById(objName);
                console.log('interval to get swfobject ' + objName + ' in time ' + new Date().getTime());
                percentLoad(swfobj);
            }
        }, 100);
    }

    function percentLoad(swfobj){
        if(swfobj.PercentLoaded() == 100){
            console.log('right now to run doCallBack ' + objName + ' in time ' + new Date().getTime());
            doCallBack(callfunc,swfobj, param);
        }else{
            //return false;
            var pInterval = setInterval(function(){
                if(swfobj.PercentLoaded() == 100){
                    clearInterval(pInterval);
                    console.log('interval to run doCallBack ' + objName + ' in time ' + new Date().getTime());
                    doCallBack(callfunc, swfobj, param);
                }
            },100);
        }
    }



    function doCallBack(fn, obj, args){
        console.log('call  ' + fn + ' function in time ' + new Date().getTime());
        var func;
        try{
            obj && (func = obj[fn]) && func.apply(obj, args);window.myswfobj = obj;window.func = func;console.log(args);
        }catch(e){
            console.log(e);

        }
    }
}

function hpRandom(m,n){
    return parseInt(Math.random() * (n - m + 1) + m);
}

function supportCss3(style) {
    var prefix = ['webkit', 'Moz', 'ms', 'o'],
        i,
        humpString = [],
        htmlStyle = document.documentElement.style,
        _toHumb = function (string) {
            return string.replace(/-(\w)/g, function ($0, $1) {
                return $1.toUpperCase();
            });
        };

    for (i in prefix)
        humpString.push(_toHumb(prefix[i] + '-' + style));

    humpString.push(_toHumb(style));

    for (i in humpString)
        if (humpString[i] in htmlStyle) return true;

    return false;
}

function getShareContent(channel,liveTitle,anchorNick,luid,suid,isWxShare){

    var isAnchor = false;
    if(suid && suid == luid){
        isAnchor = true;
    }
    var anchorTitle = ['老司机带你飞，快来欢朋直播观战吧！','本宝宝在欢朋开播了！别说我没提醒你哦～','直播不易～玩的溜不溜，都跪求真爱！'];
    var anchorContent = ['我在#欢朋直播#，等你来玩哦！','我在#欢朋直播#，一起来嗨吧','我在#欢朋直播#，快来打赏吧！'];
    var userTitle = ['玩最好的手游，看最嗨的直播！','欢朋直播，传递激情、传递真爱！','欢朋直播，精彩不容错过～'];
    var userContent = ['我正在观看“'+anchorNick+'”的直播，一起观战吧','我正在观看“'+anchorNick+'”的直播，一起嗨吧!','我正在观看“'+anchorNick+'”的直播，等你来玩哦!'];

    var title = '';
    if(channel == 'weibo'){
        title = "【"+liveTitle+"】 来自 @欢朋直播 精彩手游直播平台！";
    }
    if(channel == 'wechat'){
        title = "【"+liveTitle+"】";
    }
    var index = hpRandom(0, 2);
    var url = $conf.domain+'sharer.php?luid='+luid
    if(isWxShare){
        url = $conf.domain + 'h5share/live.php?u='+luid;
    }

    if(channel == 'wechat'){
        url = url + '&suid='+suid + '&channel=wechat';
        if(isAnchor){
            return {
                url:url,
                title:anchorTitle[index],
                content:anchorContent[index] + title
            }
        }else{
            return{
                url:url,
                title:userTitle[index],
                content:userContent[index] + title
            }
        }
    }

    if(channel == 'wechat-qq'){
        url = url ;//+ '&suid='+suid + '&channel=wechat';
        if(isAnchor){
            return {
                url:url,
                title:anchorTitle[index],
                content:anchorContent[index] + title
            }
        }else{
            return {
                url:url,
                title:userTitle[index],
                content:userContent[index] + title
            }
        }
    }


    if(isAnchor){
        return {
            url:url,
            title:anchorTitle[index] + anchorContent[index] + title,
            content:anchorTitle[index] + anchorContent[index] + title
        }
    }else{
        return {
            url:url,
            title:userTitle[index] +  userContent[index] + title,
            content:userTitle[index] +  userContent[index] + title
        }
    }


}

//share.js
var Share;
(function () {
    var a = jQuery;
    var u = {
        'tsina': 'http://service.weibo.com/share/share.php?',
        'tqq': 'http://connect.qq.com/widget/shareqq/index.html?',
        'tqzone': 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?'
    };
    var defaults = {
        url: '',
        title: '',//document.title,//分享的文字内容
        count: '0',
        sumary: '',//摘要
        desc: '',//qq空间的主要描述（发布理由）
        language: 'zh_cn',
        searchPic: 'true',
        rnd: new Date().valueOf(),
        site: '',//来源 腾讯
        pic: '',
        pics:''
    };

    Share = {
        options: {},
        init: function (option, c) {
            var self = this;
            // self.options = a.extend({},defaults, option);
            var p = a.extend({},defaults, option);
            //console.log(self.options);
            // var p = self.options;
            console.log(option);
            console.log(p);
            var tmp = [];
            for (var v in p) {
                tmp.push(v + '=' + encodeURIComponent(p[v] || ''));
            }

            if (c.channel == 'wx') {
                self.wx_qrcode(c.top, c.left);
            } else {
                var url = u[c.channel] + tmp.join('&');
                window.open(url);
            }
        },
        wx_qrcode: function (top, left) {
            var self = this;
            if (self.qrcode)
                self.qrcode.remove();

            self.qrcode = a('<div/>', {
                'class': 'wx_share_dialog'
            }).css({
                'z-index': '10001',
                'background-color': '#fff',
                'position': 'absolute',
                'border': '1px solid #fefefe',
                'padding': '10px',
                'left': left,
                'top': top
            }).appendTo(document.body);
            a('.wx_share_dialog').append('<div class="wx_share_dialog_head"> <span>分享到微信朋友圈</span> <a href="javascript:;" class="wx_share_dialog_close">X</a> </div><div id="wx_qrcode"></div>')
            a('.wx_share_dialog_head').css({
                'font-size': '12px',
                'font-weight': 'bold',
                'position': 'relative',
                'color': '#000',
                'text-align': 'left',
                'height': '16px'
            });
            a('.wx_share_dialog_close').css({
                'width': '16px',
                'height': '16px',
                'position': 'absolute',
                'right': '0',
                'top': '0',
                'color': '#999',
                'text-decoration': 'none',
                'font-size': '16px'
            });

            a('#wx_qrcode').qrcode({
                render: 'table',
                text: $conf.domanin + 'h5share/live.php?u='+$ROOM.anchorUserID
            });
            a('.wx_share_dialog_close').bind('click', function () {
                a('.wx_share_dialog').remove();
            })
        }
    }
}());

//for h5 html set page title
function getTitle(){
    if(isIphoneClient()){
        return pageTitle;
    }
    if(window.phonePlus){
        try {
            window.phonePlus.setTitle(pageTitle);
        }catch(e){
            console.log(e);
        }
    }
}
function isIphoneClient(){
	var ua = navigator.userAgent.toLowerCase();
	return /(iphone|ipad|ipod)/.test(ua);
}

var lazyLoad = {
    wrapper:null,
    init: function(id) {
        var that = this;
        that.onerrorImgUrl = "data-error";
        that.srcStore = "hp-src";
        that.class = "lazy-img";

        that.sensitivity = 50;
        minScroll = 5;
        slowScrollTime = 200;

        this.wrapper = document.querySelector(id)||document;

        this.wrapper.addEventListener("scroll", function() {
            that.changeimg();
        });

        setTimeout(function() {
            that.trigger();
        }, 500);

    },
    scanImage: function() {
        var that = this;
        var imgList = [];
        var allimg = [].slice.call(document.querySelectorAll('img.' + that.class + ''));
        allimg.forEach(function(ele) {
            if (!that.isLoadedImageCompleted(ele)) {
                imgList.push(ele);
            }
        });

        that.imglistArr = imgList;
    },
    isLoadedImageCompleted: function(ele) {
        return (ele.getAttribute('data-loaded') == '1');
    },
    trigger: function() {
        var that = this;

        eventType = that.isPhone && "touchend" || "scroll";
        that.fireEvent(this.wrapper, eventType);
        //$(window).trigger(eventType);
    },
    fireEvent: function(element, event) {
        // 其他标准浏览器使用dispatchEvent方法
        var evt = document.createEvent('HTMLEvents');
        // initEvent接受3个参数：
        // 事件类型，是否冒泡，是否阻止浏览器的默认行为
        evt.initEvent(event, true, true);
        return !element.dispatchEvent(evt);
    },
    changeimg: function() {
        function loadYesOrno(img) {
            var windowPageYOffset = window.pageYOffset,
                windowPageYOffsetAddHeight = windowPageYOffset + window.innerHeight,
                imgOffsetTop = img.getBoundingClientRect().top + window.pageYOffset;
            return imgOffsetTop >= windowPageYOffset && imgOffsetTop - that.sensitivity <= windowPageYOffsetAddHeight;
        }

        function loadImg(img, index) {

            var imgUrl = img.getAttribute(that.srcStore);

            img.setAttribute("src", imgUrl);

            img.onload || (img.onload = function() {
                img.classList.remove(that.class);
                img.setAttribute('data-loaded', 1);

                that.imglistArr[index] = null;
                img.onerror = img.onload = null;
                }
                /*img.onerror = function() {
                    img.src = img.getAttribute(that.onerrorImgUrl);
                    img.classList.remove(that.class);
                    img.classList.add("lazy-err");
                    img.setAttribute('data-loaded', 0);

                    that.imglistArr[index] = null,
                        img.onerror = img.onload = null
            }*/);

            var newImgStack = [];
            that.imglistArr.forEach(function(ele) {

                //img标签可见并且加载未完成
                if (!that.isLoadedImageCompleted(ele)) {
                    newImgStack.push(ele);
                }
            });
            that.imglistArr = newImgStack;
            angleImage($conf.angleImage);
        }

        var that = this;
        that.scanImage();
        that.imglistArr.forEach(function(val, index) {

            if (!val) return;
            var img = val;
            if (!loadYesOrno(img) || that.isLoadedImageCompleted(img)) return;

            if (!img.getAttribute(that.srcStore)) return;

            loadImg(img, index);

        })

    }
};

function QueryStringList(a){
   var b = a;
   var c = new Object();
   if (b.indexOf("?") != -1) {
      var str = b.substr(1);
      strs = str.split("&");
      for(var i = 0; i < strs.length; i ++) {
         c[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
      }
   }
   return c;
}
function xssReplce(a) {
    a = a.replace(/<script>/g,'').replace(/alert\(/g,'').replace(/javascript/g,'').replace(/<\/script>/g,'');
    return a;
}

// 从 canvas 提取图片 image
function convertCanvasToImage(canvas) {
    //新Image对象，可以理解为DOM
    var image = new Image();
    // canvas.toDataURL 返回的是一串Base64编码的URL，当然,浏览器自己肯定支持
    // 指定格式 PNG
    image.src = canvas.toDataURL("image/png");
    return image;
}

//标记6.cn进入欢朋
(function () {
   //获取进入渠道标记
    var refDef = ['6cn','v.6.cn'];
    //var dataMain = $_GET['datamain'];
    //var ref = document.referrer
    if(refDef[0]==$_GET['datamain'] || document.referrer.indexOf(refDef[1])>-1)
        setCookie('datamain','6cn');
}());

//forIE
(function () {
    if(!document.addEventListener)
        document.write('<script src="'+$conf.domain+'/static/js/forIE.js"><\/script>');
}());

/*判断IE9以下浏览器给出提示*/
(function(window) {
    var theUA = window.navigator.userAgent.toLowerCase();
    if ((theUA.match(/msie\s\d+/) && theUA.match(/msie\s\d+/)[0]) || (theUA.match(/trident\s?\d+/) && theUA.match(/trident\s?\d+/)[0])) {
        var ieVersion = theUA.match(/msie\s\d+/)[0].match(/\d+/)[0] || theUA.match(/trident\s?\d+/)[0];
        if (ieVersion < 9) {
            document.write("<div id='browser-tcBox' style='width:100%;height:20px;background:#fde3cb;margin: auto;position: fixed;z-index:999999999;top: 48px; left: 0;font-size:14px;line-height:2px;text-indent: 5%;'>" +
                "<div class='tc_top' style='overflow: hidden;*zoom:1;'>" +
                "</div>" +
                "<div class='tc_bottom' style='padding:10px;'>" +
                "您的浏览器版本太低，建议升级浏览器或更<a href='http://www.google.cn/chrome/browser/desktop/index.html' style='color: #ff7800;'>换其它浏览器</a>进行观看" +
                "</div>" +
                "</div>");
            //document.execCommand("Stop");
        };
    }
})(window);

//登录modal 手机和密码 focus 清除error状态
$('.error #username').focus(function(){
    $(this).parent().removeClass('error');
    $(this).parent().find('#login-error-text-username').text('');

});

