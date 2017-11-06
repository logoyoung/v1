/**
 * Created by logoyoung on 16/11/18.
 */

/**
 * WEB接口映射表
 *
 * */

//var ret;
var API_MAP = {
	_unique:function (data){
		data = data || [];
		var a = {};
		for (var i=0; i<data.length; i++) {
			var v = data[i];
			if (typeof(a[v]) == 'undefined'){
				a[v] = 1;
			}
		};
		data.length=0;
		for (var i in a){
			data[data.length] = i;
		}
		return data;
	},
	_dataForm:function (data,filed) {
		var t = [];
		var ret = [].map.call(data,function (x) {//console.log(x)
			for(var p in filed){
				x[p] = x[filed[p]];
				//delete x[filed[p]];
			}
			return x;
		});
		//return this._unique(ret);
		return ret;
	},
	//获取分类游戏列表
	gameList:function (data) {
		var filed = {
			gameid:'gameID',
			gamename:'gameName'
		};
		var ret = {};
		ret.gameList = this._dataForm(data.content.list,filed);
		ret.ref = data.content.ref;
		ret.liveCount = data.content.total;
		return ret;
	},
	gameInfoList:function (data) {
		var filed = {
			gameID:'gameID',
			gameName:'gameName',
			posterURL:'poster',
			liveCount:'liveTotal'
		}
		var ret = {};
		ret.gameList = this._dataForm(data.content.list,filed);
		ret.allCount = data.content.total;
		return ret;
	},
	historyList:function (data) {
		var filed = {
			anchorNickName:'nick',
			anchorPicURL:'head',
			anchorUserID:'uid',
			roomID:'roomID',      //roomID 
			angle:'orientation',
			liveStartTime:'',
			liveStatus:'',
			liveTitle:'title',
			scanTime:'stime',
			viewerCount:'viewerTotal',
			poster:'poster',
			liveStatus:'isLiving'
		};
		var ret = {};
		ret.historyList = this._dataForm(data.content.list,filed);
		ret.count = data.content.total;
		return ret;
	},
	followList:function (data) {
		var filed = {
			anchorNickName:'nick',
			anchorPicURL:'head',
			anchorUserID:'uid',
			roomID:'roomID',      //roomID 
			scanTime:'stime',
			angle:'orientation',
			liveStartTime:'stime',
			liveTitle:'title',
			viewerCount:'viewCount',
			poster:'poster',
			liveStatus:'isLiving'
		};
		var ret = {};
		ret.followList = this._dataForm(data.content.list,filed);
		ret.liveCount = data.content.liveTotal;
		ret.allCount = data.content.allCount;
		return ret;
	},
	rankList:function (data) {
		var filed = {
			uid:'uid',
			nick:'nick',
			roomID:'roomID',      //roomID 
			anchorPicUrl:'head',
			isLive:'isLiving',
			level:'level',
			money:'money',
			status:'status'
		};
		var ret = {};
		ret.rankList = this._dataForm(data.content.list,filed);
		return ret;
	},
	guessList:function (data) {
		var filed = {
			angle:'orientation',
			fansCount:'fansCount',
			gameName:'gameName',
			ispic:'ispic',
			liveTitle:'title',
			luid:'uid',
			roomID:'roomID',      //roomID 
			nick:'nick',
			posterUrl:'poster',
			viewerCount:'userCount'
		};
		var ret = {};
		ret.guessList = this._dataForm(data.content.list,filed);
		return ret;
	},
	liveList:function (data) {
		var filed = {
			angle:'orientation',
			ctime:'stime',
			gameName:'gameName',
			ispic:'ispic',
			liveTitle:'title',
			luid:'uid',
			roomID:'roomID',      //roomID 
			nick:'nick',
			posterUrl:'poster',
			viewerCount:'viewCount'
		};
		var ret = {};
		ret.liveList = this._dataForm(data.content.list,filed);
		ret.liveCount = data.content.total;
		ret.ref = data.content.ref;
		return ret;
	},
	videoList:function (data) {
		var filed = {
			angle:'orientation',
			commentCount:'commentCount',
			gameName:'gameName',
			ispic:'ispic',
			posterUrl:'poster',
			videoId:'videoID',
			videoTitle:'title',
			viewCount:'viewCount',
			videoTimeLength:'videoTimeLength'
		};
		var ret = {};
		ret.liveList = this._dataForm(data.content.list,filed);
		ret.liveCount = data.content.total;
		ret.ref = data.content.ref;
		return ret;
	},
	getUserDetail:function(data){
		var filed = {
			loginStatus:'loginStatus',
			_uid:'_uid',
			_enc:'_enc',
			nickName:'nick',
			pic:'head',
			level:'level',
			integral:'integral',
			readsign:'unreadMsg',
			hpbean:'hpbean',
			hpcoin:'hpcoin',
			levelIntegral:'levelIntegral'
		};
		var ret = {};
		ret = this._dataForm(data.content,filed);
		return ret;
	}
	
}



