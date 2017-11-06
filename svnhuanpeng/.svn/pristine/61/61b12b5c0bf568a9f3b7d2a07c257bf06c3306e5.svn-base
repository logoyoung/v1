package hpPlayer
{
	import flash.display.DisplayObject;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.events.ProgressEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.media.SoundTransform;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.system.Security;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.ui.Keyboard;
	import flash.utils.Timer;
	import flash.utils.clearTimeout;
	
	import hpPlayer.DebugInfoManager;
	import hpPlayer.PlayerCore;
	import hpPlayer.PlayerSituation;
	import hpPlayer.Recommend;
	import hpPlayer.ResourceLoadManager;
	import hpPlayer.Slider;
	import hpPlayer.UIButton;
	import hpPlayer.URLPostRequest;
	import hpPlayer.VideoScreenDirection;
	import hpPlayer.giftBanner.GiftBannerManager;
	import hpPlayer.postRequest;
	import hpPlayer.sendHttpRequest;
	import hpPlayer.showPercent;
	
	public class CommonPlayer extends Sprite
	{
		//视频播放所需变量
		public var coreVideo:CoreVideo;							//CoreVideo对象
		public var playerCore:PlayerCore;						//PlaayerCore对象
		protected var eTime:String;							//流地址补充
		protected var streamURL:String;						//流地址
		protected var serverURL:String;						//服务器地址
		protected var oriStreamURL:String;						//流地址
		protected var oriServerURL:String;						//服务器地址
		protected var liveroomURL:String;						//直播间地址
		protected var screenDirection:String;					//横竖屏
		private var authorityToChangeVolume:int;				//主播调整音量的权限，0为允许调整，1为不允许调整
		protected var sound:SoundTransform;					//音量变换，管理PlayerCore.NetStream的音量
		//加载资源所需变量
		protected var resourceLoadManager:ResourceLoadManager;	//资源管理
		protected var listResource:Array;						//资源列表
		protected var mcLoading:MovieClip;						//加载动画
		protected var percentLoaded:showPercent;				//加载进度显示
		//调试信息所需变量
		protected var containerDebufInfo:Sprite;				//调试信息容器	
		//按钮
		protected var containerUI:Sprite;						//按钮容器
		protected var containerUIBackground:Shape;				//按钮容器背景
		protected var btnPlay:UIButton;						//播放按钮
		protected var btnPause:UIButton;						//暂停按钮
		protected var btnRefresh:UIButton;						//刷新按钮
		protected var btnFullScreen:UIButton;					//全屏按钮
		protected var btnHalfScreen:UIButton;					//半屏按钮
		protected var btnMute:UIButton;						//静音按钮
		protected var btnUnmute:UIButton;						//非静音按钮
		protected var btnLiveButtom:UIButton;					//进入直播间按钮(底部)
		protected var btnLiveMain:UIButton;					//进入直播间按钮(中央)
		protected var btnSendBarrage:UIButton;					//全屏发送弹幕
		protected var buttom:MovieClip;						//Slider底部
		protected var selected:MovieClip;						//Slider选中部分
		protected var dragSlider:MovieClip;					//Slider拖拽滑块
		protected var slider:Slider;							//Slider对象
		protected var Timebuttom:MovieClip;					//TimeSlider底部
		protected var TimeSelected:MovieClip;					//TimeSlider选中部分
		protected var TimedragSlider:MovieClip;				//TimeSlider拖拽滑块
		protected var TimeSlider:Slider;						//TimeSlider
		protected var initValue:Number = 1;					//Slider的初始值
		protected var formerValue:Number = 0.5;				//Slider的上一次值，记录
		protected var btnBarrageOff:UIButton;					//关闭弹幕
		protected var btnBarrageOn:UIButton;					//开启弹幕
		protected const INTERVAL:Number = 20;					//按钮间隔
		protected var duration:Number = 0;						//时长
		private var isVideoEnd:Boolean = false;				//视频是否结束
		protected var videoWidth:Number;						//视频宽度
		protected var videoHeight:Number;						//视频高度
		protected const toolBarHieght:Number = 48;				//工具条高度
		protected var isPlay:Boolean = true;					//是否在播放中
		protected var giftMC:MovieClip;						//礼物影片剪辑
		protected var bannerMC:MovieClip;						//横幅影片剪辑
		protected var containerGift:Sprite;					//礼物横幅容器
		protected var RecommendURLRequest:URLRequest;			//推荐URLRequest
		protected var RecommendLoader:Loader;					//推荐Loader
		protected var RecommendURLList:Array;					//推荐URL列表
		protected var RecommendPIicList:Array;					//推荐Pic列表
		protected const picWidth:Number = 300;					//推荐图片宽度
		protected const picHeight:Number = 300;				//推荐图片高度
		protected const picINTERVAL:Number = 100;				//推荐图片间隔
		protected var firstRecommend:Recommend;				//第一组推荐
		protected var secondRecommend:Recommend;				//第二组推荐
		protected var singleRecommend:MovieClip;				//推荐影片剪辑
		protected var userID:Number;							//用户ID
		protected var recommendPHPURL:String;					//推荐信息php
		protected var postrequest:postRequest;					//postRequest对象
		protected var recommendmc:MovieClip;					//推荐影片剪辑
		protected var jumpmc:MovieClip;						//跳转影片剪辑
		protected var tfInfo:TextField;						//信息文本对象
		protected var tfMore:TextField;						//更多文本对象
		protected var moreLiveURL:String;						//更多直播URL
		protected var recommendData:String;					//推荐信息数据
		protected var recommendDebugTime:int = 0;				//是否已推荐
		protected var isLoggedin:int = -1;						//是否已登录,0为已登录，1为未登录
		protected var netStatusCode:String;					//网络状况代码
		protected var btnStartLive:UIButton;					//开始直播按钮
		protected var btnTip:UIButton;							//提示按钮
		protected var containerRecommend:Sprite;				//推荐容器
		protected var shouldShowRecommend:int;					//是否应该显示直播推荐  0:不显示1:显示
		protected var liveEndIntervalID:Number = -1;			//直播结束时间间隔ID
		protected var oldStreamURL:String;						//前一个流地址，检测用
		protected var loadFinishCallbackFuncName:String;		//加载完成回调函数
		protected var urlPHPURL:String = "";					//获取服务器地址、流地址等php
		protected var hostID:Number = -1;						//主播ID
		protected var urlRequest:URLPostRequest;				//获取服务器地址、流地址等的http请求
		protected var isLiving:int;							//当前是否有直播isLiving:0   未直播   相当于LiveEnd 跳 直播推荐
		protected var httpRequest:sendHttpRequest;
		protected var timerGetURL:Timer;						//计时器，发送请求得到新地址，超时使用oldURL
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		//构造函数
		public function CommonPlayer()
		{
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;
			stage.color = 0x1a1a1a;
			stage.addEventListener(Event.RESIZE, resizeHandler);
			stage.addEventListener(KeyboardEvent.KEY_UP, keyUpHandler); 
			stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
			stage.addEventListener(Event.MOUSE_LEAVE, UIHide);
			ExternalInterface.addCallback("angle", setScreenDirection);
			ExternalInterface.addCallback("setVolumeAuthority", setVolumeAuthority);
			ExternalInterface.addCallback("setHostID", setHostID);
			init();
			if(root.loaderInfo.parameters.isLiving == 0)
			{
				this.isLiving = 0;
			}
			if(root.loaderInfo.parameters.isLiving == 1)
			{
				this.isLiving = 1;
			}
			if(this.loaderInfo.bytesLoaded == this.loaderInfo.bytesTotal)
			{
				DebugInfoManager.getInstance().log("flash直接加载完成！");
				flashReady();
			}
			else
			{
				DebugInfoManager.getInstance().log("添加监听函数");
				this.loaderInfo.addEventListener(Event.COMPLETE, flashReady);
			}
		}
		
		protected function setHostID(hostid:Number):void
		{
			DebugInfoManager.getInstance().log("[-->JS-->]" + "[CommonPlayer][setHostID] hostid = " + hostid);
			this.hostID = hostid;
//			stage.addEventListener(Event.RESIZE, resizeHandler);
//			stage.addEventListener(KeyboardEvent.KEY_UP, keyUpHandler); 
//			stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
//			stage.addEventListener(Event.MOUSE_LEAVE, UIHide);
			if(this.hostID != 0 && this.urlPHPURL != null)
			{
				this.urlRequest = new URLPostRequest();
				this.urlRequest.setup(this.urlPHPURL, "POST", this.hostID, geturlData);	
			}
//			if(this.containerUI != null)
//			{
//				this.containerUI.visible = true;
//			}
		}
		
		private function flashReady(event:Event = null):void
		{
			DebugInfoManager.getInstance().log("加载完成！调用flashCallback，传递参数'flashIsReady'");
			ExternalInterface.call("flashCallback", "flashIsReady");
		}
				
		public function loadComplete(event:Event):void
		{
			DebugInfoManager.getInstance().log("FLASH加载完成！");
			ExternalInterface.call("flashCallback", "flashIsReady");
		}
		
		public function showProgress(event:ProgressEvent):void
		{
			var p:Number = event.bytesLoaded / event.bytesTotal;
			var n:Number = Math.round(p * 100);
			DebugInfoManager.getInstance().log("FLASH加载中..." + n.toString() + "%");
		}
		
		//获得元数据
		protected function metaDataHandler(object:Object):void
		{
			DebugInfoManager.getInstance().log("onMetaData" + " width= " + object.width + " height= " + object.height 
				+ " duration= " + object.duration); 
		}
		
		//初始化
		private function init():void
		{
			//调试信息
			this.containerDebufInfo = new Sprite();
			DebugInfoManager.getInstance().setupWithContainer(this.containerDebufInfo, stage.stageWidth, stage.stageHeight);
			this.containerDebufInfo.x = 0;
			this.containerDebufInfo.y = 0;
			this.containerDebufInfo.width = stage.stageWidth;
			this.containerDebufInfo.height = stage.stageHeight;
			DebugInfoManager.getInstance().hide();
			DebugInfoManager.getInstance().log("版本 " + "2017.7.21");
			stage.addChild(this.containerDebufInfo);
			this.containerDebufInfo.mouseEnabled = false;
			//加载资源
			this.resourceLoadManager = new ResourceLoadManager();
			this.listResource = new Array();
			var URLString:String;			//URL地址字符串
			if(root.loaderInfo.parameters.loadingURL)
			{
				URLString = root.loaderInfo.parameters.loadingURL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "loading.swf地址" + URLString);
				this.listResource.push(URLString);
			}
			else
			{
				this.listResource.push("./loading.swf");
				DebugInfoManager.getInstance().log("[FlashVars] " + "loading.swf地址  " + "默认 ./loading.swf");
			}
			if(root.loaderInfo.parameters.UIButtonURL)
			{
				URLString = root.loaderInfo.parameters.UIButtonURL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "UIButton.swf地址" + URLString);
				this.listResource.push(URLString);
			}
			else
			{
				this.listResource.push("./UIButton.swf");
				DebugInfoManager.getInstance().log("[FlashVars] " + "UIButton.swf地址  " + "默认 ./UIButton.swf");
			}
			if(root.loaderInfo.parameters.LiveRecommendURL)
			{
				URLString = root.loaderInfo.parameters.LiveRecommendURL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "LiveRecommend.swf地址  " + URLString);
				this.listResource.push(URLString);
			}
			else
			{
				this.listResource.push("./LiveRecommend.swf");
				DebugInfoManager.getInstance().log("[FlashVars] " + "LiveRecommend.swf地址  " + "默认 ./LiveRecommend.swf");
			}
			if(root.loaderInfo.parameters.percentLoadingURL)
			{
				URLString = root.loaderInfo.parameters.percentLoadingURL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "percentLoading.swf地址  " + URLString);
				this.listResource.push(URLString);
			}
			else
			{
				this.listResource.push("./percentLoading.swf");
				DebugInfoManager.getInstance().log("[FlashVars] " + "percentLoading.swf地址  " + "默认 ./percentLoading.swf");
			}
			if(this.listResource != null)
			{
				this.resourceLoadManager.loadWithArray(this.listResource, onProgress, callSuccess, callFailure, callAll);
				DebugInfoManager.getInstance().log("[FlashVars] " + "  " + "[开始加载资源]");
			}
			if(root.loaderInfo.parameters.hostID)
			{
				this.hostID = root.loaderInfo.parameters.hostID;
				DebugInfoManager.getInstance().log("[FlashVars] " + "hostID = " + this.hostID);
			}
			if(root.loaderInfo.parameters.urlPHP)
			{
				this.urlPHPURL = root.loaderInfo.parameters.urlPHP;
				DebugInfoManager.getInstance().log("[FlashVars] " + "urlPHP = " + root.loaderInfo.parameters.urlPHP);
			}
			if(root.loaderInfo.parameters.readyFun)
			{
				this.loadFinishCallbackFuncName = root.loaderInfo.parameters.readyFun;
			}
			if(root.loaderInfo.parameters.streamURL)
			{
				this.streamURL = root.loaderInfo.parameters.streamURL;
			}
			if(root.loaderInfo.parameters.eTime)
			{
				this.eTime = root.loaderInfo.parameters.eTime;
				this.streamURL = this.streamURL + "&eTime=" + this.eTime;
			}
			if(root.loaderInfo.parameters.serverURL)
			{
				this.serverURL = root.loaderInfo.parameters.serverURL;
			}
			if(root.loaderInfo.parameters.liveroomURL)
			{
				this.liveroomURL = root.loaderInfo.parameters.liveroomURL;
			}
			if(root.loaderInfo.parameters.screenDirection == 1)
			{
				this.screenDirection = VideoScreenDirection.VERTICAL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "横竖屏 = " + this.screenDirection + " VERTICAL");
			}
			else
			{
				this.screenDirection = VideoScreenDirection.HORIZONTAL;
				DebugInfoManager.getInstance().log("[FlashVars] " + "横竖屏 = " + this.screenDirection + " HORIZONTAL");
			}
			if(root.loaderInfo.parameters.UID >= 0 && root.loaderInfo.parameters.UID != 3000000000) //root.loaderInfo.parameters.UID >= 0 && 
			{
				this.userID = root.loaderInfo.parameters.UID;
				this.isLoggedin = 0;
				DebugInfoManager.getInstance().log("[FlashVars] " + "root.loaderInfo.parameters.UID = " + root.loaderInfo.parameters.UID + "已登录");
				DebugInfoManager.getInstance().log("[FlashVars] " + "UID = " + this.userID + "已登录");
			}
			else
			{
				this.userID = 0;
				this.isLoggedin = 1;
				DebugInfoManager.getInstance().log("[FlashVars] " + "UID 为默认值 " + this.userID + "未登录");
			}
			if(root.loaderInfo.parameters.recommendPHP)
			{
				this.recommendPHPURL = root.loaderInfo.parameters.recommendPHP;
				DebugInfoManager.getInstance().log("[FlashVars] " + "this.recommendPHPURL = " + this.recommendPHPURL);
			}
			else
			{
				this.recommendPHPURL = "http://www.huanpeng.com/api/other/flashRecommend.php";
				DebugInfoManager.getInstance().log("[FlashVars] " + "this.recommendPHPURL 设置为默认值 http://www.huanpeng.com/api/other/flashRecommend.php");
			}
			//视频
			this.sound = new SoundTransform();
			this.playerCore = new PlayerCore();
			this.coreVideo = new CoreVideo();
			stage.addChildAt(this.coreVideo, 0);
			this.containerUI = new Sprite();
			this.containerUIBackground = new Shape();
			stage.addChildAt(this.containerUI, stage.getChildIndex(this.containerDebufInfo));
			stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
			if(this.streamURL != null)
			{
				this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, this.videoWidth, this.videoHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
			DebugInfoManager.getInstance().log("[FlashVars] streamURL = " + this.streamURL);
			DebugInfoManager.getInstance().log("[FlashVars] eTime = " + this.eTime);
			DebugInfoManager.getInstance().log("[FlashVars] serverURL = " + this.serverURL);
			DebugInfoManager.getInstance().log("[FlashVars] liveroomURL = " + this.liveroomURL);
			DebugInfoManager.getInstance().log("[FlashVars] screenDirection = " + this.screenDirection);
			//UI绘制
			this.btnPlay = new UIButton();
			this.btnPause = new UIButton();
			this.btnRefresh = new UIButton();
			this.btnBarrageOff = new UIButton();
			this.btnBarrageOn = new UIButton();
			this.btnFullScreen = new UIButton();
			this.btnHalfScreen = new UIButton();
			this.btnLiveButtom = new UIButton();
			this.btnLiveMain = new UIButton();
			this.btnMute = new UIButton();
			this.btnUnmute = new UIButton();
			this.btnSendBarrage = new UIButton();
			this.containerGift = new Sprite();
			var bkg:Shape = new Shape();
			bkg.graphics.beginFill(0xffffff, 0);
			bkg.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			bkg.graphics.endFill();
			this.containerGift.addChild(bkg);
			this.containerGift.visible = true;
			this.containerGift.mouseChildren = true;
			this.containerGift.mouseEnabled = false;
			var index:int = stage.getChildIndex(this.containerUI);
			stage.addChildAt(this.containerGift, index);
			this.RecommendLoader = new Loader();
			this.RecommendURLList = new Array();
			this.RecommendPIicList = new Array();
			this.firstRecommend = new Recommend();
			this.secondRecommend = new Recommend();
			this.postrequest = new postRequest();
			this.btnStartLive = new UIButton();
			this.btnTip = new UIButton();
			this.httpRequest = new sendHttpRequest();
			this.percentLoaded = new showPercent();
			this.percentLoaded.visible = false;
			stage.addChild(this.percentLoaded);
		}
		
		protected function getData(result:String):void
		{
			if(this.timerGetURL != null)
			{
				this.timerGetURL.stop();
			}
			DebugInfoManager.getInstance().log("[CP Flash] getData中获得返回值 data = " + result); 
			if(result == null)
			{
				DebugInfoManager.getInstance().log("[CP Flash] 返回值为空"); 
				return;
			}
//			if(this.percentLoaded != null)
//			{
//				DebugInfoManager.getInstance().log("[CommonPlayer]添加加载百分比");
//				this.percentLoaded.setValue(0);
//				this.percentLoaded.visible = true;
//				this.percentLoaded.show();
//				this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;
//				this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
//				if(this.containerUI != null)
//				{
//					stage.addChildAt(this.percentLoaded, stage.getChildIndex(this.containerUI));
//				}
//				else
//				{
//					stage.addChild(this.percentLoaded);
//				}
//				setPercentLoaded();
//				DebugInfoManager.getInstance().log("[CP Flash check PL] stage.contains(this.percentLoaded) = " + stage.contains(this.percentLoaded)); 
//				DebugInfoManager.getInstance().log("[CP Flash check PL] x = " + this.percentLoaded.x + " y = " + this.percentLoaded.y);
//				DebugInfoManager.getInstance().log("[CP Flash check PL] this.percentLoaded.visible " + this.percentLoaded.visible); 
//				DebugInfoManager.getInstance().log("[CP Flash check PL] stage.contains(this.percentLoaded) = " + stage.contains(this.percentLoaded)); 
//			}
			if(this.percentLoaded != null)
			{
				DebugInfoManager.getInstance().log("[CommonPlayer] geturlData 添加加载百分比");
//				this.percentLoaded.setValue(0);
				this.percentLoaded.visible = true;
				this.percentLoaded.show();
				this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;//(stage.stageWidth - 500) / 2 - 20;//+ 500 / 2 - 396 / 2;
				this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
				stage.addChild(this.percentLoaded);
			}
			
			var obj:Object = JSON.parse(result);
			DebugInfoManager.getInstance().log('obj["data"]["url"] = ' + obj["data"]["url"]);
			var str:String = obj["data"]["url"];
			var strIndex:int = str.indexOf("liverecord")
			this.serverURL = str.slice(0, strIndex + 11);
			this.streamURL = str.slice(strIndex + 11);
			DebugInfoManager.getInstance().log("[CP Flash] " + "this.serverURL = " + this.serverURL);
			DebugInfoManager.getInstance().log("[CP Flash] " + "this.streamURL = " + this.streamURL);
			DebugInfoManager.getInstance().log("[CP Flash] " + "this.screenDirection = " + this.screenDirection);
			if(this.oldStreamURL != null)
			{
				if(this.streamURL.indexOf(this.oldStreamURL) >= 0)// == this.oldStreamURL)
				{
					DebugInfoManager.getInstance().log("与之前stream相同，不进行连接");
					return;
				}
			}
			if(this.serverURL != null && this.streamURL != null)
			{
				var str2:String = this.streamURL;
				var strIndex2:int = this.streamURL.indexOf("?");
				this.oldStreamURL = str2.slice(0, strIndex2);
				DebugInfoManager.getInstance().log("[func geturlData] 设置 this.oldStreamURL = " + this.oldStreamURL);
				this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, stage.stageWidth, stage.stageHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
			else
			{
				this.playerCore.init(this.oriServerURL, this.oriStreamURL, this.coreVideo, callback, getInfoCallback, stage.stageWidth, stage.stageHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
		}
		
		//用于获取URL信息，并输出
		protected function geturlData(result:String):void
		{
			DebugInfoManager.getInstance().log("[CP Flash] 收到url信息" + result); 
			var obj:Object = JSON.parse(result);
			if(obj["content"]["desc"] != "无效的luid" && obj["content"]["streamList"][0] != null)
			{
				this.oriServerURL = "rtmp://" + obj["content"]["streamList"][0] + "/";
			}
			if(obj["content"]["streamList"] == null || obj["content"]["streamList"][0] == null)
			{
				DebugInfoManager.getInstance().log("[CP Flash] 收到无效url信息"); 
				return;
			}
			stage.addEventListener(Event.RESIZE, resizeHandler);
			stage.addEventListener(KeyboardEvent.KEY_UP, keyUpHandler); 
			stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
			stage.addEventListener(Event.MOUSE_LEAVE, UIHide);
			if(this.containerRecommend != null)
			{
				this.containerRecommend.visible = false;
			}
			if(this.percentLoaded != null)
			{
				DebugInfoManager.getInstance().log("geturlData [CommonPlayer]添加加载百分比");
//				this.percentLoaded.setValue(0);
				this.percentLoaded.visible = true;
				this.percentLoaded.show();
				this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;//(stage.stageWidth - 500) / 2 - 20;//+ 500 / 2 - 396 / 2;
				this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
				stage.addChild(this.percentLoaded);
			}
			this.oriStreamURL = obj["content"]["stream"];
			if(obj["content"]["orientation"] == 0)
			{
				this.screenDirection = VideoScreenDirection.VERTICAL;
			}
			if(obj["content"]["orientation"] == 1)
			{
				this.screenDirection = VideoScreenDirection.HORIZONTAL;
			}
			DebugInfoManager.getInstance().log("[CP Flash] " + "this.oriServerURL = " + this.oriServerURL);
			DebugInfoManager.getInstance().log("[CP Flash] " + "this.oriStreamURL = " + this.oriStreamURL);
			this.httpRequest.init(this.oriServerURL.slice(7) //去掉rtmp://
				+ this.oriStreamURL,
				this.getData);
			DebugInfoManager.getInstance().log("--发送请求到WS--"); 
			this.timerGetURL = new Timer(2000);
			this.timerGetURL.addEventListener(TimerEvent.TIMER_COMPLETE, getURLTimeout);
			this.timerGetURL.start();
		}
		
		protected function getURLTimeout(event:TimerEvent):void
		{
			this.timerGetURL.stop();
			DebugInfoManager.getInstance().log("[CP Flash] getURLTimeout 超时 使用oldURL连接"); 
			if(this.serverURL != null && this.streamURL != null)
			{
				var str2:String = this.streamURL;
				var strIndex2:int = this.streamURL.indexOf("?");
				this.oldStreamURL = str2.slice(0, strIndex2);
				DebugInfoManager.getInstance().log("[func geturlData] 设置 this.oldStreamURL = " + this.oldStreamURL);
				this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, stage.stageWidth, stage.stageHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
			else
			{
				this.playerCore.init(this.oriServerURL, this.oriStreamURL, this.coreVideo, callback, getInfoCallback, stage.stageWidth, stage.stageHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
		}
		
		//用于获取推荐信息，并输出
		protected function getRecommendData(result:String):void
		{
			var patternFirst:String = "firstPicURL"; 
			var patternSecond:String = "secondPicURL"; 
			var str:String = result; 
			if(result == null || result == "" || str.indexOf(patternFirst) < 0 || str.indexOf(patternSecond) < 0)
			{
				DebugInfoManager.getInstance().log("[CP Flash] 收到无效返回值：缺少必要信息" + result); 
				return;
			}
			DebugInfoManager.getInstance().log("[CP Flash] 收到返回值，开始解析推荐信息JSON " + result); 
			var obj:Object = JSON.parse(result);
			this.recommendData = result;
			if(this.recommendDebugTime <= 0)
			{
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstPicURL = " + obj["content"]["list"][0]["firstPicURL"]);
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstLiveRoomURL = " + obj["content"]["list"][0]["firstLiveRoomURL"]);
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstHostName = " + obj["content"]["list"][0]["firstHostName"]);
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstAudienceNumber = " + obj["content"]["list"][0]["firstAudienceNumber"]);
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstGameName = " + obj["content"]["list"][0]["firstGameName"]);
				DebugInfoManager.getInstance().log("解析JSON 1  content list firstScreenDirection = " + obj["content"]["list"][0]["firstScreenDirection"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondPicURL = " + obj["content"]["list"][1]["secondPicURL"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondLiveRoomURL = " + obj["content"]["list"][1]["secondLiveRoomURL"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondHostName = " + obj["content"]["list"][1]["secondHostName"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondAudienceNumber = " + obj["content"]["list"][1]["secondAudienceNumber"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondGameName = " + obj["content"]["list"][1]["secondGameName"]);
				DebugInfoManager.getInstance().log("解析JSON 2  content list secondScreenDirection = " + obj["content"]["list"][1]["secondScreenDirection"]);
				this.recommendDebugTime = 1;
			}
		}
		
		//用户界面绘制
		protected function draw():void
		{
			if(this.btnPlay != null)
			{
				setUI();
				addVideo();
				addFunctions();
			}
		}
		
		//UI基本设置
		protected function setUI():void
		{
			this.containerUIBackground.graphics.clear();
			this.containerUIBackground.graphics.beginFill(0x242424, 1);
			this.containerUIBackground.graphics.drawRect(0, stage.stageHeight - this.btnPlay.height, stage.stageWidth, this.btnPlay.height);
			this.containerUIBackground.graphics.endFill();
			this.containerUI.addChildAt(this.containerUIBackground, 0);
			this.btnPlay.x = 0;
			this.btnPlay.y = stage.stageHeight - this.btnPlay.height;
			this.btnPause.x = 0;
			this.btnPause.y = stage.stageHeight - this.btnPlay.height;
			if(this.isPlay == true)
			{
				this.btnPlay.visible = false;
				this.btnPause.visible = true;
			}
			else
			{
				this.btnPlay.visible = true;
				this.btnPause.visible = false;
			}
			this.btnRefresh.x = this.btnPlay.height;
			this.btnRefresh.y = stage.stageHeight - this.btnPlay.height;
			this.btnRefresh.visible = true;
			this.btnFullScreen.x = stage.stageWidth - this.btnFullScreen.width;
			this.btnFullScreen.y = stage.stageHeight - this.btnPlay.height;
			this.btnFullScreen.visible = true;
			this.btnHalfScreen.x = stage.stageWidth - this.btnHalfScreen.width;
			this.btnHalfScreen.y = stage.stageHeight - this.btnPlay.height;
			this.btnHalfScreen.visible = false;
			if(this.slider != null && this.buttom != null)
			{
				this.slider.x = this.btnFullScreen.x - this.buttom.width - INTERVAL;
				this.slider.y = stage.stageHeight - this.btnFullScreen.height / 2 - this.slider.height / 2;
				this.slider.visible = true;
			}
			this.btnMute.x = this.slider.x - this.btnMute.width - INTERVAL;
			this.btnMute.y = stage.stageHeight - this.btnPlay.height;
			this.btnUnmute.x = this.slider.x - this.btnUnmute.width - INTERVAL;
			this.btnUnmute.y = stage.stageHeight - this.btnPlay.height;
			if(this.btnMute.visible == false && this.btnUnmute.visible == false)
			{
				this.btnMute.visible = false;
				this.btnUnmute.visible = true;
			}
			this.containerDebufInfo.width = stage.stageWidth;
			this.containerDebufInfo.height = stage.stageHeight;
			this.btnBarrageOff.visible = false;
			this.btnBarrageOn.visible = false;
			this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;//(stage.stageWidth - 500) / 2 - 20;//+ 500 / 2 - 396 / 2;
			this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
		}
		
		//用户界面重新绘制
		protected function redraw():void
		{
			if(this.btnPlay != null)
			{
				setUI();
				addVideo();
			}
		}
		
		//按钮上添加事件侦听
		protected function addFunctions():void
		{
			this.btnPlay.addEventListener(MouseEvent.CLICK, playFunction);	
			this.btnPause.addEventListener(MouseEvent.CLICK, pauseFunction);
			this.btnRefresh.addEventListener(MouseEvent.CLICK, refreshFunction);
			this.btnFullScreen.addEventListener(MouseEvent.CLICK, fullScreenFunction);
			this.btnHalfScreen.addEventListener(MouseEvent.CLICK, halfScreenFunction);
			this.btnMute.addEventListener(MouseEvent.CLICK, muteFunction);
			this.btnUnmute.addEventListener(MouseEvent.CLICK, unmuteFunction);	
			checkAuthority();
		}
		
		//播放方法，从“暂停”状态恢复播放
		protected function playFunction(event:MouseEvent):void
		{
			addVideo();
			if(this.streamURL != null)
			{
				this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, this.videoWidth, this.videoHeight, this.sound);
				this.playerCore.connectNetConncetion();
			}
			this.btnPause.visible = true;
			this.btnPlay.visible = false;
			this.isPlay = true;
		}
		
		//暂停方法，从“播放”状态暂停
		protected function pauseFunction(event:MouseEvent):void
		{
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.close();
				hideLoading();
			}
			if(this.playerCore.netConnection != null)
			{
				this.playerCore.netConnection.close();
			}
			this.btnPause.visible = false;
			this.btnPlay.visible = true;
			this.isPlay = false;
			this.playerCore.cancelDelayToConnect();
		}
		
		//刷新方法
		private function refreshFunction(event:MouseEvent):void
		{
			if(this.playerCore.netConnection != null && this.playerCore.netStream != null)
			{
				DebugInfoManager.getInstance().log("this.streamURL = " + this.streamURL);
				this.playerCore.netStream.close();
				this.coreVideo.attachNetStream(this.playerCore.netStream);
				if(this.playerCore.video != null)
				{
					this.playerCore.video.clear();
				}
				if(this.streamURL != null)
				{
					this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, this.videoWidth, this.videoHeight, this.sound);
					this.playerCore.connectNetConncetion();
				}
				this.btnPlay.visible = false;
				this.btnPause.visible = true;
			}
		}
		
		//全屏方法
		protected function fullScreenFunction(event:MouseEvent):void
		{
			stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
			this.btnFullScreen.visible = false;
			this.btnHalfScreen.visible = true;
		}
		
		//半屏方法
		protected function halfScreenFunction(event:MouseEvent):void
		{
			stage.displayState = StageDisplayState.NORMAL;
			this.btnFullScreen.visible = true;
			this.btnHalfScreen.visible = false;
		}
		
		//恢复音量，从“静音”状态恢复
		protected function muteFunction(event:MouseEvent):void
		{
			this.slider.setValue(this.formerValue);
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.soundTransform.volume = this.formerValue;
			}
			this.btnUnmute.visible = true;
			this.btnMute.visible = false;
		}
		
		//静音
		protected function unmuteFunction(event:MouseEvent):void
		{
			this.formerValue = this.slider.getValue();
			this.slider.setValue(0);
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.soundTransform.volume = 0;
			}
			this.btnUnmute.visible = false;
			this.btnMute.visible = true;
		}
		
		//隐藏UI
		protected function UIHide(event:Event):void
		{
			if(this.containerUI != null)
			{
				this.containerUI.visible = false;
			}
		}
		
		//显示UI
		protected function UIShow(event:MouseEvent):void
		{
			if(this.containerUI != null)
			{
				this.containerUI.visible = true;
			}
		}
		
		//按键监控
		protected function keyUpHandler(event:KeyboardEvent):void
		{  
			//同时按下Ctrl + G
			if(event.keyCode == 71 && event.ctrlKey)
			{  
				if(this.containerDebufInfo.visible == false)
				{
					DebugInfoManager.getInstance().show();
				}
				else
				{
					DebugInfoManager.getInstance().hide();
				}
			}
			//同时按下Ctrl + M
			if(event.keyCode == 77 && event.ctrlKey)
			{  
				DebugInfoManager.getInstance().changeMouseEnabled();
			}
		} 
		
		//加载过程中
		private function onProgress(strResource:String, percentage:Number):void
		{
//			var returnPercent:String = Math.round(percentage * 100).toFixed(0);
//			if(percentage == 0)
//			{
//				DebugInfoManager.getInstance().log("[Flash] " + strResource + "  正在加载..." + strResource + "  " + percentage + "%");
//			}
//			else
//			{
//				DebugInfoManager.getInstance().log("[Flash] " + strResource + "  正在加载..." + strResource + "  " + percentage + "%");
//			}
//			if(this.percentLoaded != null)
//			{
//				DebugInfoManager.getInstance().log("[Flash] 设置加载百分比");
//				this.percentLoaded.setValue(percentage);
//			}
		}
		
		//加载成功
		private function callSuccess(strResource:String):void
		{
			DebugInfoManager.getInstance().log("[Flash] " + strResource + "  加载成功");
			if(strResource.indexOf("percentLoading.swf") >= 0)
			{
				setPercentLoaded();
			}
		}
		
		//加载失败
		private function callFailure(strResource:String):void
		{
			DebugInfoManager.getInstance().log("[Flash] " + strResource + "  加载失败");
		}
		
		//全部加载完成
		private function callAll(isCancelled:Boolean):void
		{
			if(isCancelled == true)
			{
				DebugInfoManager.getInstance().log("[Flash] " + "上一个加载被取消");
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "全部加载完成");
				setGiftMovieClip();
				setLoading();
				setButton();
			}
		}
		
		private function setPercentLoaded():void
		{
			var getClass:Class;
			var mcBackground:MovieClip;
			var mcPercent:MovieClip;
			var mcSpot:MovieClip;
			getClass = ResourceLoadManager.getExportClass("percentBkg");
			if (getClass != null)
			{
				mcBackground = (new getClass()) as MovieClip;
			}
			getClass = ResourceLoadManager.getExportClass("percentLoaded");
			if (getClass != null)
			{
				mcPercent = (new getClass()) as MovieClip;
			}
			if(mcBackground != null && mcPercent != null)
			{
				DebugInfoManager.getInstance().log("[Flash] " + "this.percentLoaded成功创建");
				this.percentLoaded.setup(duplicateDisplayObject(mcBackground) as MovieClip, duplicateDisplayObject(mcPercent) as MovieClip);
				this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;
				this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
				stage.addChild(this.percentLoaded);
//				this.percentLoaded.setValue(0);
				this.percentLoaded.visible = false;
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "this.percentLoaded未成功创建");
			}
			DebugInfoManager.getInstance().log("[Flash] " + "this.percentLoaded.x = " + this.percentLoaded.x
				+ " this.percentLoaded.width = " + this.percentLoaded.width + " this.percentLoaded.y = " + this.percentLoaded.y
				+ " this.percentLoaded.height = " + this.percentLoaded.height);
		}
		
		//设置礼物横幅
		private function setGiftMovieClip():void
		{
			DebugInfoManager.getInstance().log("[Flash] " + "setGiftMovieClip()");
			this.giftMC = ResourceLoadManager.getExportMovieClip("mcGift");
			if(this.giftMC != null)
			{
				this.addChild(this.giftMC);
				this.giftMC.visible = false;
			}
			this.bannerMC = ResourceLoadManager.getExportMovieClip("mcBanner");
			if(this.bannerMC != null)
			{
				this.addChild(this.bannerMC);
				this.bannerMC.visible = false;
			}
			GiftBannerManager.getInstance().setupWithContainer(this.containerGift, stage.stageWidth, stage.stageHeight, this.giftMC, this.bannerMC);
		}
		
		private function GBMCallback(info:String):void
		{
			DebugInfoManager.getInstance().log("GBM返回信息：" + info);
		}
		
		//设置loading动画
		protected function setLoading():void
		{
			DebugInfoManager.getInstance().log("[Flash] " + "setLoading()");
			this.mcLoading = ResourceLoadManager.getExportMovieClip("loading");
			if(this.mcLoading != null)
			{
				DebugInfoManager.getInstance().log("[Flash] " + "loading成功创建");
				this.mcLoading.x = (stage.stageWidth - this.mcLoading.width) / 2;
				this.mcLoading.y = (stage.stageHeight - this.mcLoading.height) / 2;
//				if(this.streamURL == null && this.netStatusCode != "end")
//				{
					this.mcLoading.visible = true;
//				}
//				else
//				{
					this.mcLoading.stop();
					this.mcLoading.visible = false;
//				}
				var index:int = stage.getChildIndex(this.containerDebufInfo);
				stage.addChildAt(this.mcLoading, index);
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "loading未找到");
			}
			
		}
		
		//设置按钮
		private function setButton():void
		{
			setSingleButton(this.btnPlay, "btnPlay");
			setSingleButton(this.btnPause, "btnPause");
			setSingleButton(this.btnRefresh, "btnRefresh");
			setSingleButton(this.btnFullScreen, "btnFullScreen");
			setSingleButton(this.btnHalfScreen, "btnHalfScreen");
			setSingleButton(this.btnMute, "btnMute");
			setSingleButton(this.btnUnmute, "btnUnmute");
			setSingleButton(this.btnLiveButtom, "btnLiveButtom");
			setSingleButton(this.btnLiveMain, "btnLiveMain");
			setSingleButton(this.btnBarrageOff, "btnBarrageOff");
			setSingleButton(this.btnBarrageOn, "btnBarrageOn");
			setSingleButton(this.btnSendBarrage, "btnSendBarrage");
			this.buttom = ResourceLoadManager.getExportMovieClip("barAll");
			this.selected = ResourceLoadManager.getExportMovieClip("barSelected");
			this.dragSlider = ResourceLoadManager.getExportMovieClip("dragSlider");
			this.Timebuttom = ResourceLoadManager.getExportMovieClip("barAll");
			this.TimeSelected = ResourceLoadManager.getExportMovieClip("barSelected");
			this.TimedragSlider = ResourceLoadManager.getExportMovieClip("dragSlider");
			this.giftMC = ResourceLoadManager.getExportMovieClip("mcGift");
			this.bannerMC = ResourceLoadManager.getExportMovieClip("mcBanner");
			this.recommendmc = ResourceLoadManager.getExportMovieClip("SingleRecommend");
			this.jumpmc = ResourceLoadManager.getExportMovieClip("btnJump");
			setSingleButton(this.btnStartLive, "btnStartLive");
			setSingleButton(this.btnTip, "btnTip");
			if(this.recommendmc != null && this.jumpmc != null)
			{
				this.firstRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
				this.secondRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
				DebugInfoManager.getInstance().log("[CP Player]设置直播推荐MC");
			}
			if(this.btnTip != null)
			{
				this.btnTip.x = (stage.stageWidth - this.btnTip.width ) / 2;
				this.btnTip.y = (stage.stageHeight - this.btnTip.height ) / 2;
				stage.addChild(this.btnTip);
				DebugInfoManager.getInstance().log("CP成功找到btnTip x = " + this.btnTip.x + " y = " + this.btnTip.y + " visible = " 
					+ this.btnTip.visible + " contains = " + stage.contains(this.btnTip));
			}
			if(this.giftMC != null && this.bannerMC != null)
			{
				this.giftMC.x = 0;
				this.giftMC.y = 0;
				this.bannerMC.x = this.giftMC.x + this.giftMC.width - 11;
				this.bannerMC.y = this.giftMC.y + 23;
				this.bannerMC.width = 100;
				this.giftMC.visible = false;
				this.bannerMC.visible = false;
				this.addChild(this.giftMC);
				this.addChild(this.bannerMC);
			}
			if(this.buttom != null && this.selected != null && this.dragSlider != null)
			{
				this.slider = new Slider();
				this.slider.setup(this.buttom, this.selected, this.dragSlider, this.initValue, getSliderValueFunction);
				this.containerUI.addChild(this.slider);
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "缺少组件，Slider不可被创建");
			}
			if(this.Timebuttom != null && this.TimeSelected != null && this.TimedragSlider != null)
			{
				this.TimeSlider = new Slider();
				this.TimeSlider.setup(this.Timebuttom, this.TimeSelected, this.TimedragSlider, 0, getTimeSliderValueFunction);
				this.containerUI.addChild(this.TimeSlider);
				this.TimeSlider.visible = false;
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "缺少组件，TimeSlider不可被创建");
			}
			draw();
			this.singleRecommend = ResourceLoadManager.getExportMovieClip("SingleRecommend");
		}
		
		//设置单个按钮
		private function setSingleButton(button:UIButton, className:String):void
		{
			var mc:MovieClip = ResourceLoadManager.getExportMovieClip(className);
			if(mc != null)
			{
				button.setupMovieclip(mc);
				button.visible = false;
				this.containerUI.addChild(button);
			}
		}
		
		//深拷贝
		protected static function duplicateDisplayObject(target:DisplayObject, autoAdd:Boolean = false):DisplayObject
		{
			var targetClass:Class = Object(target).constructor;
			var duplicate:DisplayObject = new targetClass();
			duplicate.transform = target.transform;
			duplicate.filters = target.filters;
			duplicate.cacheAsBitmap = target.cacheAsBitmap;
			duplicate.opaqueBackground = target.opaqueBackground;
			if(autoAdd && target.parent)
			{
				target.parent.addChild(duplicate);
			}
			DebugInfoManager.getInstance().log("[CP Player] " + target.name + " 复制完成");
			return duplicate;
		}
		
		//回调函数，Slider值变化则调用
		private function getSliderValueFunction(value:Number):void
		{
			if(this.sound != null && this.playerCore.netStream != null 
				&& this.playerCore.netConnection != null)
			{
				this.sound.volume = value;
				if(this.playerCore.netStream.soundTransform != null )
				{
					this.playerCore.netStream.soundTransform = this.sound;
				}
			}
			if(value == 0)
			{
				this.btnUnmute.visible = false;
				this.btnMute.visible = true;
			}
			else
			{
				this.btnMute.visible = false;
				this.btnUnmute.visible = true;
			}
		}
		
		//回调函数，TimeSlider值变化则调用
		protected function getTimeSliderValueFunction(value:Number):void
		{
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.seek(value * this.Timebuttom.width);
			}
		}
		
		//连接成功之后回调
		protected function callback(situation:String, code:String):void
		{
			var getClass:Class;
			var mcBackground:MovieClip;
			var mcPercent:MovieClip;
			var mcSpot:MovieClip;
			getClass = ResourceLoadManager.getExportClass("percentBkg");
			if (getClass != null)
			{
				mcBackground = (new getClass()) as MovieClip;
			}
			getClass = ResourceLoadManager.getExportClass("percentLoaded");
			if (getClass != null)
			{
				mcPercent = (new getClass()) as MovieClip;
			}
			if(situation != "加载")
			{
				DebugInfoManager.getInstance().log("[situation] " + situation + " [code] " + code);
			}
			if(this.playerCore != null && this.playerCore.netStream != null)
			{
				var bufferPercent:Number = this.playerCore.netStream.bufferLength / this.playerCore.netStream.bufferTime * 100; 
				if(Math.ceil(bufferPercent) >= 100)
				{
					bufferPercent = 100;
				}
				bufferPercent = Math.round(bufferPercent);
				if(this.percentLoaded != null && (50 + bufferPercent / 100 * 50 <= 100))
				{
					this.percentLoaded.setValue( 50 + bufferPercent / 100 * 50 );
					this.percentLoaded.show();
					if(this.isPlay == true)
					{
						this.percentLoaded.visible = true;
					}
//					DebugInfoManager.getInstance().log("设置bufferPercent = " + bufferPercent);
					if(!stage.contains(this.percentLoaded))
					{
						DebugInfoManager.getInstance().log("当前不包含percentLoaded元件，将添加");
					}
					stage.addChild(this.percentLoaded);
//					DebugInfoManager.getInstance().log("this.percentLoaded.visible = " + this.percentLoaded.visible);
//					DebugInfoManager.getInstance().log("this.percentLoaded.组件.visible = " + this.percentLoaded.mcBackground.visible
//						 + this.percentLoaded.mcPercent.visible + this.percentLoaded.tfPercent.visible);
				}
			}
			else
			{
				if(this.percentLoaded != null)
				{
					this.percentLoaded.visible = false;
				}
			}
			showLoading();
			if(situation == PlayerSituation.OTHER)
			{
				if(code == "CHECK")
				{
					DebugInfoManager.getInstance().log("需要重新检查地址！");
					ExternalInterface.call("flashCallback", "urlError");
				}
			}
			if(situation == PlayerSituation.START)
			{
			}
			if(code == "NetConnection.Connect.Success")
			{
				if(this.percentLoaded != null)
				{
					this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;
					this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
					stage.addChild(this.percentLoaded);
				}
				if(this.percentLoaded != null)
				{
					this.percentLoaded.setValue(50);
				}
			}
			if(code == "NetStream.Play.Start")
			{
				addVideo();
				if(this.percentLoaded != null)
				{
					DebugInfoManager.getInstance().log("this.percentLoaded != null");
					this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;
					this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
					stage.addChild(this.percentLoaded);
				}
				else
				{
					DebugInfoManager.getInstance().log("this.percentLoaded == null");
				}
				DebugInfoManager.getInstance().log("this.percentLoaded.visible = " + this.percentLoaded.visible);
//				DebugInfoManager.getInstance().log("this.percentLoaded.组件.visible = " + this.percentLoaded.mcBackground.visible
//					+ this.percentLoaded.mcPercent.visible + this.percentLoaded.tfPercent.visible);
				
			}
			//缓冲区为空，加载，显示加载动画
			if(situation == PlayerSituation.EMPTY)
			{
				showLoading();
				if(this.percentLoaded != null)
				{
					this.percentLoaded.visible = false;
				}
			}
			//缓冲区满，播放，隐藏加载动画
			if(situation == PlayerSituation.FULL)
			{
				this.isVideoEnd = false;
				this.netStatusCode = "hide";
				if(this.containerRecommend != null)
				{
					this.containerRecommend.visible = false;
					if(stage.contains(this.containerRecommend))
					{
						stage.removeChild(this.containerRecommend);
					}
					if(this.liveEndIntervalID != 0)
					{
						clearTimeout(this.liveEndIntervalID);
					}
				}
				hideLoading();	
				if(this.percentLoaded != null)
				{
					this.percentLoaded.visible = false;
				}
				if(this.btnPlay != null && this.btnPause != null)
				{
					this.btnPlay.visible = false;
					this.btnPause.visible = true;
				}
				if(this.containerRecommend != null)
				{
					this.containerRecommend.visible = false;
				}
				this.btnMute.addEventListener(MouseEvent.CLICK, muteFunction);
				this.btnUnmute.addEventListener(MouseEvent.CLICK, unmuteFunction);	
				if(this.slider != null)
				{
					this.slider.addSliderEventListenerFunc();
				}
			}
			if(code == "NetConnection.Connect.Closed")
			{
				DebugInfoManager.getInstance().log("[CP Flash] " + "this.serverURL = " + this.serverURL);
				DebugInfoManager.getInstance().log("[CP Flash] " + "this.streamURL = " + this.streamURL);
				this.btnMute.removeEventListener(MouseEvent.CLICK, muteFunction);
				this.btnUnmute.removeEventListener(MouseEvent.CLICK, unmuteFunction);	
				this.slider.removeSliderEventListener();
				this.playerCore.retry();
				DebugInfoManager.getInstance().log("服务器地址" + this.serverURL);
			}
			if(situation == PlayerSituation.STOP)
			{
				this.isVideoEnd = true;
				hideLoading();
				if(this.percentLoaded != null)
				{
					this.percentLoaded.visible = false;
				}
				if(stage.contains(this.playerCore.video))
				{
					this.playerCore.video.clear();
				}
			}
			if(this.percentLoaded != null && this.percentLoaded.visible == true)
			{
				if(this.containerUI != null)
				{
					stage.addChildAt(this.percentLoaded, stage.getChildIndex(this.containerUI));
				}
				else
				{
					stage.addChild(this.percentLoaded);
				}
			}
		}
		
		//获得元数据回调 
		protected function getInfoCallback(duration:Number):void
		{
			this.duration = duration;
		}
		
		//将video对象添加到舞台，并设置宽高等
		protected function addVideo():void
		{
//			DebugInfoManager.getInstance().log("ADDVIDEO");
			if(this.playerCore.video != null)
			{
				this.playerCore.video.drawWithDirection(this.screenDirection, this.videoWidth, this.videoHeight);
				if(stage.contains(this.playerCore.video))
				{
					stage.addChildAt(this.playerCore.video, 0);
				}
				else
				{
					stage.addChildAt(this.playerCore.video, 0);
				}
			}
		}
		
		//尺寸变化，更新组件大小及坐标
		protected function resizeHandler(event:Event):void
		{	
//			DebugInfoManager.getInstance().log("[RESIZE]");
			if(stage.displayState == StageDisplayState.FULL_SCREEN)
			{
				if(this.btnFullScreen != null && this.btnHalfScreen != null)
				{
					this.btnFullScreen.visible = false;
					this.btnHalfScreen.visible = true;
				}
			}
			else
			{
				if(this.btnFullScreen != null && this.btnHalfScreen != null)
				{
					this.btnFullScreen.visible = true;
					this.btnHalfScreen.visible = false;
				}
			}
			if(this.mcLoading != null)
			{
				this.mcLoading.x = (stage.stageWidth - this.mcLoading.width) / 2;
				this.mcLoading.y = (stage.stageHeight - this.mcLoading.height) / 2;
			}
			redraw();
		} 
		
		//输入横竖屏
		private function setScreenDirection(screenDirection:int):void
		{
			if(screenDirection == 0)
			{
				this.screenDirection = VideoScreenDirection.HORIZONTAL;
			}
			if(screenDirection == 1)
			{
				this.screenDirection = VideoScreenDirection.VERTICAL;
			}
			else
			{
				this.screenDirection = VideoScreenDirection.HORIZONTAL;
			}
			if(DebugInfoManager.getInstance() != null)
			{
				DebugInfoManager.getInstance().log("[-->JS-->]" + " [方向] " + this.screenDirection);
			}
		}
		
		//设置当前音量和权限
		private function setVolumeAuthority(currentVolume:Number, authority:int):void
		{
			this.sound.volume = currentVolume;
			this.authorityToChangeVolume = authority;
			setVolume();
			checkAuthority();
		}
		
		//设置音量
		private function setVolume():void
		{
			DebugInfoManager.getInstance().log("[-->JS-->]" + "[setVolume] " + this.sound.volume);
			if(this.playerCore.netConnection != null && this.playerCore.netStream != null)
			{
				this.playerCore.netStream.soundTransform = this.sound;
				DebugInfoManager.getInstance().log("[succeed]" + "设置音量为：" + this.playerCore.netStream.soundTransform.volume);
			}
			this.slider.setValue(this.sound.volume);
		}
		
		//检查权限，进行相应处理
		private function checkAuthority():void
		{
			if(this.authorityToChangeVolume == 1)
			{
				this.btnMute.removeEventListener(MouseEvent.CLICK, muteFunction);
				this.btnUnmute.removeEventListener(MouseEvent.CLICK, unmuteFunction);	
				this.slider.removeSliderEventListener();
			}
			if(this.authorityToChangeVolume == 0)
			{
				this.btnMute.addEventListener(MouseEvent.CLICK, muteFunction);
				this.btnUnmute.addEventListener(MouseEvent.CLICK, unmuteFunction);	
				if(this.slider != null)
				{
					this.slider.addSliderEventListenerFunc();
				}
			}
		}
		
		//显示加载动画
		protected function showLoading():void
		{
			if(this.isVideoEnd == false)
			{
				if(this.mcLoading != null)
				{
					this.mcLoading.x = (stage.stageWidth - this.mcLoading.width) / 2;
					this.mcLoading.y = (stage.stageHeight - this.mcLoading.height) / 2;
					this.mcLoading.play();
					this.mcLoading.visible = true;
				}
			}
			if(this.containerRecommend != null)
			{
				this.containerRecommend.visible = false;
			}
		}
		
		//隐藏加载动画
		protected function hideLoading():void
		{
			if(this.mcLoading != null)
			{
				this.mcLoading.stop();
				this.mcLoading.visible = false;
			}
			if(this.percentLoaded != null)
			{
				this.percentLoaded.visible = false;
			}
		}
		
		//视频播放停止
		protected function stopVideo():void
		{
			if(this.playerCore != null && this.playerCore.video != null && stage.contains(this.playerCore.video))
			{
				this.playerCore.video.clear();
			}
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.close();
			}
			if(this.playerCore.netConnection != null)
			{
				this.playerCore.netConnection.close();
			}
		}
	}
}