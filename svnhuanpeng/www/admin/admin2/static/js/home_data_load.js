/**
 *加载首页数据
 * 
 */
var hd = function(type){
	
	this._loadtype = typeof(type)=='undefined'?true:type;
	this._LIVE;//直播状态
	this._LIVE2;//直播状态
	this._DOMAIN;//host
	this._ROOT;//主目录配置
	this._tmpPic;
	this._defaultPic;
	this._loginStatus = 0;//0游客｜1用户 
	this._uid = '';
	this._enc = '';
	this._othergameid = 401;
	this._xhrlive = [];
	this._init();

}

hd.prototype={
		/**
		 * 初始化
		 */
		_init:function(){
			this._LIVE = 100;
			this._LIVE2 = 1;
			this._DOMAIN = '';
			this._ROOT = $conf.domain
			this._tmpPic = this._ROOT+'static/img/src/default_live.png';
			this._userPic = this._ROOT+'static/img/userface.png';
			this._uid = this._getCookie('_uid');
			this._enc = this._getCookie('_enc');
			this._checkLogin();
			this._start();
			this._xhr={};
			this._defaultPic = this._ROOT+'static/img/src/default/';
		},
		/**
		 * 验证是否登录
		 */
		_checkLogin:function(){
			if( this._uid&&this._enc ){
				this._loginStatus = 1;//cookie里有uid和校验认为他是用户，具体的数据请求在验证它是否合法。
			}
		},
		/**
		 *  执行
		 */
		_start:function(){
			if(this._loginStatus){
				//this._getHistoryList(3);
				//this._getFollowList(3);
				//this._userStatus();
			}
			//this._getLiveList(8, 10,0);
			//this._userAjaxForData();
			//this._getRank(1,1,6);
				
			//this._eventReg();
			var gameList = [190,150,215,62,this._othergameid];
			gameList = gameList.join(',');
			this._gameList(gameList);
			this._newsList();
			$(".login_reg.l,.to_log").click(function(){loginFast.login(0);});
		   	$(".login_reg.r,.to_reg").click(function(){loginFast.login(1);});
		},
		/**
		 * 获取浏览历史JSON数据
		 */
		_getHistoryList:function(size){//console.log(this)
			var o = this;
			if(!o._loginStatus){
				return;
			}
			size = (size==''||size==null||typeof(size)=='undefined')?3:size;
			var data = {
					uid:o._uid,
					encpass:o._enc,
					size:size
			};
			var url = o._ROOT+'a/historyList.php';
			if(o._xhrhistory!=null)
				o._xhrhistory.abort();
			o._xhrhistory=o._ajax(url,data,o._loadHistory,o._error);
			
		},
		/**
		 * 加载获取到的历史数据
		 */
		_loadHistory:function(data){console.log(data);
			var o = arguments[1];
			data = data['historyList'];
			if(typeof(data)!='object'||data==null){
				return;
			}
			var domUl = $('.history ul');
			var domUlStr = '';
			var domLiStr = '';
			if(data.length>0){
			for(var key=0; key < data.length;key++){
				if(parseInt(data[key].liveStatus)==o._LIVE){
					//domLiStr = '<li class="cur"><a href="'+o._ROOT+'room.php?luid='+data[key].anchorUserID+'">';
					domLiStr = '<li class="cur"><a href="'+o._ROOT+data[key].roomID+'">';
				}else{
					//domLiStr = '<li><a href="'+o._ROOT+'room.php?luid='+data[key].anchorUserID+'">';
					domLiStr = '<li><a href="'+o._ROOT+data[key].roomID+'">';
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
					       +'<div class="x">'+o._historyTimeFormAt(data[key].scanTime)+'</div></div></a></li>';	
				if(key< data.length-1)
					domLiStr +='<div class="line"></div>';
				domUlStr += domLiStr;
			}
			domUlStr += '<a href="'+o._ROOT+'personal/follow"><li class="more">更多</li></a>';
			}else{
				domUlStr = '<li class="more no_login">'
					     + '<div class="img_no_login">'
				         + '<img src="'+$conf.domain+'static/img/logo/home_no_fh.png">'
				         + '</div><div class="txt_div">您还没有浏览历史哦</div></li>';
			}
			//console.log(domUlStr);
			domUl.html(domUlStr);
		},
		/**
		 * 格式化浏览时间
		 */
		_historyTimeFormAt:function(unixTime){
			if(typeof(unixTime)=='undefined'||unixTime==''||unixTime==null||!parseInt(unixTime)){
				return '';
			}
			unixTime = parseInt(unixTime);
			return calVisitTime(unixTime);//调用公共函数格式化时间戳 
		},
		/**
		 * 获取关注列表数据
		 */
		_getFollowList:function(size){
			if(!this._loginStatus){
				return;
			}
			size = (size==''||size==null||typeof(size)=='undefined')?3:size;
			var data = {
					uid:this._uid,
					encpass:this._enc,
					size:size
			};
			var url = this._ROOT+'a/followList.php';
			if(this._xhrfollow!=null)
				this._xhrfollow.abort();
			this._xhrfollow=this._ajax(url,data,this._loadFollow,this._error);
		},
		/**
		 * 加载关注数据
		 */
		_loadFollow:function(data){
			var o = arguments[1];
			var liveCount = data['liveCount'];
			data = data['followList'];
			if(typeof(data)!='object'||data==null){
				return;
			}
			var domUl = $('.lovelist ul');
			var domUlStr = '<div class="livecount">当前关注的有<n>'+liveCount+'</n>个正在直播</div>';
			var domLiStr = '';console.log(data)
			if(data.length>0){
			for(var key=0; key < data.length;key++){
				//domLiStr = '<li ><a href="'+o._ROOT+'room.php?luid='+data[key].anchorUserID+'">';
				domLiStr = '<li ><a href="'+o._ROOTdata[key].roomID+'">';

				if(data[key].anchorPicURL){
					domLiStr +='<div class="pic"><img src="'+data[key].anchorPicURL+'"></div>';
				}else{
					domLiStr +='<div class="pic"><img src="'+o._userPic+'"></div>';
				}
				domLiStr +='<div class="detail"><div class="s">'+data[key].anchorNickName+'</div>';
				if(parseInt(data[key].liveStatus)==o._LIVE2){
					var statusStr = Math.ceil((new Date().getTime()/1000-parseInt(data[key].liveStartTime))/60);
					console.log(statusStr);
					statusStr = '已直播'+statusStr+'分钟';
				}else{
					statusStr = '暂无直播';
				}
				var viewer = (parseInt(data[key].viewerCount))?data[key].viewerCount:0; //alert(viewer);
				domLiStr +='<div class="x">'+statusStr+'</div></div><div class="fansCount">'+viewer+'</div><div class="icon_fansCount"></div></a></li>';
				if(key<data.length-1)
					domLiStr +='<div class="line"></div>';
				domUlStr += domLiStr;
			}			
			domUlStr += '<a href="'+o._ROOT+'personal/follow"><li class="more">更多</li></a>';
			}else{
				domUlStr = '<li class="more no_login">'
				     + '<div class="img_no_login">'
			         + '<img src="'+$conf.domain+'static/img/logo/home_no_fh.png">'
			         + '</div><div class="txt_div">您还没有任何关注哦</div></li>';
			}
			domUl.html(domUlStr);			
		},
		/**
		 * 关注列表数据处理
		 */
		_followDataForm:function(data){
			console.log(data);
			for(var key in data){
				
			}
		},
		/**
		 * 获取直播列表数据
		 */ 
		_getLiveList:function(gameId,size,destObj,lastId,type,xhrkey){
			var gameId = (gameId==null||typeof(gameId)=='undefined')?'':gameId;
			var size = (size==null||typeof(size)=='undefined')?'':size;
			var lastId = (lastId==null||lastId==''||typeof(lastId)=='undefined')?0:lastId;
			var destObj = (destObj==null||typeof(destObj)=='undefined')?'':destObj;
			var type = (type==null||type==''||typeof(type)=='undefined')?0:type; //console.log(type+'----')
			var xhrkey = (xhrkey==null||xhrkey==''||typeof(xhrkey)=='undefined')?0:xhrkey;
			
			if(this._loginStatus){  
				var data = {
						uid:this._uid,
						encpass:this._enc,
						size:size,
						gameID:gameId,
						lastId:lastId,
						type:type
				};
			}else{
				var data = {
						size:size,
						gameID:gameId,
						lastId:lastId,
						type:type
				};
			}
			var url = this._ROOT+'api/other/homePageGameList.php';
			if(this._xhrlive[xhrkey]!=null)
				this._xhrlive[xhrkey].abort();
			this._xhrlive[xhrkey]=this._ajax(url,data,this._loadLiveList,this._error,destObj);
		},
		_getOtherLiveList:function(othergameid,destObj,page,size,type){
			page = (page==''||page==null||typeof(page)=='undefined')?1:page;
			size = (size==null||typeof(size)=='undefined')?4:size;
			type = (type==null||type==''||typeof(type)=='undefined')?0:type;
			destObj = (destObj==null||typeof(destObj)=='undefined')?'':destObj;
			othergameidr = (othergameid==''||othergameid==null||typeof(othergameid)=='undefined')?'':othergameid;
			var data = {
				uid:this._uid,
				encpass:this._enc,
				gameID:othergameid,
				page:page,
				size:size,
				type:type
			};
			var url = this._ROOT+'api/other/homePageGameList.php';
			if(this._xhrother!=null)
				this._xhrother.abort();
			this._xhrother=this._ajax(url,data,this._loadLiveList,this._error,destObj);

		},
		/**
		 * 加载直播列表数据
		 */
		_loadLiveList:function(data){
			//
			data = API_MAP.liveList(data);//console.log(data);
			var liveCount = data['liveCount'];
			var ref=data['ref'];
			var data = data['liveList'];
			var o = arguments[1];//console.log(data);
			var blocki= arguments[2];
			if(typeof(data)!='object'||data==null){
				return;
			}
			//var count = parseInt(leftCount)+parseInt(data.length);
			//console.log(data.length)
			//$('#block5 .block:eq('+blocki+') .block_title .num').text(count);
			var domUl =$('.block_live:eq('+blocki+') ul') ;
			//var domTitle =$('.block_title:eq('+blocki+')') ;
			var domUlStr = '';
			var domLiStr = '';
			//var pat = /default*+/;
			//alert(location.href.indexOf('index'));
			for(var key=0; key < data.length;key++){
				data[key].posterUrl = data[key].posterUrl?data[key].posterUrl:(o._defaultPic+'260x150.png');
				//var cur = parseInt(data[key].isFollow)?(' '+'cur'):'';
				var angleStr = (parseInt(data[key].angle)==0 && parseInt(data[key].ispic))?$conf.angleImage:'';
				domLiStr = //'<li class="h_item"><a href="'+o._ROOT+'room.php?luid='+data[key].luid+'"><div class="img_block"><i></i><b></b>'
						   '<li class="h_item"><a href="'+o._ROOT+data[key].roomID+'"><div class="img_block"><i></i><b></b>'	
				           +'<img class="'+angleStr+'" src="'+data[key].posterUrl+'">'
					       +'</div><div class="liveinfo">'				
						   +'<p>'+data[key].liveTitle+'</p>'
						   +'<div class="icon1"></div>'
						   +'<span class="fl nick">'+data[key].nick+'</span>'
						   +'<div class="icon2"></div>'
						   +'<span class="fl">'+data[key].viewerCount+'</span>'
						   +'<span class="fr last">'+data[key].gameName+'</span></div></div></a></li>';
				domUlStr += domLiStr;
			}//console.log(domUlStr);
			if(o._loadtype)
				//domUl.html('');
			    domUl.html(domUlStr);
			else
				domUl.html(domUl.html()+domUlStr);
			hoveritem();
			angleImage(domUl);//设置高度
			//domUl.append(data);
			//var pat = /default\d\.php/;//当前为测试
			//if(!pat.test(window.location.href))//default.php脚本为测试脚本
			$('.block_title:eq('+blocki+') #game').text(ref);
			$('.block_title:eq('+blocki+') .num').text(liveCount);
			if($(window).width()<1180){
				var gameBlockCount = $('#block5 .block_live').length;
				if(gameBlockCount>3){//区分首页
				for(var i=0;i<gameBlockCount;i++){
					$('#block5 .block_live:eq('+i+') ul li:gt(5)').css('display','none');
					//console.log(i)
				}
				}
			}			
		},
		/**
		 * 获取猜你喜欢数据
		 */
		_guessYouLike:function(size){
			var size = (size==null||size==''||typeof(size)=='undefined')?6:size;
			var data = {
					uid:this._uid,
					encpass:this._enc,
					size:size
					};
			var url = this._ROOT+'api/other/guessYouLike.php';
			if(this._xhryoulike!=null)
				this._xhryoulike.abort();
			this._xhryoulike=this._ajax(url,data,this._loadYouLike,this._error);
		},
		/**
		 * 加载猜你喜欢
		 */
		_loadYouLike:function(data){
			/*var guessLoveList = data[0];
			var guessLove = $("#block1 ul li");
		      // console.log(guessLoveList);*/
			//console.log(data);
			data = API_MAP.guessList(data);
			data = data['guessList'];
			var o = arguments[1];
			//var data = data[0];
			var domUlStr = '';
			var domLiStr = '';
			var ulObj = $('#block1 ul');
			ulObj.html('');
			for(var key=0; key < data.length;key++){
				data[key].posterUrl = data[key].posterUrl?data[key].posterUrl:(o._defaultPic+'330x170.png');
				//var cur = parseInt(data[key].isFollow)?(' '+'cur'):'';
				var angleStr = (parseInt(data[key].angle)==0 && parseInt(data[key].ispic))?$conf.angleImage:'';
				domLiStr = //'<li class="h_item"><a href="'+o._ROOT+'room.php?luid='+data[key].luid+'"><div class="img_block"><i></i><b></b>'
						   '<li class="h_item"><a href="'+o._ROOT+data[key].roomID+'"><div class="img_block"><i></i><b></b>'
				           +'<img class="'+angleStr+'" src="'+data[key].posterUrl+'">'
					       +'</div><div class="liveinfo">'				
						   +'<p>'+data[key].liveTitle+'</p>' 
						   +'<div class="icon1"></div>'
						   +'<span class="fl nick">'+data[key].nick+'</span>'
						   +'<div class="icon2"></div>'
						   +'<span class="fl">'+data[key].viewerCount+'</span>'
						   +'<span class="fr last">'+data[key].gameName+'</span></div></a></li>';
				ulObj.append(domLiStr);
			}//console.log(domUlStr);
			//鼠标划过
			hoveritem();
			angleImage(ulObj);
			//$('#block1 ul').html(domUlStr);
			if($(window).width()<1180)
				$('.w980 .fav .block_live ul li:gt(3)').css('display','none');
		},
		_getRank:function(userType,timeType,size){
			$('.rankdiv .ranklist').html('');
			loadAnimate.selector('rankLoad').showLoad();
			var o = this;
			var url = o._ROOT+'api/other/homeRanking.php';
			var data = {userType:userType,timeType:timeType,size:size};
			o._ajax(url,data,o._loadRank,o._error);
		},
		_loadRank:function(data){
			//console.log(data);
			data = API_MAP.rankList(data);
			var o = arguments[1];
			var rankList = data['rankList'];
			var userType = data['userType'];console.log(data);
			var obj = $('.rankdiv .ranklist');
			//obj.children().remove();
			var liStr = '';
			var ulStr = '';
			var numStr = '';
			for(var key=0; key < rankList.length;key++){
				rankList[key].anchorPicUrl = rankList[key].anchorPicUrl?rankList[key].anchorPicUrl:o._userPic; 
				var num = parseInt(key)+1;
				if(num==1)
					numStr = '<div class="icon_set_new no first"></div>';
				else if(num==2)
					numStr = '<div class="icon_set_new no second"></div>';
				else if(num==3)
					numStr = '<div class="icon_set_new no third"></div>';
				else
					numStr = '<div class="no">'+num+'.</div>';
				var uri = '';
				if($('.rankdiv .ranktype .tblock:eq(0)').hasClass('cur'))
					//uri = $conf.domain+'room.php?luid='+rankList[key].uid;
					uri = $conf.domain+rankList[key].roomID;
				else
					uri = 'javascript:void(0);'
				liStr = '<span><a href="'+uri+'"><div class="p">'+numStr
				      + '<div class="pic"><img src="'+rankList[key].anchorPicUrl+'"></div>'
				      + '<div class="username">'+rankList[key].nick+'</div>'
				      + '</div></a></span><div class="x_line"></div>';
				ulStr += liStr; 
			}
			loadAnimate.selector('rankLoad').closeLoad();
			obj.html(ulStr);
			
			if($(window).width()>1180)
				$(".rankdiv .ranklist span:gt(4),.rankdiv .ranklist .x_line:gt(4)").css("display","none");
			/*for(var key in rankList){
				rankList[key].anchorPicUrl = rankList[key].anchorPicUrl?rankList[key].anchorPicUrl:o._userPic; 
				var num = parseInt(key)+1;
				$('.rankdiv .p:eq('+key+') .no').text(num);
				$('.rankdiv .p:eq('+key+') .pic img').attr('src',rankList[key].anchorPicUrl);
				$('.rankdiv .p:eq('+key+') .username').text(rankList[key].nick);
			}*/
		},
		
		_userAjaxForData:function(){alert(1);
			$(".changeicon").click(function(){alert(1)})
			
		},
		_userStatus:function(){
			var data = {
					uid:this._uid
					};
			var url = this._ROOT+'a/getUserInfo.php';
			this._ajax(url,data,this._changeUserStatus,this._error);
		},
		_changeUserStatus:function(data){//console.log(data); 
			var o = arguments[1];
			if(data!=''&&data!=null&&typeof(data)!='undefined'){
				data = data[0];
				//if()
				$("#personal_info .p_option a.pCenter").attr('href','./personal/personInfo.php');
			       // console.log(userpic);
			    $(".right span.userpic img,#personal_info .p_detail .p_face img").attr('src',data['pic']?data['pic']:o._userPic);
			    $("#personal_info .p_detail .p_info p").text(data['nick']);
			       // $(".right span.userpic img,#personal_info .p_detail .p_face img").attr('src','http://dev-img.huanpeng.com/5/4/549f342652634701194130c17fe49a86.jpeg');
			    $(".login_reg.l,.login_reg.r").css("display","none");
			    $(".right span.userpic").css("display","block");
			    $("#logout").click(function(){logout_submit();});
			}
		},
		_eventReg:function(){//alert(11)
			var o = this;
			$('.rankdiv .ranktype div,.rankdiv .title_r span').click(function(){
				var timeType = parseInt($('.rankdiv .title_r span.cur').index())+1;
				var userType = parseInt($('.rankdiv .ranktype div.cur').index())+1;
				console.log(timeType);
				console.log(userType);
				o._getRank(userType,timeType,6);	
				});
		},
		
		_gameList:function(gameIds){
			var o = this;
			url = o._ROOT+'api/game/gameInfoList.php';
			var data = {type:1,base:0};
			o._ajax(url,data,o._gameListLoad);
		},
		_gameListLoad:function(data){
			//console.log(data);
			data = API_MAP.gameInfoList(data);
			$('#block3 .block_title .num').text(data['allCount']);
			var obj = $('#block3 .block_live ul');
			var gameList = data['gameList'];
			var o = arguments[1];
			var liStr = '';
			//var picUrl = '1';
			obj.html('');
			for(var key=0; key < gameList.length;key++){
				/*switch(parseInt(key)){
					case 0:picUrl='http://static.huomaotv.cn/upload/game/image/20151020/095550_24846.png';break;
					case 1:picUrl='http://kascdn.kascend.com/jellyfish/game/poster/151229/1451370733918.jpg';break;
					case 2:picUrl='http://kascdn.kascend.com/jellyfish/game/poster/150929/1443522245991.jpg';break;
					case 3:picUrl='http://kascdn.kascend.com/jellyfish/game/poster/151029/1446109658757.jpg';break;
					case 4:picUrl='http://kascdn.kascend.com/jellyfish/game/poster/150929/1443522122621.jpg';break;
					default:picUrl=o._tmpPic;break;
				}*/
				//console.log(picUrl)
				liStr = '<li class="h_item"><a href="'+o._ROOT+'GameZone.php?gid='+gameList[key].gameID+'" target="_blank">'
				      + '<img src="'+gameList[key].posterURL+'">'
				      + '<div class="liveinfo"><div class="gt">'+gameList[key].gameName+'</div>'
				      + '<div class="count_txt">'+gameList[key].liveCount+'</div>'
				      + '<div class="icon_set count"></div></div></a></li>';
				obj.append(liStr);
			}
			//设置划过样式
			hoveritem();
			angleImage(obj);
			if($(window).width()<1180)
				$("#block3 .block_live ul li:last").css('display','none');
		},
		_newsList:function (size) {
			var o = this;
			url = o._ROOT+'api/information/getInformation.php';
			var data = {type:0};
			o._ajax(url,data,o._newsListLoad);
		},
		_newsListLoad:function (data) {
			//console.log(data);
			//if(data.length==0||data.status)
				//console.log();
			var o = arguments[1];
			var newList = data['content'].plist;
			var actList = data['content'].tlist;
			var newListStr = '';
			for(var i=0; i < newList.length;i++){
				var poster = newList[i].poster?newList[i].poster:o._ROOT+'/static/img/news-place.png';
				//newListStr += '<a href="'+o._ROOT+'news.php?id='+newList[i].id+'"><img alt="1" src="'+poster+'" style="display: none;"></a>'
				$('#news-list img').eq(i).attr('src',poster);
				var link = newList[i].url?newList[i].url:o._ROOT+'news.php?id='+newList[i].id;
				$('#news-list a').eq(i).attr('href',link);
			}
			//$('#news-list').html(newListStr);
			var actListStr = '<ul>';
			for(var i=0; i < actList.length;i++){

				if(actList[i].id == ''){
                    actListStr += '<li><span class="first">'+actList[i].type+'</span> <span class="centerline"></span> <span class="news-title"><a href="'+o._ROOT+'news.php?id='+actList[i].id+'">'+actList[i].title+'</a></span></li>';
				}else{
                    actListStr += '<li><span class="first">'+actList[i].type+'</span> <span class="centerline"></span> <span class="news-title"><a href="'+o._ROOT+'news/'+actList[i].url+'">'+actList[i].title+'</a></span></li>';
				}
			}
			actListStr +='</ul>';
			/*if(actList.length<5)
				actListStr +='</ul>';
			else
				actListStr +='<li class="last"><a href="./news_activity.php">查看更多资讯</a></li></ul>';*/
			$('#act-list').html(actListStr);
		},

		/**
		 * 获取cookie
		 */
		_getCookie:function(){
			var a  = document.cookie.match(new RegExp("(?:^|;)\\s*" + arguments[0] + "=([^;]*)"));
		    return (a) ? decodeURIComponent(a[1]) : null;
		},
		/**
		 * 设置cookie值
		 * 设置值为单位＋数值 例如：d12表示12天后过期
		 * d(天)｜h（小时）｜m（分钟）｜s（秒）
		 */
		_setCookie:function(){
			if(!arguments[0]||!arguments[1])
				return;
			var cookieStr = arguments[0] + "=" + encodeURIComponent(arguments[1]) + ";";
			if(arguments[2]){alert(1)
				var pat = /([dhms])(\d+)/i;
				var mat = pat.exec(arguments[2]);//console.log(mat)
				var t = parseInt(mat[2]);
				var time = 0;
				switch(mat[1].toLowerCase()){
				case 'd':time=t*24*60*60*1000;break;
				case 'h':time=t*60*60*1000;break;
				case 'm':time=t*60*1000;break;
				case 's':time=t*1000;break;
				default:break;
				}//alert(2)
				var exp = new Date();
				exp.setTime(exp.getTime()+time);
				cookieStr +="expires="+exp.toGMTString()+";";
				//alert(3)
			}	
			//alert(cookieStr)
			document.cookie = cookieStr;	
			//console.log(document.cookie);
		},
		/**
		 * ajax简易封装便于书写
		 */
		_ajax:function(url,data,successFun,failedFun,destObj,async,type,dataType){
			//默认参数
			async = (async==null||async==''||typeof(async)=='undefined')?'true':async;
			type = (type==null||type==''||typeof(type)=='undefined')?'post':type;
			dataType = (dataType==null||dataType==''||typeof(dataType)=='undefined')?'json':dataType;
			destObj = (destObj==null||typeof(destObj)=='undefined')?'':destObj;
			failedFun = (failedFun==null||failedFun==''||typeof(failedFun)=='undefined')?this._error:failedFun;
			var o = this;
			
			o._xhr = $.ajax({
				type:type,
				async:async,
				data:data,
				url:url,
				dataType:dataType,
				success:function(d){
					if(d.length==0)
						return true;
					successFun(d,o,destObj);//数据处理
				},
				error:function(e){
					failedFun(e,o,destObj);//错误处理
				}
			});
			return o._xhr;
		},
		_error:function(data){
			console.log('hoome_data_load.js req error on line 453');
		}
}
//var hd = new hd(); 