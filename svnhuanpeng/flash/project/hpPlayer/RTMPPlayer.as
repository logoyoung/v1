package hpPlayer 
{
	import flash.display.Bitmap;
	import flash.display.DisplayObject;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.media.SoundTransform;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.navigateToURL;
	import flash.system.Security;
	import flash.text.Font;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.ui.Keyboard;
	import flash.utils.Timer;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	import hpPlayer.giftBanner.GiftBannerItem;
	import hpPlayer.giftBanner.GiftBannerItemStatus;
	import hpPlayer.giftBanner.GiftBannerManager;
	
	public class RTMPPlayer extends CommonPlayer
	{
		protected var timerRTMPHide:Timer;				//计时器，定时隐藏工具条
		private var containerGiftBanner:Sprite;		//礼物横幅容器
		protected var liveEndType:int = -1;			//"0"-当前没有直播，"1"-直播已结束
		private var fontSelectedList:Array;			//命中的字体
		private const fontCompareList:Array = new Array("Microsoft YaHei UI",	//预订字体
			"Microsoft YaHei", "STHeiti", "PingFang SC", "PingFang", "Helvetica");
		private var recommendFont:String;				//推荐所用字体
		private var count:int = 0;						//计数
		protected var isAlreadyRecommend:int = 0;		//是否已有推荐
		protected var timerSendID:Timer;				//计时器，定时发送主播ID和UID信息
		protected var randomNumber:Number;				//随机数，定时发送主播ID和UID信息初始时间用
		protected var anotherLive:int = 0;				//是否另有直播
		protected var startLiveID:Number;				//发直播时间间隔ID
		protected var timerResendURLRequest:Timer;		//计时器，重新发送URLRequest
		private const MAXRETRYTIME:int = 5;			//最多重试次数
		private var currentRetryTime:int = 0;		//当前重试次数
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		//构造函数
		public function RTMPPlayer()
		{
			super();
			ExternalInterface.addCallback("liveEnd", liveEnd);
//			ExternalInterface.addCallback("setHostID", setHostID);
			this.randomNumber = Math.round(30 * Math.random()) * 1000;
			this.timerSendID = new Timer(this.randomNumber, 1);
			this.timerSendID.addEventListener(TimerEvent.TIMER_COMPLETE, sendIDtoJS);
			this.timerSendID.start();
			this.timerResendURLRequest = new Timer(2000, 1);
			this.timerResendURLRequest.addEventListener(TimerEvent.TIMER_COMPLETE, resendURLRequest);
			var background:Shape = new Shape();
			background.graphics.beginFill(0xffffff, 0);
			background.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			background.graphics.endFill();
			this.containerGiftBanner = new Sprite();
			this.containerGiftBanner.addChild(background);
			var backgroundR:Shape = new Shape();
			backgroundR.graphics.beginFill(0xffffff, 0);
			backgroundR.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			backgroundR.graphics.endFill();
			this.containerRecommend = new Sprite();
			this.containerRecommend.mouseEnabled = false;
			this.containerRecommend.mouseChildren = false;
			this.containerRecommend.addChild(backgroundR);
			stage.addChildAt(this.containerRecommend, stage.getChildIndex(this.containerUI));
			GiftBannerManager.getInstance().init();
			if(this.giftMC != null && this.bannerMC != null)
			{
				GiftBannerManager.getInstance().setupWithContainer(this.containerGiftBanner, 
					this.containerGiftBanner.width, this.containerGiftBanner.height, 
					this.giftMC, this.bannerMC);
			}
			stage.addEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			this.timerRTMPHide = new Timer(5000, 1);
			this.timerRTMPHide.addEventListener(TimerEvent.TIMER_COMPLETE, RTMPUIHide);
			this.timerRTMPHide.start();
			this.RecommendURLList = new Array();
			this.RecommendPIicList = new Array();
			this.firstRecommend = new Recommend;
			this.secondRecommend = new Recommend();
			this.tfInfo = new TextField();
			this.tfMore = new TextField();
			checkRecommendFont();
//			if(root.loaderInfo.parameters.recommendPHP)
//			{
//				this.recommendPHPURL = root.loaderInfo.parameters.recommendPHP;
//				DebugInfoManager.getInstance().log("[FlashVars] " + "recommendPHP地址  = " + this.recommendPHPURL);
//			}
//			else
//			{
//				this.recommendPHPURL = "http://dev.huanpeng.com/main/api/other/flashRecommend.php";
//				DebugInfoManager.getInstance().log("[FlashVars] " + "recommendPHP地址  " + "默认 http://dev.huanpeng.com/main/api/other/flashRecommend.php");
//			}
//			if(root.loaderInfo.parameters.LiveRecommendURL)
//			{
//				var URLString:String = root.loaderInfo.parameters.LiveRecommendURL;
//				DebugInfoManager.getInstance().log("[FlashVars] " + "LiveRecommend.swf地址  " + URLString);
//				this.listResource.push(URLString);
//			}
//			else
//			{
//				this.listResource.push("./LiveRecommend.swf");
//				DebugInfoManager.getInstance().log("[FlashVars] " + "LiveRecommend.swf地址  " + "默认 ./LiveRecommend.swf");
//			}
//			if(root.loaderInfo.parameters.isLiving == 0)
//			{
//				this.isLiving = 0;
//				DebugInfoManager.getInstance().log("[FlashVars] " + "当前没有直播，跳转直播推荐");
//				liveEnd();
//			}
//			if(root.loaderInfo.parameters.isLiving == 1)
//			{
//				this.isLiving = 1;
//			}
			if(root.loaderInfo.parameters.hostID)
			{
				this.hostID = root.loaderInfo.parameters.hostID;
				DebugInfoManager.getInstance().log("[FlashVars] " + "hostID = " + this.hostID);
			}
			if(this.hostID != -1 && this.urlPHPURL != null && (this.isLiving == 1))
			{
				DebugInfoManager.getInstance().log("满足条件，发送url请求地址");
				this.urlRequest = new URLPostRequest();
				this.urlRequest.setup(this.urlPHPURL, "POST", this.hostID, this.geturlData);	
			}
			if(this.recommendPHPURL != null && this.userID >= 0 && this.recommendData == null)
			{
				this.postrequest.setup(this.recommendPHPURL, URLRequestMethod.POST, this.userID, super.getRecommendData);
			}
			if(root.loaderInfo.parameters.hostID)
			{
				this.hostID = root.loaderInfo.parameters.hostID;
				DebugInfoManager.getInstance().log("[FlashVars] " + "hostID = " + this.hostID);
			}
			else
			{
				if(this.percentLoaded != null)
				{
					this.percentLoaded.visible = false;
				}
				if(this.containerUI != null)
				{
					this.containerUI.visible = false;
					stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
				}
				liveEnd();
			}
			
		}
		
		//用于获取URL信息，并输出
		override protected function geturlData(result:String):void
		{
			DebugInfoManager.getInstance().log("[RTMP Flash] 收到url信息" + result); 
			var obj:Object = JSON.parse(result);
			if(obj["content"]["desc"] != "无效的luid" && obj["content"]["streamList"][0] != null)
			{
				this.oriServerURL = "rtmp://" + obj["content"]["streamList"][0] + "/";
			}
			if(obj["content"]["streamList"] == null || obj["content"]["streamList"][0] == null)
			{
				DebugInfoManager.getInstance().log("[RTMP Flash] 收到无效url信息"); 
//				if(this.currentRetryTime >= this.MAXRETRYTIME - 1)
//				{
					liveEnd();
//				}
//				else
//				{
//					if(this.timerResendURLRequest != null)
//					{
//						this.timerResendURLRequest.start();
//					}
//				}
				return;
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
			DebugInfoManager.getInstance().log("[RTMP Flash] " + "this.oriServerURL = " + this.oriServerURL);
			DebugInfoManager.getInstance().log("[RTMP Flash] " + "this.oriStreamURL = " + this.oriStreamURL);
			this.httpRequest.init(this.oriServerURL.slice(7) //去掉rtmp://
				+ this.oriStreamURL,
				this.getData);
			DebugInfoManager.getInstance().log("--发送请求到WS--"); 
			//			if(this.oldStreamURL != null)
			//			{
			//				if(this.streamURL.indexOf(this.oldStreamURL) >= 0)// == this.oldStreamURL)
			//				{
			//					DebugInfoManager.getInstance().log("与之前stream相同，不进行连接");
			//					return;
			//				}
			//			}
			//			if(this.serverURL != null && this.streamURL != null)
			//			{
			//				var str:String = this.streamURL;
			//				var strIndex:int = this.streamURL.indexOf("?");
			//				this.oldStreamURL = str.slice(0, strIndex);
			//				DebugInfoManager.getInstance().log("[func geturlData] 设置 this.oldStreamURL = " + this.oldStreamURL);
			//				this.playerCore.init(this.serverURL, this.streamURL, this.coreVideo, callback, getInfoCallback, stage.stageWidth, stage.stageHeight, this.sound);
			//				this.playerCore.connectNetConncetion();
			//			}
		}
		
		override protected function setHostID(hostid:Number):void
		{
			DebugInfoManager.getInstance().log("[-->JS-->]" + "[RtmpPlayer][setHostID] hostid = " + hostid);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			super.setHostID(hostid);
		}
		
		//暂停方法，从“播放”状态暂停
		override protected function pauseFunction(event:MouseEvent):void
		{
			cancelEndLive();
			if(this.playerCore.netStream != null)
			{
				//				this.playerCore.netStream.pause();
				this.playerCore.netStream.close();
				//				showLoading();
				hideLoading();
			}
			if(this.playerCore.netConnection != null)
			{
				this.playerCore.netConnection.close();
				this.playerCore.netConnection = null;
			}
			this.btnPause.visible = false;
			this.btnPlay.visible = true;
			this.isPlay = false;
			this.playerCore.cancelDelayToConnect();
		}
		
		protected function sendIDtoJS(event:TimerEvent):void
		{
			ExternalInterface.call("getTime", this.hostID, this.userID);
			this.timerSendID.reset();
			this.timerSendID = null;
			this.randomNumber = 30 * 1000;
			this.timerSendID = new Timer(this.randomNumber, 1);
			this.timerSendID.addEventListener(TimerEvent.TIMER_COMPLETE, sendIDtoJS);
			this.timerSendID.start();
		}
		
		protected function resendURLRequest(event:TimerEvent):void
		{
			DebugInfoManager.getInstance().log("resendURLRequest");
			this.timerResendURLRequest.stop();
			this.timerResendURLRequest.reset();
			this.currentRetryTime++;
			if(this.currentRetryTime >= this.MAXRETRYTIME)
			{
				DebugInfoManager.getInstance().log("重试超过" + this.MAXRETRYTIME + "次,不再重试");
				return;
			}
			if(this.hostID != 0 && this.urlPHPURL != null)
			{
				this.urlRequest = new URLPostRequest();
				this.urlRequest.setup(this.urlPHPURL, "POST", this.hostID, geturlData);	
			}
		}
		
		private function checkRecommendFont():void
		{
			var allFonts:Array = Font.enumerateFonts(true);
			var i:int;
			for(var iCompareList:int = 0; iCompareList <　this.fontCompareList.length; iCompareList++)
			{
				for(var index:int = 0; index < allFonts.length; index++)
				{
					if(allFonts[index].fontName == fontCompareList[iCompareList])
					{
						this.recommendFont = allFonts[index].fontName;
						break;
					}
				}
				if(this.recommendFont != null)
				{
					DebugInfoManager.getInstance().log("直播推荐字体： " + this.recommendFont);
					break;
				}
			}
		}
		
//		//输入地址
//		override protected function inputURL(StreamURL:String, ServerURL:String = null, LiveRoomURL:String = null):void
//		{	
//			DebugInfoManager.getInstance().log("[IndexPlayer]");
//			DebugInfoManager.getInstance().log("[-->JS-->]" + " [ServerURL] " + ServerURL + " [videoURL] " + StreamURL + " [LiveRoomURL] " + LiveRoomURL);
//			//设置URL
//			this.streamURL = StreamURL;
//			this.serverURL = ServerURL;
//			this.liveroomURL = LiveRoomURL;
//			super.inputURL(this.streamURL, this.serverURL, this.liveroomURL);
//			if(this.firstRecommend != null)
//			{
//				this.firstRecommend.visible = false;
//				this.firstRecommend.remove();
//			}
//			if(this.secondRecommend != null)
//			{
//				this.secondRecommend.visible = false;
//				this.secondRecommend.remove();
//			}
//			if(this.tfInfo != null)
//			{
//				this.tfInfo.visible = false;
//			}
//			if(this.tfMore != null)
//			{
//				this.tfMore.visible = false;
//			}
//			this.isAlreadyRecommend = 0;
//			if(this.containerUI != null)
//			{
//				this.containerUI.visible = true;
//				stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
//				stage.addEventListener(Event.MOUSE_LEAVE, UIHide);
//			}
////			if(this.liveEndIntervalID != 0)
////			{
////				clearTimeout(this.liveEndIntervalID);
////			}
//			cancelEndLive();
//		}
		
		protected function RTMPUIHide(event:TimerEvent):void
		{
			super.UIHide(event);
		}
		
		protected function RTMPUIShow(event:MouseEvent):void
		{
			this.timerRTMPHide.stop();
			super.UIShow(event);
			this.timerRTMPHide.start();
		}
		
		protected function liveEnd(isLive:int = -1):void
		{
			DebugInfoManager.getInstance().log("[调用]" + "[RTMPPlayer][liveEnd]");
			if(this.recommendPHPURL != null && this.userID >= 0)// && this.recommendData == null)
			{
				this.postrequest.setup(this.recommendPHPURL, URLRequestMethod.POST, this.userID, getRTMPRecommendData);
			}
			if(this.percentLoaded != null)
			{
				this.percentLoaded.visible = false;
			}
			this.liveEndType = 0;
			this.shouldShowRecommend = 1;
//			if(this.playerCore.netStream != null)
//			{
//				this.playerCore.netStream.close();
//			}
//			if(this.playerCore.netConnection != null)
//			{
//				this.playerCore.netConnection.close();
//			}
//			if(this.playerCore.video != null)
//			{
//				DebugInfoManager.getInstance().log("[调用]" + "[RTMPPlayer][liveEnd]" + "this.playerCore.video != null");
//				this.playerCore.video.clear();
//			}
			this.liveroomURL = null;
			this.streamURL = null;
			this.serverURL = null;
			if(this.playerCore != null)
			{
				this.playerCore.stop();
				this.playerCore.cancelDelayToConnect();
			}
			
//			this.btnLiveMain.visible = false;
			super.hideLoading();
			if(this.containerUI != null)
			{
				this.containerUI.visible = false;
			}
			if(this.containerRecommend != null)
			{
				stage.addChildAt(this.containerRecommend, stage.getChildIndex(this.containerDebufInfo));
			}
			if(this.isAlreadyRecommend == 0)
			{
				DebugInfoManager.getInstance().log("this.isAlreadyRecommend == 0");
				if(this.recommendData != null)
				{
					setRecommend();
				}
				else
				{
					DebugInfoManager.getInstance().log("this.recommendData == null");
				}
				this.isAlreadyRecommend = 1;
			}
			else
			{
				DebugInfoManager.getInstance().log("this.isAlreadyRecommend == 1");
			}
			this.liveEndIntervalID = setTimeout(trySetRecommend, 1000);
			
		}
		
		//用于获取推荐信息，并输出
		protected function getRTMPRecommendData(result:String):void
		{
			var patternFirst:String = "firstPicURL"; 
			var patternSecond:String = "secondPicURL"; 
			var str:String = result; 
			if(result == null || result == "" || str.indexOf(patternFirst) < 0 || str.indexOf(patternSecond) < 0)
			{
				DebugInfoManager.getInstance().log("[CP Flash] 收到无效返回值：缺少必要信息"); 
				DebugInfoManager.getInstance().log("[CP Flash] " + result); 
				return;
			}
			DebugInfoManager.getInstance().log("[CP Flash] " + result); 
			DebugInfoManager.getInstance().log("[CP Flash] 收到返回值，开始解析推荐信息JSON "); 
			var obj:Object = JSON.parse(result);
			this.recommendData = result;
			DebugInfoManager.getInstance().log("[CP Flash] this.recommendData = " + this.recommendData); 
			if(this.recommendDebugTime <= 0)
			{
				DebugInfoManager.getInstance().log("解析JSON 0  status = " + obj.status);
				DebugInfoManager.getInstance().log("解析JSON 0  content moreLive = " + obj["content"]["moreLive"]);
				DebugInfoManager.getInstance().log("解析JSON 0  content list = " + obj["content"]["list"]);
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
			setRecommend();
		}
//		override protected function callback(situation:String, code:String):void
//		{
//			super.callback(situation, code);
//			if(code == "NetConnection.Connect.Closed")
//			{
//				//				hideLoading();
//				//				this.playerCore.netStream = null;
//				//				this.playerCore.netConnection = null;
//				var result:int = ExternalInterface.call("ClosedResolution");
//				if(result == 0)//流断开——展示loading动画
//				{
//					DebugInfoManager.getInstance().log("服务器断开，原因是 " + result + " 流断开");
//					showLoading();
//				}
//				if(result == 1)//直播结束——展示直播推荐
//				{
//					DebugInfoManager.getInstance().log("服务器断开，原因是 " + result + " 直播结束");
//					liveEnd();
//				}
//				if(result == 2)//其他——展示“主播暂时不在，……”
//				{
//					DebugInfoManager.getInstance().log("服务器断开，原因是 " + result + " 其他");
//					//showAbsence;
//				}
//			}
//		}
		
		//重新尝试添加Recommend
		public function trySetRecommend():void	
		{
			DebugInfoManager.getInstance().log("trySetRecommend()");
			if(this.recommendmc != null && this.jumpmc != null)
			{
				cancelEndLive();
				setRecommend();
			}
		}
		
		//取消超时
		public function cancelEndLive():void
		{
			DebugInfoManager.getInstance().log("cancelEndLive()");
			if(this.liveEndIntervalID != 0)
			{   
				clearTimeout(this.liveEndIntervalID);   
			}
			DebugInfoManager.getInstance().log("this.liveEndIntervalID = " + this.liveEndIntervalID);
		}
		
		//设置loading动画
		override protected function setLoading():void
		{
			this.mcLoading = ResourceLoadManager.getExportMovieClip("loading");
			if(this.mcLoading != null)
			{
				this.mcLoading.x = (stage.stageWidth - this.mcLoading.width) / 2;
				this.mcLoading.y = (stage.stageHeight - this.mcLoading.height) / 2;
				if(this.streamURL == null && this.liveEndType == -1)
				{
					this.mcLoading.visible = true;
				}
				else
				{
					this.mcLoading.stop();
					this.mcLoading.visible = false;
				}
				var index:int = stage.getChildIndex(this.containerDebufInfo);
				stage.addChildAt(this.mcLoading, index);
			}
			else
			{
				DebugInfoManager.getInstance().log("[Flash] " + "loading未找到");
			}
		}
		
		protected function setRecommend():void
		{
			hideLoading();
			if(this.anotherLive > 0)
			{
				return;
			}
			if(this.percentLoaded != null)
			{
				this.percentLoaded.visible = false;
			}
			this.isAlreadyRecommend = 1;
			if(this.recommendData == null || this.recommendData == "")
			{
				DebugInfoManager.getInstance().log("[flash] 未找到推荐信息");
				this.liveEndIntervalID = setTimeout(trySetRecommend, 1000);
				return;
			}
			var obj:Object = JSON.parse(this.recommendData);
			var recommendCount:int = 0;
			this.moreLiveURL = obj["content"]["moreLive"];
			DebugInfoManager.getInstance().log("this.recommendData = " + this.recommendData);
			if(this.recommendmc != null && this.jumpmc != null)
			{
				DebugInfoManager.getInstance().log("可以创建Recommend");
				if(obj["content"]["list"][0]["firstScreenDirection"] != null)
				{
					recommendCount++;
					DebugInfoManager.getInstance().log("[flash] first recommendCount = " + recommendCount);
					if(this.firstRecommend.mcRecommend == null && this.firstRecommend.mcJumpTo == null)//count >= 0)
					{
						this.firstRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
					}
					this.firstRecommend.setup(obj["content"]["list"][0]["firstScreenDirection"], obj["content"]["list"][0]["firstHostName"], 
						obj["content"]["list"][0]["firstAudienceNumber"], obj["content"]["list"][0]["firstGameName"], 
						obj["content"]["list"][0]["firstPicURL"], obj["content"]["list"][0]["firstLiveRoomURL"],
						this.recommendFont);
				}
				if(obj["content"]["list"][1]["secondScreenDirection"] != null)
				{
					recommendCount++;
					DebugInfoManager.getInstance().log("[flash] second recommendCount = " + recommendCount);
					if(this.secondRecommend.mcRecommend == null && this.secondRecommend.mcJumpTo == null)//count >= 0)
					{
						this.secondRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
					}
					this.secondRecommend.setup(obj["content"]["list"][1]["secondScreenDirection"], obj["content"]["list"][1]["secondHostName"], 
						obj["content"]["list"][1]["secondAudienceNumber"], obj["content"]["list"][1]["secondGameName"], 
						obj["content"]["list"][1]["secondPicURL"], obj["content"]["list"][1]["secondLiveRoomURL"],
						this.recommendFont);
				}
				this.firstRecommend.x = int(Math.floor( (stage.stageWidth - (recommendCount * 280 + (recommendCount - 1) * 16)) / 2));
				this.firstRecommend.y = int(Math.floor( (stage.stageHeight - 240 - 32) / 2 ));
				if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
				{
					this.firstRecommend.visible = false;
				}
				else
				{
					this.firstRecommend.visible = true;
				}
				this.firstRecommend.visible = true;
				if(this.firstRecommend.sprite != null)
				{
					this.firstRecommend.sprite.visible = true;
				}
				this.containerRecommend.addChild(this.firstRecommend);
				this.secondRecommend.x = int(Math.floor( this.firstRecommend.x + 280 + 16 ));
				this.secondRecommend.y = int(Math.floor( this.firstRecommend.y ));
				count = 1;
				if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
				{
					this.secondRecommend.visible = false;
				}
				else
				{
					this.secondRecommend.visible = true;
				}
				if(this.secondRecommend.sprite != null)
				{
					this.secondRecommend.sprite.visible = true;
				}
				this.containerRecommend.addChild(this.secondRecommend);
				var tformat:TextFormat = new TextFormat();
				tformat.size = 16;
				tformat.color = 0xffffff;
				tformat.font = this.recommendFont;
				this.tfInfo.defaultTextFormat = tformat;
				this.tfInfo.autoSize = TextFieldAutoSize.CENTER;
				this.tfInfo.text = "主播休息了，看看其他精彩直播吧";
				this.tfInfo.x = this.firstRecommend.x + ( recommendCount * 280 + (recommendCount - 1) * 16 - this.tfInfo.width ) / 2;
				this.tfInfo.y = this.firstRecommend.y - 20 - this.tfInfo.height;
				this.tfInfo.selectable = false;
				this.tfInfo.alpha = 0.5;
				if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
				{
					this.tfInfo.visible = false;
				}
				else
				{
					this.tfInfo.visible = true;
				}
				if(!stage.contains(this.tfInfo))
				{
					this.containerRecommend.addChild(this.tfInfo);
					if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
					{
						this.tfInfo.visible = false;
					}
					else
					{
						this.tfInfo.visible = true;
					}
				}
				tformat.color = 0xff6c00;
				this.tfMore.defaultTextFormat = tformat;
				this.tfMore.autoSize = TextFieldAutoSize.CENTER;
				this.tfMore.text = "更多直播 >>";
				this.tfMore.x = this.firstRecommend.x + recommendCount * 280 - this.tfMore.width;
				this.tfMore.y = this.firstRecommend.y + 240 + 20;
				this.tfMore.selectable = false;
				this.tfMore.alpha = 1;
				if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
				{
					this.tfMore.visible = false;
				}
				else
				{
					this.tfMore.visible = true;
				}
				if(!stage.contains(this.tfMore))
				{
					this.containerRecommend.addChild(this.tfMore);
					if(this.firstRecommend.hostName == null && this.secondRecommend.hostName == null)
					{
						this.tfMore.visible = false;
					}
					else
					{
						this.tfMore.visible = true;
					}
				}
				var sprite:Sprite = new Sprite();
				sprite.graphics.beginFill(0xffffff, 0);
				sprite.graphics.drawRect(this.tfMore.x, this.tfMore.y, this.tfMore.width, this.tfMore.height);
				sprite.graphics.endFill();
				sprite.addEventListener(MouseEvent.CLICK, toMoreLive);
				sprite.buttonMode = true;
				this.containerRecommend.addChild(sprite);
				if(this.anotherLive > 0)
				{
					this.containerRecommend.visible = false;
				}
				else
				{
					this.containerRecommend.visible = true;
				}
				if(!stage.contains(this.containerRecommend))
				{
					stage.addChildAt(this.containerRecommend, stage.getChildIndex(this.containerUI));
				}
				if(this.btnLiveMain != null)
				{
					this.btnLiveMain.visible = false;
				}
			}
			else
			{
				this.liveEndIntervalID = setTimeout(trySetRecommend, 1000);
			}
			if(!stage.contains(this.containerRecommend))
			{
				stage.addChild(this.containerRecommend);
			}
		}
		
		private function toMoreLive(event:MouseEvent):void
		{
			if(this.moreLiveURL != null)
			{
				var LiveRoomURLRequest:URLRequest = new URLRequest(this.moreLiveURL);
				navigateToURL(LiveRoomURLRequest, "_self");
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
			return duplicate;
		}
	}
}