/**
 *
 * 页头
 *
 * */
var head = function(scriptType,sizeChange){
	this._conf = $conf;
    this._LIVE = 100;//直播状态
    this._LIVE2 = 1;
	this._scriptType = (typeof(scriptType)=='undefined'||scriptType==null)?-1:parseInt(scriptType);
	this._sizeChange = (typeof(sizeChange)!='boolean')?true:sizeChange;
	this._pageWith = 1180;//首页分1180px和980px两版 
	this._w1Class = 'w1180';//宽版类
	this._w2Class = 'w980';//窄版类
	this._styClass = 'cur';
	this._changeObj = $('.navi #size');
	this._user = user;//用户游客对象

	this._historyCount = 5;//默认显示3条历史纪录
	this._followCount  = 3;//默认显示3条关注纪录
	this._xhrhistory;
	this._xhrfollow;//ajax对象
    this._defaultPic = $conf.defaultUserPic;
    this._jump = ['personal/login.php','/personal/register.php'];
	this._init();
};
head.prototype = {
	_init:function(){
		this._getUserPic();
		this._changePageType();	
		this._pageAnimation();
		this._login();
		this._logout();
		this._reg();
		this._search();
		this._getRecGame(12,2);
		//this._getHistoryList();
		//this._getFolllowList();
	},
	_pageAnimation:function(){
		var o = this;
		if(o._scriptType>-1)
		$('.navi ul li.txt:eq('+o._scriptType+')').addClass(o._styClass);
		$(window).resize(function(){
			o._changePageType();
		});
		//var searchObj = $('.navi input.search,.search_icon');
		
		/* searchObj.blur(function(){
			searchObj.removeClass(o._styClass);
		});
		searchObj.focus(function(){
			searchObj.addClass(o._styClass);
		}); */
		/* $('.right span .appdown .imgCode').qrcode({
			render:'table',
			text:'dev.huanpeng.com',
			width:150,
			height:150
		}); */
		$('.right .rightspan.h').mouseenter(function(){//alert(1);
			//$(this).addClass('cur');
		    o._getHistoryList();
		    return false;
			});
		/* $('.right span.icon3').mouseleave(function(){
			$(this).removeClass('cur'); 	
			}); */
		$('.right .rightspan.f').mouseenter(function(){
			//$(this).addClass('cur');
		    o._getFollowList();
		    return false;
			});
		/* $('.right span.icon4').mouseleave(function(){
			$(this).removeClass('cur');
			}); */
		/*$('.userpic.h_sup').mouseenter(function(){
			//$(this).addClass('cur');
		    o._getUserInfo();
		    return false;
			});*/
		
		$('.navi input').focus(function(){console.log('focus');
		//$(this).addClass('cur');
			$('.navi input,.navi ul li.last .search_icon .icon').addClass('cur');
		});
		$('.navi input').blur(function(){console.log('blur');
			$('.navi input,.navi ul li.last .search_icon .icon').removeClass('cur');
		});
	},
	_login:function(){
		var o = this;
		$(".login_reg.l,.to_log").click(function(){o._logreg(0);});
	},
	_reg:function(){
		var o = this;
		$(".login_reg.r,.to_reg").click(function(){o._logreg(1);});
	},
	_logout:function(){
	    $('#personal_info .p_option a:eq(1)').click(function(){logout_submit();	});
		},
	_logreg:function(t){
		var o = this;
		var lhref = document.location.href.split('?');
		var getParams = typeof lhref[1] =='undefined'?'':'?'+lhref[1];
		if(lhref[0].indexOf(o._jump[0])>-1||lhref[0].indexOf(o._jump[1])>-1)
			document.location.href = $conf.domain+o._jump[t]+getParams;
		else
			loginFast.login(t);
	},
	_search:function(){
		var o = this;
		$('.search_icon').click(function(){
			o._jumpToSearch();
		})
		$('input.search').keypress(function(e){
			if(e.keyCode=='13')
				o._jumpToSearch();
		});
		},
	_jumpToSearch:function(){
		var keyStr = $('input.search').val();//alert($('input.search')[0].defaultValue+'----');
		if(keyStr && keyStr != $('input.search')[0].defaultValue)
		document.location.href = $conf.domain+'search.php?key='+encodeURIComponent(keyStr);
		},
	_changePageType:function(){console.log(this._sizeChange);
		if(!this._sizeChange)
			return;
		var pageWidth = $(window).width();
		if(pageWidth>=this._pageWith){
			this._changeObj.removeClass(this._w2Class);
			this._changeObj.addClass(this._w1Class);
		}else{
			this._changeObj.removeClass(this._w1Class);
			this._changeObj.addClass(this._w2Class);
		}
	},
	//loadData:function(){},
	_getHistoryList:function(size){
		if(this._xhrhistory!=null)
			this._xhrhistory.abort();
		var o   = this;
		if( !o._user.loginStatus )
			return;//如果没有登录不用
		var domUl = $('.history ul');
		domUl.html('');
		loadAnimate.selector('history').showLoad();
		var size = (size==''||size==null||typeof(size)=='undefined')?o._historyCount:size;		
		var url = $conf.domain+'api/room/historyList.php';
		this._xhrhistory = 
		$.ajax({
			url:url,
			data:{uid:o._user._uid,encpass:o._user._enc,size:size},
			type:'post',
			dataType:'json',
			success:function(data){
				data = API_MAP.historyList(data);
				if(data.length==0)
					return true;
				data = data['historyList'];
				var domUlStr = '';
				var domLiStr = '';
				if(data.length>0){
				for(var key=0; key < data.length;key++){
				if(parseInt(data[key].liveStatus)==o._LIVE2){
					//domLiStr = '<li class="cur"><a href="'+o._conf['domain']+'room.php?luid='+data[key].anchorUserID+'">';
					domLiStr = '<li class="cur"><a href="'+o._conf['domain']+data[key].roomID+'">';
				}else{
					//domLiStr = '<li><a href="'+o._conf['domain']+'room.php?luid='+data[key].anchorUserID+'">';
					domLiStr = '<li><a href="'+o._conf['domain']+data[key].roomID+'">';
				}
				if(data[key].anchorPicURL){
					domLiStr +='<div class="pic"><img src="'+data[key].anchorPicURL+'"></div>';
				}else{
					domLiStr +='<div class="pic"><img src="'+o._userPic+'"></div>';
				}
				domLiStr += '<div class="detail">'
					       +'<div class="icon_set icon liveicon"></div>'
					       +'<div class="s">'+data[key].anchorNickName+'</div>'
					       +'<div class="icon_set icon historyicon"></div>'
					       +'<div class="x">'+calVisitTime(parseInt(data[key].scanTime))+'</div></div></a></li>';	
				if(key< data.length-1)
					domLiStr +='<div class="line"></div>';
				domUlStr += domLiStr;
			}
			//domUlStr += '<a href="'+o._conf['domain']+'personal/follow"><li class="more">更多</li></a>';
			}else{
				domUlStr = '<li class="more no_login">'
					     + '<div class="img_no_login">'
				         + '<img src="'+o._conf['domain']+'static/img/logo/home_no_login.png">'
				         + '</div><div class="txt_div">您还没有浏览历史哦</div></li>';
			}
			//console.log(domUlStr);
			loadAnimate.selector('history').closeLoad();
			domUl.html(domUlStr);
			}, 
			error:function(error){console.log('ajax request error for historyList on line 923')} 
		});
	},
	_getFollowList:function(size){
		if(this._xhrfollow!=null)
			this._xhrfollow.abort();
		var o   = this;
		if( !o._user.loginStatus )
			return;//如果没有登录不用
		var domUl = $('.lovelist ul');
		domUl.html('');
		loadAnimate.selector('follow').showLoad();
		var size = (size==''||size==null||typeof(size)=='undefined')?o._followCount:size;		
		var url = $conf.domain+'api/room/followList.php';
		this._xhrfollow = 
		$.ajax({
			url:url,
			data:{uid:o._user._uid,encpass:o._user._enc,size:size},
			type:'post',
			dataType:'json',
			success:function(data){
				data = API_MAP.followList(data);
				var liveCount = data['liveCount'];
				data = data['followList'];
				var domUlStr = '<div class="livecount">当前关注的有<em style="color:#ff7800;margin:0px 3px;font-style:normal">'+liveCount+'</em>个正在直播</div>';
				var domLiStr = '';//console.log(data)
				if(data.length>0){
					for(var key=0; key < data.length;key++){
						//domLiStr = '<li ><a href="'+o._conf['domain']+'room.php?luid='+data[key].anchorUserID+'">';
						domLiStr = '<li ><a href="'+o._conf['domain']+data[key].roomID+'">';
						if(data[key].anchorPicURL){
						domLiStr +='<div class="pic"><img src="'+data[key].anchorPicURL+'"></div>';
					}else{
						domLiStr +='<div class="pic"><img src="'+o._userPic+'"></div>';
					}
					domLiStr +='<div class="detail"><div class="s">'+data[key].anchorNickName+'</div>';
					if(parseInt(data[key].liveStatus)==o._LIVE2){

						var statusStr = o._timeFormat(data[key].scanTime);

						console.log(statusStr);
						//statusStr = '直播<div style="color:#ff7800;display:inline-block;">'+statusStr+'</div>';
					}else{
					statusStr = '暂未直播';
					}
					var viewer = (parseInt(data[key].viewerCount))?data[key].viewerCount:0;
					domLiStr +='<div class="x">'+statusStr+'</div></div><div class="fansCount">'+viewer+'</div><div class="icon_set icon_fansCount"></div></a></li>';
					if(key<data.length-1)
					domLiStr +='<div class="line"></div>';
					domUlStr += domLiStr;
					}			
				domUlStr += '<a href="'+o._conf['domain']+'personal/follow"><li class="more">更多</li></a>';
				}else{
					domUlStr = '<li class="more no_login">'
						     + '<div class="img_no_login">'
						     + '<img src="'+o._conf['domain']+'static/img/logo/home_no_login.png">'
						     + '</div><div class="txt_div">您还没有任何关注哦</div></li>';
			}
				loadAnimate.selector('follow').closeLoad();
				domUl.html(domUlStr);	
			},
			error:function(error){console.log('ajax request error for historyList on line 974')}
		});
	},
	_getUserPic:function(){
		var o = this;
		var u = this._user;
		var htmlstr = '';
		if(!u.loginStatus){console.log('----'+123);
			htmlstr += '<a class="icon_set icon icon5 rightspan" href="javascript: void(0)"><span class="login_reg  r" >注册</span>';
		    htmlstr += '<span class="line"></span><span class="login_reg  l" >登录</span></a>';
		}
		else{
		var $conf = this._conf;//alert(u.integral+'--'+u.levelIntegral);
		var finished = (parseInt(u.integral) / parseInt(u.levelIntegral) * 100);
        var c = u.levelIntegral - u.integral;
        //if(c==0) c=20;
		u.pic = u.pic?u.pic:o._defaultPic; 
		u.pic = encodeURI(u.pic);
		htmlstr += '<span class="userpic h_sup">';
		htmlstr += '<a href="'+$conf['domain']+'personal/'+'">';
		htmlstr += '<img src="'+u.pic+'"/></a>';
		htmlstr += '<div class="personal h_pop">'
        htmlstr += '</div></span>';
		}
        $('.navi .right').append(htmlstr);
        $('.userpic.h_sup').mouseenter(function(){
        		o._getUserInfo();
        })
	},
	_getUserInfo:function(){
		//var u = this._user;
		var o = this;
		var htmlstr = '';
		$.ajax({
			url:$conf.api+'user/info/getUserDetail.php',
			type:'post',
			data:{uid:o._user._uid,encpass:o._user._enc},
			dataType:'json',
			success:function(data){console.log(data)
				//data = API_MAP.getUserDetail(data);console.log(data)
				data = data.content;
				var u = data;
				//var $conf = this._conf;//alert(u.integral+'--'+u.levelIntegral);
				var finished = (parseInt(u.integral) / parseInt(u.levelIntegral) * 100);
		        var c = u.levelIntegral - u.integral;
		        //if(c==0) c=20;
				u.head = u.head?u.head:o._defaultPic; 
				u.head = encodeURI(u.head);
				
		        htmlstr += '<div id="personal_info">';
		        htmlstr += '<div class="p_detail">';
		        htmlstr += '<div class="p_face">';
		        htmlstr += '<img src="'+u.head+'"/>';
		        htmlstr += '</div>';
		        htmlstr += '<div class="p_info">';
		        htmlstr +='<p>'+ u.nick+'</p>';
		        htmlstr +='<div class="clear"></div>';
		        htmlstr +='<span class="anchor_icon hpcoin"></span>';
		        htmlstr +='<span class="count">'+ numberFormat(parseFloat(u.hpcoin),1)+'</span>';
		        htmlstr +='<span class="anchor_icon hpbean"></span>';
		        htmlstr +='<span class="count">'+ numberFormat(parseFloat(u.hpbean),1)+'</span>';
		        htmlstr +='</div>';
		        htmlstr +='<a target="_blank" href="'+$conf.domain +'personal/recharge.php'+'" id="recharge">充值</a>';
		        htmlstr +='</div>';
		        htmlstr +='<div class="p_level">';
		        htmlstr +='<span class="level"></span>';
		        htmlstr +='<span class="lupIcon"><div class="arrow_up"></div><div class="line red"></div><div class="line red"></div><div class="line white"></div><div class="line red"></div><div class="line white"></div><div class="line red"></div></span>';
		        htmlstr +='<span class="levelBarSpan"><strong id="levelBar" style="width:'+finished+'%"></strong></span>';
		        htmlstr +='<div class="clear"></div>';
		        htmlstr +='<span class="levelup">距离升级还有<a>'+ numberFormat(parseFloat(c)) +'</a>经验值</span>';
		        htmlstr +='</div>';
		        htmlstr +='<div class="p_msg"><a href="'+$conf.domain+'personal/pm/">我的新消息：'+ u.unreadMsg+'条</a></div>'
		        htmlstr +='<div class="p_option">';
		        htmlstr +='<a href="'+$conf.domain + 'personal/' +'" >个人中心</a>';
		        htmlstr +='<div class="lineheight"></div>';
		        htmlstr += '<a>退出</a>';
		        htmlstr +='</div>';
		        htmlstr +='</div>';
		        $('.personal.h_pop').html(htmlstr);
		    		o._logout();
				}
		});
		},
	_getRecGame:function(size,ref){
	    ref = (typeof ref == 'number')?ref:2;//默认2为web请求
	    $.ajax({
	        url:$conf.api+'game/gameList.php',
	        data:{client:ref,size:size},
	        type:'post',
	        dataType:'json',
	        success:function(data){
				data = API_MAP.gameList(data);console.log(data);
	            var gameList = data['gameList'];
	            var htmlStr = '';
	            var domObj = $('.gametypePop .pop');
	            for(var key=0; key < gameList.length;key++){
	                htmlStr += '<span class="game">'
	                	        +  '<a href="'+$conf.domain+'GameZone.php?gid='+gameList[key].gameid+'"> '+gameList[key].gamename+' </a></span>';
		            }
	            domObj.html(htmlStr);
		        }
		    });
		},
	_timeFormat:function(time){
		var t = calTime(time);
        var str = [
            '年',
            '个月',
            '天',
            '小时',
            '分钟',
            '秒'
        ];


        for(var i = 0; i < t.length; i++){
            if(t[i])
                return '已播<em style="margin:0px 5px;color:#ff7800;display:inline-block;font-style: normal;">'+t[i]+'</em>'+str[i];
        }
	}
};

(function(){
	if(typeof String.prototype.trim == 'undefined')
		String.prototype.trim = function(){console.log('mytrim()')
			return this.replace(/(^\s*)|(\s*$)/g,'');
	}
}())