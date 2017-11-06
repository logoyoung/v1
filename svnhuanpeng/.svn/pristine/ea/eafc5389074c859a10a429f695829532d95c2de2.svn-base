/**
 * 后台公用js
 * date 2015-12-10
 */
(function(window, undefined){
  Op_common = window.Op_common || {};
  Op_common.loadQueue = [];
  Op_common.$win = $(window);
  Op_common.$bd = $('body');
  Op_common.initParams = window.Conf || {}; 

  /**
   * 常用的正则
   * @type {Object}
   */
  Op_common.textReg = {
    nameReg:/^[\u4E00-\u9FA5a-zA-Z0-9_]{1,20}$/,//用户名,中文字母数字下划线，1~20
    descReg:/(^\s*)|(\s*$)/g,//匹配空白字符
    detailUrlReg:/\/[0-9]+\/[0-9]+/,//url匹配
    picSuffix_s:/(.jpg|.png|.jpeg|.emf)/,//图片后缀匹配
    pattern:/^[\u4e00-\u9fa5]+$/,//中文匹配
    url:/^https?\:\/\//,//url匹配
    phone:/^(1[34578]\d{9})$/,//手机号
    email:/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/,//邮箱
    bankcard:/^[1-9]\d{15,18}$/,//银行卡
    Idcard:/^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/,//身份证验证
    num:/^\d+(\.\d+)?$/,//纯数字匹配
  };
  Op_common.test = function(txt,type){
      type = this.textReg[type];
      if(txt && type){
          return type.test(txt);
      }
  }
  /* 简单的模版方法，可以定制模版类型
   * eg.
   *  var html = '<div class="demo">{name}</div>'
   *    , data = {name: 'Hello world!'}
   *  html = Op_common.template(html, data) 
   *  -> '<div class="demo">Hello world!</div>'
   */
  Op_common.template = function (str, data){
    var fn = !/\W/.test(str) ? Op_common.template(document.getElementById(str).innerHTML) :
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +
        "with(obj){p.push('" +
        str
          .replace(/[\r\t\n]/g, " ")
          .split("\{").join("\t")
          .replace(/((^|\})[^\t]*)'/g, "$1\r")
          .replace(/\t(.*?)\}/g, "',$1,'")
          .split("\t").join("');")
          .split("\}").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");
    
    return data ? fn( data ) : fn;
  };
  /**
   * 返回当前url或设置跳转
   * @param  {[type]} url [url] 没有直接返回当前url
   * @return {[type]}     [description]
   */
  Op_common.locate = function( url ){
    if(!!!url){
      return window.location.href;
    }else{
      window.location.href = url;
    }
  };
  /**
   * 对象合并
   * @param  {[type]} o [合并到的对象]
   * @param  {[type]} p [被合并的对象]
   * @return {[type]}   [description]
   */
  Op_common.merge = function( o , p ){
    for(var attr in p ){
      o[ attr ] = p[ attr ];
    }
    return o ;
  };
  /**
   * 参数化url
   * eg:
   *  var url = http://test.v.cuctv.com/index.php?m=video&c=special&a=get_special&specialid=80
   *  Op_common.params(rul) 
   *  => {m: 'video', c: 'special', a: 'get_special', specialid: '80'}
   */
  Op_common.params = function( u ){
    u = u || location.search;
    if( !u ){ return false; }
    var p = u.split("\?");
    var o = {};
    p = p.length ==2 ? p[1] : p[0];
    var list = p.split("\&");
    for(var i = 0; i < list.length; i++){
      var m = list[i],name = '';
      m = m.split("\=");
      o[m[0]] = m.slice(1, m.length).join("=") || "";
    }
    return o ;
  };
  /**
   * 对象参数序列化
   * @param  {[type]} para [需序列化的对象]
   * @return {[type]}      [description]
   */
  Op_common.decodeParams = function ( para ){
    if( typeof para !== 'object' ){
      return para;
    }else{
      var html = [];
      for(var p in para ){
        html.push( p + "=" + encodeURIComponent( para[p]) );
      }
      return html.join("&");
    }
  };
  /**
   * 数组去重
   * @param  {[type]} obj [数组对象]
   * @return {[type]}     [description]
   */
  Op_common.distinctEl = function(obj){
    var tempArray = [];
    var temp = "";
    var index = 0;
    for(var i = 0; i < obj.length; i++){
      temp = obj[i];
      for(var j = 0; j < tempArray.length; j++){
        if(temp == tempArray[j]){
          temp = "";
          break;
        }
      }
      if(temp == null || temp != ""){
        tempArray[index] = temp;
        index++;
      }
    }
    return tempArray;
  };
  /**
   * HTML编码
   * @param  {[string]} text [需编码的字符串]
   * @return {[type]}      [description]
   */
  Op_common.encodeHTML = function(text){
    return String(text).replace(/["<>&]/g, function(all){
      return "&" + {
        '"': 'quot',
        '<': 'lt',
        '>': 'gt',
        '&': 'amp'
      }[all] + ";";
    });
  };
  /**
   * 模拟点击
   * @param  {[type]} item   [模拟点击的对象元素]
   * @param  {[type]} tolink [需要跳转的链接地址]
   * @return {[type]}        [description]
   */
  Op_common.virtualClick = function(item,tolink){
    if(!tolink||!item) return;
    var toBlank = item.find('[link-clk]');
    if(!toBlank.length){
      location.href = tolink;
    }else{//新窗口弹出
      toBlank.get(0).click();
    }
  };
  /**
   * json跨域
   * 默认的服务器回调方法：callback
   * @param  {Date}   ){                 var seq [description]
   * @return {[type]}     [description]
   */
  Op_common.getJSON = (function(){
    var seq = new Date() * 1;
    return function (url, params, callback, mp){
      var jsonp = 'Op_commonJsonp' + seq++
        , head = document.getElementsByTagName('head')[0]
        , script = document.createElement('script');

      for(var key in params){
        url += (/\?/.test( url ) ? '&' : '?') + key + '=' + encodeURIComponent(params[key]);
      }

      url += '&callback=' + jsonp;
      if(mp) url += '&mp=' + mp;

      window[jsonp] = function(data){
        window[jsonp] = undefined;
        try{
          delete window[jsonp];
        }catch(e){}

        if(head){
          head.removeChild(script);
        }
        callback(data);
      };

      script.charset = "UTF-8";
      script.src = url;
      head.appendChild(script);
    };
  }());
  /**
   * ajax调取通用方法
   * @param  {[type]}   url     [请求地址]
   * @param  {[type]}   data    [请求数据]
   * @param  {Function} fn      [成功回调方法]
   * @param  {[type]}   noFn    [失败回调方法]
   * @param  {Boolean}  isAsync [是否异步，默认异步]
   * @return {[type]}           [同步情况下返回数据]
   */
  Op_common.postData = function(url,data,fn,noFn,isAsync){
    if(!url) return;
    var returnData = {};
    !data.noLoad&&Op_common.showLoad();
    $.ajax({
      type: data.ajaxType||'POST',
      url: url,
      data: data,
      async:isAsync!==undefined?isAsync:true,
      success:function(res){
        var res = (typeof res=='string')?JSON.parse(res):res;
        if(res.status == 1||res.status == 'succ'||res.errcode==0 || res.code == 1){
          fn ? fn(res) : Op_common.popS();
          if(!isAsync){
            returnData = res;
          }
        }else{
          noFn ? noFn(res):Op_common.popE(res.msg||res.errmsg);
        }
        !data.noLoad&&Op_common.hideLoad();
      }
    });
    return returnData;
  },
  /**
   * 元素垂直居中
   * @param  {[type]} obj [description]
   * @return {[type]}     [description]
   */
  Op_common.vhCenter = function (obj,offleft) {
    var oW, oH, wW, wH, sT;
    oW = obj.width();
    oH = obj.height();
    wW = $(window).width();
    wH = $(window).height();
    sT = $(window).scrollTop(),
    t = (wH - oH) / 2 + sT,
    ox = offleft || 0;
    obj.css({
        left: ((wW - oW) / 2+ ox) + "px",
        top: (t<0?0:t) + "px"
    });
    $(window).scrollTop(sT);
  };
  /**
   * ajax等待图标添加
   * @param {[type]} obj   [需要添加等待图标的元素]
   * @param {[type]} objid [等待图片id，可选]
   */
  Op_common.setLoadImgPos = function (obj, objid) {
      var x, y, w, h, iW, iH, $img, imgId, imgS;
      imgId = "tempImg" + objid;
      imgS = '<img class="vM" id="' + imgId + '" src="http://res2.esf.leju.com/Op_common/statics/images/loading.gif" alt="loading..."/>'
      Op_common.$bd.append(imgS);
      $img = $("#" + imgId);
      x = obj.offset().left;
      y = obj.offset().top;
      w = obj.outerWidth();
      h = obj.outerHeight();
      iW = $img.width();
      iH = $img.height();
      $img.show().css({ top: y + (h - iH) / 2, left: x + (w - iW) / 2, position: "absolute", zIndex: "9999" });
      return false;
  };
  // JS Cookie操作（设置，读取，删除）
  Op_common.setCookie = function(name,value,time,domain){
    var date = new Date(),
    domain = domain?domain:'';
    date.setTime(date.getTime() + time*1000);
    document.cookie = name + "=" + value + "; expires=" + date.toGMTString()+"; path=/;domain="+domain;
  };
  Op_common.delCookie = function(name, path, domain){
    Op_common.setCookie( name, '', 'Thu, 01 Jan 1970 00:00:00 GMT', path, domain );
  };
  Op_common.getCookie = function(name){ 
    var search = name + "=";
    if(document.cookie.length > 0){ 
      offset = document.cookie.indexOf(search); 
        if(offset != -1){ 
          offset += search.length; 
          end = document.cookie.indexOf(";",offset); 
          if(end == -1) end = document.cookie.length;
          return document.cookie.substring(offset, end); 
        }else{
          return ""; 
        }
    }else{
      return "";
    }
  };

  /**
   * js延迟加载
   */
  //入列
  Op_common.queue = function( data ){
    Op_common.loadQueue.push( data );
    if( Op_common.loadQueue[0] !== 'runing' ){
      Op_common.dequeue();
    }
  };
  // 出列 
  Op_common.dequeue = function(){
    var fn = Op_common.loadQueue.shift();
    if( fn === 'runing' ){
      fn = Op_common.loadQueue.shift();
    }
    if( fn ){
      Op_common.loadQueue.unshift( 'runing' );
      fn();
    }
  };
  Op_common.loadTextArea = function(idString){
    if(!idString) return;
    $(idString).each(function(){
      var $this = $(this);
      if( $this.text()=='') return;
      if($this.text().indexOf('<iframe')==-1){
          Op_common.queue(function(){
            Op_common.loadScript( $this.get(0) );
          });
      }else{
          $this.after($($this.text())).remove();
      }
    });
  };
  /**
   * 重写document.write实现无阻塞加载script
   * @param { Dom Object } textarea元素
   */
  Op_common.loadScript = function( elem ){
    var url = elem.value.match( /src="([\s\S]*?)"/i )[1],
        parent = elem.parentNode,
        // 缓存原生的document.write
        docWrite = document.write,  
        // 创建一个新script来加载
        script = document.createElement( 'script' ), 
        head = document.head || 
            document.getElementsByTagName( 'head' )[0] || 
            document.documentElement;
    
    // 重写document.writedd
    document.write = function( text ){
        parent.innerHTML = text;
    };

    script.type = 'text/javascript';
    script.src = url;
    
    script.onerror = 
    script.onload = 
    script.onreadystatechange = function( e ){
        e = e || window.event;
        if( !script.readyState || 
        /loaded|complete/.test(script.readyState) ||
        e === 'error'
        ){

            // 恢复原生的document.write
            document.write = docWrite;
            head.removeChild( script );
            
            // 卸载事件和断开DOM的引用
            // 尽量避免内存泄漏
            head =          
            parent = 
            elem =
            script = 
            script.onerror = 
            script.onload = 
            script.onreadystatechange = null;
            Op_common.dequeue();
        }
    }
    
    // 加载script
    head.insertBefore( script, head.firstChild );
  };
  Op_common.showLoad = function(){
        this.loadHandle=dialog({
            title: 'loading',
            cancel: false,
        }).showModal();
  };
  Op_common.hideLoad = function(){
      if(this.loadHandle){
          this.loadHandle.close();
      }
  };
  /********** Ajax *************/
  Op_common.ajax = function(url,data,fn,opt){
      opt = $.extend({
            modal:true,
        },opt);
        if(opt.modal){this.showLoad();}
        if(typeof data=='object'){
            data=this.decodeParams(data);
        }
        $.ajax($.extend({
            url:url||'',
            data:data||'',
            beforeSend:Op_common.hideLoad,
            success:function(res){
                if(typeof Op_common.loadHandle=='object'){
                    Op_common.hideLoad();
                }
                if(typeof fn=='function'){
                    fn(res);
                }
            },
            type:'POST',
            dataType:'JSON',
        },opt));
  };
  Op_common.ajaxForm = function(jq,fn,opt){
        if(!jq)return;
        opt=$.extend({before:null},opt);
        jq.submit(function(e){
            e.preventDefault();
            var data=$(this).iserialize(),empty=function(field){
                var idx=data.indexOf(field+'=');
                return idx===-1 || ['&',''].indexOf(data.slice(idx-1,idx))===-1 || ['&',''].indexOf(data.slice(++idx+field.length,++idx+field.length))!==-1;
            };
            //验证规则
            if(data && opt.rule){
                $.each(opt.rule,function(k,v){
                    if(v.require && !empty(k))return true;
                    var dom=jq.find('*[name='+k+']');
                    if(v.focus===undefined || v.focus){
                        if(dom.is('input')){
                            dom.focus();   
                        }
                    }
                    //长度
                    if(v.length && !empty(k)){
                        if(dom.val().length==''){
                            dom.focus();
                        }else{
                            return true;
                        }
                    }
                    //电话
                    if(v.phone && !empty(k)){
                        if(!Op_common.test(dom.val(),'phone')){
                            dom.focus();
                        }else{
                            return true;
                        }
                    }
                    //身份证
                    if(v.Idcard && !empty(k)){
                        if(!Op_common.test(dom.val(),'Idcard')){
                            dom.focus();
                        }else{
                            return true;
                        }
                    }
                    //金额
                    if(v.money && !empty(k)){
                        if(!Op_common.test(dom.val(),'num')){
                            dom.focus();
                        }else if(dom.val()<1000){
                            dom.focus();
                        }else if(dom.val()>10000000){
                            dom.focus();
                        }else{
                           return true; 
                        }
                    }
                    //邮箱
                    if(v.email && !empty(k)){
                        if(!Op_common.test(dom.val(),'email')){
                            dom.focus();
                        }else{
                            return true;
                        }
                    }
                    
                    if(v.error===undefined || v.error){
                        dom.closest('.control-group').addClass('error');
                    }
                    Op_common.alert(v.msg);
                    data = null;
                    return false;
                });
            }
            if(data && opt.before && $.isFunction(opt.before)){
                data = opt.before.apply({
                    empty:empty
                },[data]);
            }
            if(data){
                Op_common.ajax(this.attributes['action']?this.attributes['action'].value:window.location.href,data,fn);
            }
        });
  };
  
  Op_common.autohideerror = function(jq){
    jq = jq||$('body')
    $(jq).on('keydown','.error input,.error select',function(){
        $(this).closest('.error').removeClass('error'); 
    });
    $(jq).on('focus','.error input,.error select',function(){
        $(this).closest('.error').removeClass('error'); 
    });
  }
  
  /********** 模拟弹框系列 *************/
  Op_common.alert = function(msg,callback,opt){
    opt = opt||{};
    dialog($.extend({
      title: '提示',
      content: msg,
      width: 260,
      ok:callback||function () {},
      fixed: true,
    },opt)).show();
  };
  Op_common.confirm = function (msg,okcallback,nocallback,opt){
      dialog($.extend({
          title: '提示',
          content: msg,
          okValue: '确定',
          ok: okcallback||function () {},
          cancelValue: '取消',
          cancel: nocallback||function () {},
          width: 260,
          fixed: true,
      },opt)).show();
  };
  Op_common.msg = function (msg,callback,opt){
      var opt=$.extend({
          fixed: true,
          content: msg,
          time:2000,
      },opt)
      var dia=dialog(opt).show();
      setTimeout(function () {
          dia.close().remove();
          if($.type(callback) === 'function'){
            callback();
          }
      },opt.time);
  };
  Op_common.showModal = function(){
    dialog({
        fixed: true,
        title: 'loading...',
        cancel: false,
        //content: '<img src=http://res.op.cc/img/spinner.gif />'
    }).showModal();
  }
  /**
   * 初始化方法
   */
  Op_common.init = function(){
    
    //退出绑定
    $('#loginout').on('click',function(){
        Op_common.ajax('/public/logout','',function(){
            window.location.reload();
        });
    });
    
    //扩展Jq
    $.fn.extend({
        iserialize:function(){
            var s=this.serialize(),t;
            $(this).find('*[data-toggle=buttons-radio]').each(function(){
                t=$(this).find('.active').get(0);
                if(t){
                  s+='&'+t.name+'='+t.value;
                }
            });
            return s;
        }
    });
  };

  Op_common.init(); 
  
  window.Op_common = Op_common;
})(window);