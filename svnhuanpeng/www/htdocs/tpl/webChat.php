<!-- 样式加载 -->
<style type='text/css'>
	object#webChat{
		position:absolute;
		z-index:999999999;
		top:-1000px;
        display: none;
	}
	.notice-dialog{
		width:180px;
		height:52px;
		background:#333;
		z-index:99999999999;
		filter:alpha(opacity=90);
		-moz-opacity:0.9;
		opacity: 0.9;
		color:#fff;
		right: -180px;
		position: relative;
	}
	.notice-dialog a{
		color:#fff;
	}
	.notice-dialog a:hover{
		color:#fff;
	}
	.notice-dialog a:visited{
		color:#fff;
	}
	img{
		width:100%;
		height:100%;
	}
	.notice-dialog .pic{
		margin:6px;
		width:40px;
		height:40px;
		float:left;
	}
	.notice-dialog .detail{
		float:left;
		position:relative;
	}
	.notice-dialog .detail .nick{
		margin: 8px 0px 0px 0px;
	}
	.notice-dialog .detail a{

	}
	.notice-dialog .x{
		width: 10px;
		height: 10px;
		position: absolute;
		right: 2px;
		top: 4px;
		background-position: -201px -22px;
		cursor:pointer;
	}
	.hidden{
		position:absolute;
		right: 0;
		top:50px;
		overflow: hidden;
	}
</style>

<div style="display: none;">
    <div id="webChat" style="display: none;">
    </div>
</div>
<div class="hidden">
	<div class="hidden_dialog"></div>
</div>
<script>
	!function(){
		var id = 'webChat';
		var file = $conf.domain + 'static/chatProxy.swf';
		var flashVersion = '9.0.0';
		var install = 'expressInstall.swf';
		swfobject.embedSWF(file, id, '0', '0',flashVersion, install);
		//游客
		var guid = 3000000000;
		var genc = 'gustuserenterencpass';

		var uid = getCookie('_uid') || guid;
		var enc = getCookie('_enc') || genc;

		requestSocketServer();

		function requestSocketServer(){
			var requestUrl = $conf.api + 'room/getSocketServer.php';
			var requestData = {};
			ajaxRequest({url:requestUrl,data:requestData},function (responseData) {
				if(responseData.serverList.length){
					var server = responseData.serverList[0].split(':');
					runSwfFunction(id, 'login', server[0], server[1], uid, enc, $conf.pushRoomID, 'wbChatCallBack');
				}
			});
		}
		window.wbChatCallBack = function(a, b){
			if(a == 'receivemessage'){
				var m = eval('(' + b + ')');

				switch (parseInt(m.t)){
					case 2001:
						noticeDialog(m);
						break;
					case 2002:
						timeOutConnect(m);
						break;
				}
			}
		}
		var msgStack = [];
		var createDialog = function(node,msg){//alert(conf['_domain']);
			var htmlStr = '<div class="notice-dialog"><div class="personal_icon x"></div>'
				+'<a href="'+$conf.domain+'room.php?luid='+msg.luid+'">'
				+'<div class="pic">'
				+ '<img src="'+msg.pic+'">'
				+ '</div><div class="detail"><div class="nick">'+msg.nick
				+ '</div>'
				+ '正在直播快围观</div></a></div>';

			return node.before(htmlStr);
		};
		var showDialog = function(msg){
			var firstDialog = $('.notice-dialog:first');
			if(!firstDialog.html())
				createDialog($('.hidden_dialog'),msg);
			else
				createDialog(firstDialog,msg);

                $('.x').click(function(e){
                    e.stopPropagation();
                    $(this).parent().parent().remove();
                });
                aFn();return false;
		};
		var aFn = function(){
			var first = $('.notice-dialog:first');
			first.css('display','block');
			first.animate({right:'0px',opacity:0.9},500,function(){
				var o = $(this);
				setTimeout(function(){
					o.animate({right:'-180px',opacity:0.5},500,function(){
						$(this).remove();
					})
				},10000);
			});
		};
		var noticeDialog  = function(msg){
			showDialog(msg);
		};
		var timeOutConnect = function(msg){
			if(typeof(msg)!='object')
				return;
			var currentLuid = parseInt(getCookie('currentLuid'));
			if(currentLuid == msg.luid)
			//refreshCommendLive(currentLive);
				initPlayer('rtmpplayer',currentLuid,'room.php?luid='+currentLuid);
		};
		var refreshCommendLive = function(liveData){

		};


	}();


</script>