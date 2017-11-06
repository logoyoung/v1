//系统配置文件
var conf = {};
(function(){
	var _http = document.location.protocol;
    var _domain = _http+'//' + document.domain + '/' ;
    var _api = _domain + 'api/';
    var _person = _domain + 'personal/';
    var _static = _domain + 'static';
    var _js = _static + 'js/';
    var _css = _static + 'css/';
    var _img = _static + 'img/';
    if(document.domain == 'dev.huanpeng.com'){
        var env = 'dev';
    }else{
        var env = 'pro';
    }

    var img = [];
    img['dev'] = _http+'//dev-img.huanpeng.com';
    img['pro'] = _http+'//img.huanpeng.com';
    
    var apiUrl = {
    		
    };
    
    //资源路径
    var _src = {
    		defaultUserPic:_img+'userface.png',//默认头像
    		defaultLivePicSP:_img+'vertical_screen.jpg',//竖屏直播默认图,
    		defaultLivePicHP:_img+'src/default/260x150.png'//横屏直播默认图
    //todo
    };
    
    var imgSize = 2 * 1024 * 1024;
    conf = {
        getConf:function(){
            return{
                angleImage:'angle_class',
                domain : _domain,
                api : _api,
                img : img[env],
                src : _src,
                person:_person,
                pushRoomID:1,
                maxUid:3000000000,
                group:{
                    own:5,
                    admin:4,
                    user:1
                },
                taskUrl:{
                    6:_person+'mp/certify_phone/',
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
                //defaultFace:"http://dev-img.huanpeng.com/5/e/5e49f1310263dae8f0bc3f484860f2ad.png",
                //defaultUserPic:"http://dev.huanpeng.com/main/static/img/userface.png",
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
                cookie:['_uid', '_enc', '_uinfo','_uproperty', '_unick','_uface'],
                isIE:navigator.appVersion.indexOf('MSIE') > 0 || navigator.appVersion.indexOf("Trident/7.0") > 0,
                isIE7:navigator.appVersion.indexOf('MSIE 7.0') > 0,
                isFF:navigator.appVersion.indexOf('Firefox') > 0
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


function geetest(conf, callBack){
    function handler(captchaObj){
        captchaObj.appendTo(conf.append);
        if(conf.product == 'popup'){
            captchaObj.onReady(function(){
                captchaObj.show();
            });
        }
        captchaObj.onSuccess(function(){
            captchaObj.hide();
            callBack && typeof callBack =='function' && (callBack(captchaObj.getValidate()));
        });
    }
    $.ajax({
        url: $conf.api+"code/geetest_api.php?rand="+Math.round(Math.random()*100),
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

    $.ajax($.extend({},defaultConf, conf));
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
    document.cookie = name + "=" + encodeURIComponent(value) + "; path=/main;domain=" + document.domain + ";";
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
        document.cookie = name + "=" + encodeURIComponent(cval) + ";expires=" + exp.toGMTString() + "; path=/main;domain=" + document.domain + ";";
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

    var p = /(13|15|18)[0-9]\d{8}|17[678]\d{8}/;
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
        if(parity[sum % 11] != code[17]){
            pass =  false;
            tip = '校验位错误';
        }
    }
    if(!pass) console.log(tip);
    return pass;
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
    myXMLHttpRequest = $.ajax({
        url:$conf.api + "getStreamList.php",
        type:'post',
        dataType:'json',
        data:{
            luid:luid
        },
        success:function(d){
            if(d.length == 0){
                return true;
            }
            var streamList = d.streamList;
            var orientation = d.orientation;
            var stream = d.stream;

            var rtmp = streamList[0] ? 'rtmp://'+streamList[0] : '';

            if(!streamList || !stream || !streamList[0]){
                runSwfFunction(obj, 'liveEnd', 0);
                runSwfFunction(obj, 'liveEnd', 0);
                return;
            }
            if(liveRoom){
                runSwfFunction(obj, "inputURL", stream, rtmp, liveRoom);
            }else{
                runSwfFunction(obj, "inputURL", stream, rtmp);
                //runSwfFunction(obj, "barrageURL", $conf.domain + 'static/flash/barrage.swf');
                var hostLiveID = getCookie('hostLiveID');
                if(hostLiveID && d.liveId == hostLiveID){
                    //runSwfFunction(obj, 'isHost', 0);
                    runSwfFunction(obj, 'setVolumeAuthority', 0, 1);
                    deleteCookie('hostLiveID');
                }
            }
            if(orientation == 0){
                runSwfFunction(obj, "angle", 1);
            }else{
                runSwfFunction(obj, "angle", 0);
            }

        }
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



function replace_em(str){
    str = str.replace(/\</g,'&lt;');
    str = str.replace(/\>/g,'&gt;');
    str = str.replace(/\n/g,'<br/>');
    str = str.replace(/\[em_([0-9]*)\]/g, function(word){

        var num = word.match(/\d{1,2}/g);
        num = num[0] ? num[0] : '';
        var str = word;
        num && num < 23 && (str='<img src="static/img/emoji/'+num+'.png" border="0" />');
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
        'recharge':'li-recharge'
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
                zIndex: 999,
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
        '-1009':{id:'username', type:'notFound'}
    };
    var htmlFunc = null;
    var bindMobileHtml = null;
    var isPage = false;
    loginFast = {
        //ModalHtml:{
        //    loginHeader:function(){
        //        return '<div class="loginHeader"> <ul class="login_select_tab"><li class="selected">登录</li><li>注册</li></ul><span class="loginModal-close" onClick="loginFast.cancel()"></span></div>';
        //    },
        //    loginConRight:function(){
        //        // $conf = conf.getConf(); var wblogin = $conf.domain + 'personal/oauth/signin/weibo/index.php'; var qqlogin = $conf.domain + 'personal/oauth/signin/qq/index.php'; var wxlogin = $conf.domain + 'personal/oauth/signin/weixin/index.php'; return '<div class="loginCon-right"><span class="title">用第三方账号登录</span><a href="'+wblogin+'"><span class="otherLogin wb"><span class="otherLogin-img"></span><span>微博登录</span></span></a><a href="'+qqlogin+'"><span class="otherLogin qq"><span class="otherLogin-img"></span><span>QQ登录</span></span></a><a href="'+wxlogin+'"><span class="otherLogin wx"><span class="otherLogin-img"></span><span>微信登录</span></span></a></div>';
        //        return '';
        //    },
        //    loginHtmlStr:function(){
        //        return '<div class="loginCon-left"> <div id="login_form" class="login_tab_con"> <div class="login-form-item"> <span class="login-input-icon login-icon-phone"> <i></i> </span> <input type="text" class="text input-item-text" id="username" placeholder="请输入手机号"> <div class="input-item-error-text" id="login-error-text-username"></div> </div> <div class="login-form-item"> <span class="login-input-icon login-icon-password"> <i></i> </span> <input type="password" class="text input-item-text" id="password" placeholder="输入密码"> <div class="input-item-error-text" id="login-error-text-password"></div> </div>  <div class="login-form-item button-container login-button-container"> <a id="loginsubmit" href="javascript:;" class="input-item-btn">登录</a> </div> <div class="login-form-item control-password"> <a href="javascript:;"class="toRegister">我要注册</a> <a href="">忘记密码?</a> </div> </div> </div> <div class="loginCon-foot"></div>';
        //    },
        //    regHtmlStr:function(){
        //        return '<div id="reg_form" class="login_tab_con"> <div class="login-form-item"> <span class="login-input-icon login-icon-phone"><i></i></span> <input type="text" class="text input-item-text" id="username" placeholder="请输入手机号"> <div class="input-item-error-text" id="login-error-text-username"></div> </div><div class="login-form-item identCode-item"> <span class="login-input-icon login-icon-text"> <i></i> </span> <input type="text" class="input-item-text" id="identCode" placeholder="验证码"> <div class="input-item-error-text" id="login-error-text-identCode"></div> <span class="login-identCode"> <img src="http://dev.huanpeng.com/main/a/code/registerCode.php?nowtime='+new Date().getTime()+'"> </span> </div> <div class="login-form-item mobileCode-item"> <span class="login-input-icon login-icon-msg"><i></i></span> <input type="text" class="text input-item-text" id="mobileCode" placeholder="验证码"> <div class="input-item-error-text" id="login-error-text-mobileCode"></div> <a href="javascript:;" id="reg-getMobileCode">获取验证码</a> </div> <div class="login-form-item"> <span class="login-input-icon login-icon-nick"><i></i></span> <input type="text" class="text input-item-text" id="usernick" placeholder="昵称"> <div class="input-item-error-text" id="login-error-text-usernick"></div> </div> <div class="login-form-item"> <span class="login-input-icon login-icon-password"> <i></i> </span> <input type="password" class="text input-item-text" id="password" placeholder="密码"> <div class="input-item-error-text" id="login-error-text-password"></div> </div>  <div class="login-form-item button-container login-button-container"> <a id="regsubmit" href="javascript:;" class="input-item-btn">注册</a> </div> <div class="login-form-item control-agreement"> <span href="javascript:;"class="">注册即代表同意 <a class="agreement-rule" href="">《欢朋TV用户协议及版权说明》</a></span> </div> <div class="loginCon-foot"></div> </div>';
        //    },
        //    loginFooter:function(){
        //        // return '<div class="loginFooter"></div>';
        //        return '';
        //    }
        //},
        set_pos:function(){//设置位置
            var f = a('#loginModal');
            var width = f.width() + parseInt(f.css('padding-left')) + parseInt(f.css('padding-right'));
            f.css('margin-left', -width/2 +'px');
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
            }else{
                b.initLogin();
            }
        },
        bindingMobile:function(){
            bindMobileHtml = bindMobileHtml ? bindMobileHtml:huanpeng.template('jsTemplate-bindMobileModal');
            this.createModal(bindMobileHtml(),{style:'width:400px;'});
            this.initHeader();
            this.initBindingMobile();
        },
        initBindingMobile:function(){
            var bindingForm = $('#bind_form');
            var self = this;
            var submit = $('#bindSubmit');
            var bindingDom = {
                username:bindingForm.find('#username'),
                mobileCode:bindingForm.find('#mobileCode'),
                getCode:$("#bind-getMobileCode"),
                password:bindingForm.find('#password'),
                password2:bindingForm.find('#password2')
            };
            bindingDom.username.bind('input prooertychange',function () {
                if($(this).val().length == 11){
                    self.checkPhoneNumber($(this).val());
                }
            });
            var lockGetMobileCode = 0;
            bindingDom.getCode.bind('click', function(){
                if(lockGetMobileCode==1){
                    return;
                }
                var username = bindingDom.username.hasClass('placeholder') ? '':bindingDom.username.val();
                if(!username){
                    self._error('username','empty');
                    return;
                }
                if(bindingDom.username.parents('.login-form-item').hasClass('error')){
                    return;
                }
                var iTime = 59;
                var Account;
                geetest({product:'popup',append:'#binding-captcha'}, function (data) {
                    var data = $.extend({mobile:username,type:'gt',from:'1'},data);
                    var url = $conf.api+'code/mobileCode.php'
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
                    });
                })
            });
            bindingForm.find('input').focus(function () {
                self._clearError($(this).parents('.login-form-item'));
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
                var url = $conf.api+'user/bindingMobile.php';

                data = $.extend({uid:getCookie('_uid'),encpass:getCookie('_enc')},data);
                ajaxRequest({url:url,data:data},function(){
                    location.href = location.href;
                },function (d) {
                    alert(d.desc);
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

            function toReg(){
                var tmpData = {
                    username:'mobile',
                    //identCode:'identCode',
                    mobileCode:'mobileCode',
                    usernick:'nick',
                    password:'password'
                }
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
                a.ajax({
                    url:$conf.api+'registered.php',
                    type:'post',
                    dataType:'json',
                    data:data,
                    success:function(o){
                        if(o.uid && o.encpass){
                            regSucc();
                        }else{
                            regErr();
                        }

                        function regSucc(){
                            setCookie('_uid', o.uid);
                            setCookie('_enc', o.encpass);
                            if(isPage){
                                var ref_url = $_GET['ref_url'] ? decodeURIComponent($_GET['ref_url']) : '/main/personal';
                                location.href = $conf.domain + ref_url;
                            }else{
                                location.href =location.href;
                            }

                        }
                        function regErr(){
                            self.enableForm();
                            self._clearError();
                            if(codeErrorStruct[o.code]){
                                self._error(codeErrorStruct[o.code].id, codeErrorStruct[o.code].type);
                            }
                        }
                    }
                });
            };

            regDom.username.bind('input prooertychange', function(){
                if($(this).val().length == 11){
                    self.checkPhoneNumber($(this).val());
                }
            });

            var lockGetMobileCode = 0;
            regDom.getCode.bind('click', function(){
                if(lockGetMobileCode == 1){
                    return;
                }
                var regUserName = regDom.username.hasClass('placeholder') ? '' :regDom.username.val();
                //var regIdentCode = regDom.identCode.hasClass('placeholder')? '' : regDom.identCode.val();
                if(!regUserName){
                    self._error('username', 'empty');
                    return;
                }
                //if(!regIdentCode){
                //    self._error('identCode', 'error');
                //    return;
                //}
                var iTime = 59;
                var Account;

                geetest({product:'popup',append:'#reg-captcha'}, function(data){
                    var data = $.extend({mobile:regUserName, type:'gt'}, data);
                    ajaxRequest({url:$conf.api + 'code/registerMobileCode.php',data:data,success:function(d){
                        if(d.isSuccess == 1){
                            lockGetMobileCode = 1;
                            RemainTime();
                        }else{
                            if(d.code && codeErrorStruct[d.code]){
                                self._error(codeErrorStruct[d.code].id, codeErrorStruct[d.code].type);
                            }
                        }
                    }});

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
                });

                //$.ajax({
                //    url:$conf.api + 'code/registerMobileCode.php',
                //    type:'post',
                //    dataType:'json',
                //    data:{
                //        mobile:regUserName,
                //        code:regIdentCode
                //    },
                //    success:function(d){
                //        if(d.isSuccess == 1){
                //            lockGetMobileCode = 1;
                //            RemainTime();
                //        }else{
                //            if(d.code && codeErrorStruct[d.code]){
                //                self._error(codeErrorStruct[d.code].id, codeErrorStruct[d.code].type);
                //            }
                //        }
                //    }
                //});
                //
                //var iTime = 59;
                //var Account;
                //function RemainTime(){
                //    var btn = regDom.getCode;
                //    //btn.attr('disabled', 'disabled');
                //    btn.addClass('disabled');
                //
                //    var iSecond, iMinute, sSecond = "", sTime = "";
                //    if(iTime >= 0){
                //        iSecond = parseInt(iTime % 60);
                //        iMinute = parseInt(iTime / 60);
                //
                //        if(iSecond > 0) {
                //            if (iMinute > 0) {
                //                sSecond = iMinute + "分钟" + iSecond + "s"
                //            } else {
                //                sSecond = iSecond + "s后重发";
                //            }
                //        }
                //        sTime = sSecond;
                //        if(iTime == 0){
                //            clearTimeout(Account);
                //            sTime = "获取验证码";
                //            iTime = 59;
                //            lockGetMobileCode = 0;
                //            btn.removeClass('disabled');
                //        }else{
                //            Account = setTimeout(RemainTime, 1000);
                //            iTime = iTime - 1;
                //        }
                //    }else{
                //        btn.removeClass('disabled');
                //        sTime = '获取验证码';
                //    }
                //    btn.text(sTime);
                //}
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
                        self.checkUserNick(val);
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
            var _domain = document.domain;
            var submit = a("#loginsubmit");

            var loginDom = {
                username:loginForm.find('#username'),
                password:loginForm.find('#password')
            }
            var identCodeOpen = false;
            var geeLoginValidateCode = false;


            var initIdentCode = function(){
                var url = $conf.api + 'code/logInCode.php';
                if(!geeLoginValidateCode){
                    clearIdentCode();
                    loginDom.password.parents('.login-form-item').after('<div class="login-form-item identCode-item"></div>');
                    geetest({product:'float',append:'.identCode-item'},function(data){
                        geeLoginValidateCode = data;
                    });
                    identCodeOpen = true;
                }
                return;

                if(loginForm.find('.identCode-item').get()[0]){
                    //loginForm.find('.identCode-item .login-identCode img').attr('src',url+'?nowtime='+new Date().getTime());
                }else{
                    loginDom.password.parents('.login-form-item').after('<div class="login-form-item identCode-item"></div>');
                    //loginDom.password.parents('.login-form-item').after('<div class="login-form-item identCode-item"> <span class="login-input-icon login-icon-text"> <i></i> </span> <input type="text" class="input-item-text" id="identCode" placeholder="验证码"> <div class="input-item-error-text" id="login-error-text-identCode"></div> <span class="login-identCode"> <img src="'+url+'?nowtime='+new Date().getTime()+'"> </span> </div>');
                    //loginDom.identCode = loginForm.find('#identCode');
                    //loginForm.find('.identCode-item .login-identCode img').bind('click', function(){
                    //    $(this).attr('src', url + '?nowtime='+new Date().getTime());
                    //});
                    geetest({product:'float',append:'.identCode-item'},function(data){
                        geeLoginValidateCode = data;
                    });
                }

                identCodeOpen = true;
            }
            var clearIdentCode = function(){
                loginForm.find('.identCode-item').remove();
                identCodeOpen = false;
            }
            //if(getCookie('_login_identCode_open') == 1){
            //    initIdentCode();
            //}

            var toLogin = function(){
                self._clearError();

                var tmpData = {
                    username:'userName',
                    password:'password'
                };

                //if(identCodeOpen){
                //    tmpData.identCode = "identCode";
                //}
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
                a.ajax({
                    url:$conf.api + "logIn.php",
                    type:'post',
                    dataType:'json',
                    data:data,
                    success: function(o){
                        geeLoginValidateCode = false;
                        if(o.uid && o.encpass){
                            loginSucc();
                        }else{
                            loginErr();
                        }
                        function loginSucc(){
                            setCookie('_uid', o.uid);
                            setCookie('_enc', o.encpass);
                            deleteCookie('_login_identCode_open');
                            if(isPage){
                                var ref_url = $_GET['ref_url'] ? decodeURIComponent($_GET['ref_url']) : '/personal';;
                                location.href =  $conf.domain + ref_url;
                            }else{
                                location.href = location.href;
                            }
                        }
                        function loginErr(){
                            self.enableForm();
                            if(codeErrorStruct[o.code]){
                                self._error(codeErrorStruct[o.code].id, codeErrorStruct[o.code].type);
                            }

                            if(o.code == '-4031' || o.code == -4061){
                                setCookie('_login_identCode_open', 1);
                                //if(o.code == '-4031'){
                                //    //loginForm.find('.identCode-item .login-identCode img').attr('src',$conf.api+'code/logInCode.php'+'?nowtime='+new Date().getTime());
                                //}
                            }
                            if(getCookie('_login_identCode_open') == 1){
                                initIdentCode();
                                //identCodeOpen = true;
                            }
                        }
                    },
                    error:function(e){
                        //console.log(e);
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
                        $.ajax({
                            url:$conf.api+ 'register/checkMoblieIsUsed.php',
                            type:'post',
                            dataType:'json',
                            data:{
                                mobile:val
                            },
                            success:function(){
                                if(getCookie('_login_identCode_open') == 1){
                                    initIdentCode();
                                }else{
                                    clearIdentCode();
                                }
                            }
                        });
                    }
                }

            });
            loginForm.find('.toRegister').bind('click', function(){
                self.login(1);
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
            var self = this;
            if(checkMobile(mobile)){
                $.ajax({
                    url:$conf.api+ 'register/checkMoblieIsUsed.php',
                    type:'post',
                    dataType:'json',
                    data:{
                        mobile:mobile
                    },
                    success:function(d){
                        if(d.isSuccess == 1){
                            self._error('username', 'used');
                        }
                    }
                });
            }else{
                this._error('username', 'format');
            }
        },
        checkUserNick:function(nick){
            var self = this;
            $.ajax({
                url:$conf.api+'register/checkNickIsUsed.php',
                type:'post',
                dataType:'json',
                data:{
                    nick:nick
                },
                success:function(d){
                    if(d.isSuccess == 1){
                        self._error('usernick', 'used');
                    }else if(d.code && codeErrorStruct[d.code]){
                        self._error(codeErrorStruct[d.code].id, codeErrorStruct[d.code].type);
                    }
                }
            });
        },
        _err:function(obj, text){
            obj.text(text);
            obj.parents('.login-form-item').removeClass('error').addClass('error');
        },
        _error:function(id, type){
            var text = this._errorID[id] + this._errorType[type];
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
            'length':'长度6-12位',
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
                    d.append('<span class="disabled"><下一页></span>');
                    d.append('<span class="current">1</span>')
                    d.append('<span class="disabled">下一页></span>');
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
            backgroundImage:'url('+$conf.domain+'/static/img/gif/load.gif)'
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
 		if(number<=9999)
 			return digitsFormat(number);
 		else if(number>9999&&number<=99999999)
 			return numberFn(number,4,decimal);
 		else if(number>99999999)
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
    //console.log(decimals);
    return pre + num.replace(/\d{1,3}(?=(\d{3})+(\.\d*)?)/g, '$&,') + decimals;
}

function angleImage(node){
	var image = (typeof node!='object')?$('.'+$conf.angleImage):node.find('.'+$conf.angleImage);
   // var image = $('.'$conf.angleImage);
    //var image = ($('.img_block.'+$conf.angleImage).length>0)?$('.img_block.'+$conf.angleImage):$('.imagecontainer.'+$conf.angleImage);
	var width = typeof(arguments[1])!='undefined'?arguments[1]:parseInt(image.width());
    var height = width * 16 / 9;
    //console.log(image);console.log(width);
    var position = -(height - parseInt(image.parent().height()))/2;
    //console.log(width);
    image.css({
        'height':height + 'px',
        'position':'relative',
        'top' : position + 'px'
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
        console.log('call  ' + fn + 'function in time ' + new Date().getTime());
        var func;
        try{
            obj && (func = obj[fn]) && func.apply(obj, args);
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
        url = $conf.domain+'h5share/live.php?u='+luid;
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
        url = url + '&suid='+suid + '&channel=wechat';
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
        return {url:url,title:anchorTitle[index] + anchorContent[index] + title}
    }else{
        return {url:url,title:userTitle[index] +  userContent[index] + title}
    }


}