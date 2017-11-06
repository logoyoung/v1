package
{
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageScaleMode;
	import flash.events.ActivityEvent;
	import flash.events.Event;
	import flash.events.FocusEvent;
	import flash.events.FullScreenEvent;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.events.NetStatusEvent;
	import flash.events.StatusEvent;
	import flash.events.TextEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.filters.GlowFilter;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.media.SoundTransform;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.system.Capabilities;
	import flash.system.Security;
	import flash.system.SecurityPanel;
	import flash.system.System;
	import flash.text.Font;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFieldType;
	import flash.text.TextFormat;
	import flash.ui.Keyboard;
	import flash.utils.ByteArray;
	import flash.utils.Timer;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	import hpPlayer.Barrage.BarrageItem;
	import hpPlayer.Barrage.BarrageManager;
	import hpPlayer.CommonPlayer;
	import hpPlayer.CountDown;
	import hpPlayer.DebugInfoManager;
	import hpPlayer.RTMPPlayer;
	import hpPlayer.UIButton;
	import hpPlayer.giftBanner.GiftBannerManager;
	
	public class LiveRoomPlayer extends RTMPPlayer
	{
		private var timerHide:Timer;							//计时器，定时隐藏工具条
		private var jsonObj:Object; 							//JSON对象
		private var containerBarrage:Sprite;					//弹幕容器
		private var isBarrageOn:Boolean = true;				//弹幕是否开启
		private var fontSelectedList:Array;					//命中的字体
		private const fontCompareList:Array = new Array("Microsoft YaHei UI",	//预订字体
			"Microsoft YaHei", "STHeiti", "PingFang SC", "PingFang", "Helvetica");
		private var tfGetBarrage:TextField;					//全屏接收用户输入弹幕的文本对象
		private var connectIntervalId:int;						//连接时间间隔ID
		private var backgroundGetBarrage:Shape;				//弹幕输入背景
		private var MinTimeInterval:int;						//发送弹幕的最小时间间隔
		private var MaxCharacterNumber:int;					//发送弹幕的最大字符数
		private var countdown:CountDown;						//弹幕倒计时
		private var backgroundR:Shape;							//推荐背景
		private var camera:Camera;
		private var video:Video;
		private var rtmp:String;
		private var microphone:Microphone;
		private var baMicrophone:Array;
		private var baCamera:Array;
		private var cmFunc:String;
		private var bgContainerBarrage:Shape;
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function LiveRoomPlayer()
		{
			super();
			this.bgContainerBarrage = new Shape();
			stage.addEventListener(KeyboardEvent.KEY_UP, fullScreenEnterSendBarrage); 
			stage.addEventListener(Event.RESIZE, this.resizeHandler);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
			this.timerHide = new Timer(5000, 1);
			this.timerHide.addEventListener(TimerEvent.TIMER_COMPLETE, LiveRoomUIHide);
			this.timerHide.start();
			ExternalInterface.addCallback("addGiftBanner", getGiftBannerInfo);
			ExternalInterface.addCallback("JSONtoString",JSaddBarrage);
			ExternalInterface.addCallback("isLoggedIn", isLoggedIn);
			ExternalInterface.addCallback("getCM", asFunc);  
			var background:Shape = new Shape();
			background.graphics.beginFill(0xffffff, 0);
			background.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			background.graphics.endFill();
			this.containerBarrage = new Sprite();
			this.containerBarrage.addChild(background);
			this.containerBarrage.mouseChildren = false;
			this.containerBarrage.mouseEnabled = false;
			var index:int = stage.getChildIndex(this.containerRecommend);
			stage.addChildAt(this.containerBarrage, index);
			BarrageManager.getInstance().init();
			BarrageManager.getInstance().setupWithContainer(this.containerBarrage, this.containerBarrage.width, this.containerBarrage.height);
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight;
			super.timerRTMPHide.removeEventListener(TimerEvent.TIMER_COMPLETE, RTMPUIHide);
			this.countdown = new CountDown();
			this.backgroundGetBarrage = new Shape();
			this.tfGetBarrage = new TextField();
			this.tfGetBarrage.type = TextFieldType.INPUT;
			this.tfGetBarrage.addEventListener(TextEvent.TEXT_INPUT, onTextInput); 
			this.tfGetBarrage.addEventListener(FocusEvent.FOCUS_IN, tfFocusIn);
			this.tfGetBarrage.addEventListener(FocusEvent.FOCUS_OUT, tfFocusOut);
			var tformat:TextFormat = new TextFormat();
			tformat.color = 0x333333;
			tformat.size = 16;
			this.tfGetBarrage.defaultTextFormat = tformat;
			this.backgroundR = new Shape();
			checkFont();
			if(root.loaderInfo.parameters.isLoggedIn == 0)
			{
				DebugInfoManager.getInstance().log("[FlashVars] " + "用户已登录");
				this.isLoggedin = 0;
			}
			if(root.loaderInfo.parameters.isLoggedIn == 1)
			{
				DebugInfoManager.getInstance().log("[FlashVars] " + "用户未登录");
				this.isLoggedin = 1;
			}
			if(root.loaderInfo.parameters.maxCharacterNumber)
			{
				this.MaxCharacterNumber = root.loaderInfo.parameters.maxCharacterNumber;
				DebugInfoManager.getInstance().log("[FlashVars] " + "maxCharacterNumber = " + this.MaxCharacterNumber);
			}
			else
			{
				this.MaxCharacterNumber = 50;
				DebugInfoManager.getInstance().log("[FlashVars] " + "未找到maxCharacterNumber，设置为" + this.MaxCharacterNumber);
			}
			if(root.loaderInfo.parameters.minTimeInterval)
			{
				this.MinTimeInterval = root.loaderInfo.parameters.minTimeInterval;
				DebugInfoManager.getInstance().log("[FlashVars] " + "minTimeInterval = " + this.MinTimeInterval);
			}
			else
			{
				this.MinTimeInterval = 2;
				DebugInfoManager.getInstance().log("[FlashVars] " + "未找到minTimeInterval，设置为" + this.MinTimeInterval);
			}
			if(root.loaderInfo.parameters.giftURL)
			{
				var URLString:String = root.loaderInfo.parameters.giftURL;
				DebugInfoManager.getInstance().log("[LiveroomPlayer FlashVars] " + "gift.swf地址" + URLString);
				this.listResource.push(URLString);
			}
			else
			{
				this.listResource.push("./gift.swf");
				DebugInfoManager.getInstance().log("[FlashVars] " + "gift.swf地址  " + "默认 ./gift.swf");
			}
			if(this.containerRecommend != null && this.containerBarrage != null)
			{
				stage.addChildAt(this.containerRecommend, stage.getChildIndex(this.containerBarrage));
			}
			if(root.loaderInfo.parameters.toOpenLive == 1 && root.loaderInfo.parameters.returnDeviceListFuncName != null)
			{
				this.anotherLive = 1;
				if(this.containerRecommend != null)
				{
					this.containerRecommend.visible = false;
					cancelEndLive();
				}
				DebugInfoManager.getInstance().log("[FlashVars] " + "主播房间，直接发直播 returnDeviceListFuncName = "
					+ root.loaderInfo.parameters.returnDeviceListFuncName);
			}
			else
			{
				DebugInfoManager.getInstance().log("[FlashVars] toOpenLive = " + root.loaderInfo.parameters.toOpenLive);
				DebugInfoManager.getInstance().log("[FlashVars] returnDeviceListFuncName = " + root.loaderInfo.parameters.returnDeviceListFuncName);
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
					stage.removeEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
					stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
					stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
				}
				liveEnd();
			}
			DebugInfoManager.getInstance().log("[Flash] 是否禁止对用户的摄像头和麦克风的访问Capabilities.avHardwareDisable = " 
				+ Capabilities.avHardwareDisable);
			DebugInfoManager.getInstance().log("[Flash] 摄像头列表：" + Camera.names);
			DebugInfoManager.getInstance().log("[Flash] 麦克风列表：" + Microphone.names);
		}
		
		private function asFunc(func:String):void
		{
			DebugInfoManager.getInstance().log("[JS] 返回函数名：" + func);
			if(this.cmFunc == null || this.cmFunc == "")
			{
				startLiveID = setTimeout(showTip, 1000);
			}
			this.cmFunc = func;
			this.shouldShowRecommend = 0;
			this.anotherLive = 1;
			if(this.containerRecommend != null)
			{
				this.containerRecommend.visible = false;
				if(stage.contains(this.containerRecommend))
				{
					stage.removeChild(this.containerRecommend);
				}
				while(this.containerRecommend.numChildren)
				{
					this.containerRecommend.removeChildAt(0);	
				}
				cancelEndLive();
			}
		}
		
		private function showTip():void
		{
			if(this.startLiveID != 0)
			{
				clearTimeout(this.startLiveID);
			}
			if(this.mcLoading != null)
			{
				this.mcLoading.visible = false;
			}
			if(this.containerRecommend != null)
			{
				this.containerRecommend.visible = false;
				cancelEndLive();
			}
			if(this.containerUI != null)
			{
				this.containerUI.visible = false;
			}
			if(this.btnTip != null)
			{
				this.btnTip.x = (stage.stageWidth - this.btnTip.width ) / 2;
				this.btnTip.y = (stage.stageHeight - this.btnTip.height ) / 2;
				this.btnTip.visible = true;
				stage.addChild(this.btnTip);
				this.btnTip.addEventListener(MouseEvent.CLICK, getCM);
			}
		}
		
		private function getCM(event:MouseEvent):void
		{
			this.btnTip.visible = false;
			DebugInfoManager.getInstance().log("getCM()");
			Security.showSettings(SecurityPanel.PRIVACY);
			microphone = Microphone.getMicrophone(); 
			this.baMicrophone = Microphone.names;
			var myString:String = "默认";
			for(var i:int = 0; i < baMicrophone.length; i++)
			{
				if(myString == this.baMicrophone[i])
				{
					this.baMicrophone.splice(i, 1);
					break;
				}
			}
			myString = "通讯";
			for(i=0; i < this.baMicrophone.length; i++)
			{
				if(myString == this.baMicrophone[i])
				{
					baMicrophone.splice(i, 1);
					break;
				}
			}
			myString = "内建麦克风";
			for( i=0; i < this.baMicrophone.length; i++)
			{
				if(myString == this.baMicrophone[i])
				{
					baMicrophone.splice(i, 1);
					break;
				}
			}
			camera = Camera.getCamera();
			camera.addEventListener(StatusEvent.STATUS, onCameraStatus);  
			this.baCamera = Camera.names;
			var avHardwareDisable:int;
			if(Capabilities.avHardwareDisable == true)
			{
				avHardwareDisable = 1;
			}
			else
			{
				avHardwareDisable = 0;
			}
			//true禁止访问,false允许访问
//			DebugInfoManager.getInstance().log("是否禁止对用户的摄像头和麦克风的访问：" + avHardwareDisable);
//			DebugInfoManager.getInstance().log("摄像头列表：" + this.baCamera);
//			DebugInfoManager.getInstance().log("麦克风列表：" + this.baMicrophone);
			if(this.baCamera != null && this.baMicrophone != null)
			{
				showStartLive();
			}
		}
		
		private function onMicrophoneStatus(e:StatusEvent):void 
		{				
			DebugInfoManager.getInstance().log("e.code = " + e.code);
			switch(e.code) {
				case "Microphone.Unmuted"://Microphone access was allowed
					DebugInfoManager.getInstance().log("麦克风...允许访问");
					break;
				case "Microphone.Muted"://Microphone access was denied
					DebugInfoManager.getInstance().log("麦克风...拒绝访问");
					break;
			}
			showStartLive();
		}
		
		private function onCameraStatus(e:StatusEvent):void 
		{				
			DebugInfoManager.getInstance().log("e.code = " + e.code);
			switch(e.code) {
				case "Camera.Unmuted"://Camera access was allowed允许
					DebugInfoManager.getInstance().log("摄像头...允许访问");
					showStartLive();
					break;
				case "Camera.Muted"://Camera access was denied拒绝
					DebugInfoManager.getInstance().log("摄像头...拒绝访问");
					break;
			}
		}
		
		private function showStartLive():void
		{
			if(this.btnStartLive != null)
			{
				this.btnStartLive.x = (stage.stageWidth - this.btnStartLive.width) / 2;
				this.btnStartLive.y = (stage.stageHeight - this.btnStartLive.height) / 2;
				this.btnStartLive.visible = true;
				stage.addChild(this.btnStartLive);
				this.btnStartLive.addEventListener(MouseEvent.CLICK, checkNames);
			}
		}
		
		private function checkNames(event:MouseEvent = null):void
		{
			ExternalInterface.call("startLive");
			if(this.mcLoading != null)
			{
				showLoading();
			}
			if(this.containerRecommend != null)
			{
				this.containerRecommend.visible = false;
			}
			if(this.btnStartLive != null)
			{
				this.btnStartLive.visible = false;
			}
			if(this.baCamera != null && this.baMicrophone != null)
			{
				DebugInfoManager.getInstance().log((this.baCamera).toString());
				DebugInfoManager.getInstance().log((this.baMicrophone).toString());
				ExternalInterface.call(this.cmFunc, "video", this.baCamera);
				ExternalInterface.call(this.cmFunc, "audio", this.baMicrophone);
			}
			else
			{
				asFunc(this.cmFunc);
			}
		}
		
		private function tfFocusIn(event:FocusEvent):void
		{
			if(this.timerHide != null)
			{
				this.timerHide.stop();
				this.timerHide.removeEventListener(TimerEvent.TIMER_COMPLETE, LiveRoomUIHide);
			}
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
			this.timerHide.removeEventListener(TimerEvent.TIMER_COMPLETE, LiveRoomUIHide);
			super.timerRTMPHide.removeEventListener(TimerEvent.TIMER_COMPLETE, RTMPUIHide);
		}
		
		//设置容器深度
		private function setContainerIndex():void
		{
			if(this.containerUI != null)
			{
				stage.addChild(this.containerUI);
			}
			
			if(this.containerGift != null)
			{
				stage.addChild(this.containerGift);
				this.containerGift.mouseEnabled = true;
				this.containerBarrage.mouseChildren = false;
			}
			if(this.containerRecommend != null)
			{
				stage.addChild(this.containerRecommend);
				this.containerRecommend.mouseEnabled = false;
				this.containerRecommend.mouseChildren = true;
			}
			if(this.containerBarrage != null)
			{
				stage.addChild(this.containerBarrage);
				this.containerBarrage.mouseEnabled = false;
				this.containerBarrage.mouseChildren = false;
			}
			if(this.containerDebufInfo != null)
			{
				stage.addChild(this.containerDebufInfo);
			}
			if(this.containerUI != null)
			{
				stage.addChild(this.containerUI);
			}
			if(this.percentLoaded != null)
			{
				stage.addChild(this.percentLoaded);
			}
		}
		
		private function tfFocusOut(event:FocusEvent):void
		{
			if(this.timerHide != null)
			{
				this.timerHide.start();
			}
			stage.addEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
			if(this.timerHide != null)
			{
				this.timerHide.start();
				this.timerHide.addEventListener(TimerEvent.TIMER_COMPLETE, LiveRoomUIHide);
			}
		}
		
		private function isLoggedIn(isloggedin:int):void
		{
			this.isLoggedin = isloggedin;
		}
		
		private function onTextInput(event:TextEvent):void 
		{ 
			if(event.text.toLowerCase().indexOf("<script>")>-1) 
			{ 
				event.preventDefault(); 
			} 
			this.timerHide.stop();
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
		} 
		
		//全屏方法
		override protected function fullScreenFunction(event:MouseEvent):void
		{
			if(this.isLoggedin == 0)
			{
				stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
			}
			else
			{
				stage.displayState = StageDisplayState.FULL_SCREEN;
			}
			this.btnFullScreen.visible = false;
			this.btnHalfScreen.visible = true;
		}
		
		private function getGiftBannerInfo(UID:int, receiverID:int, senderName:String, receiverName:String, liveroomURL:String):void
		{
			GiftBannerManager.getInstance().addGiftBannerMessage(UID, receiverID, senderName, receiverName, liveroomURL);
			setContainerIndex();
		}
		
		//按键监控
		override protected function keyUpHandler(event:KeyboardEvent):void
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
			setContainerIndex();
		} 
		
		private function fullScreenEnterSendBarrage(event:KeyboardEvent):void
		{
			//按下Enter,全屏发送弹幕
			if(event.keyCode == 13)
			{  
				sendBarrage();
			}
		}
		
		private function checkFont():void
		{
			var allFonts:Array = Font.enumerateFonts(true);
			var i:int;
			var fontString:String;
			for(var iCompareList:int = 0; iCompareList <　this.fontCompareList.length; iCompareList++)
			{
				for(var index:int = 0; index < allFonts.length; index++)
				{
					if(allFonts[index].fontName == fontCompareList[iCompareList])
					{
						fontString = allFonts[index].fontName;
						DebugInfoManager.getInstance().log("当前弹幕字体为： " + allFonts[index].fontName);
						break;
					}
				}
				if(fontString != null)
				{
					break;
				}
			}
		}
		
		//JS添加弹幕
		public function JSaddBarrage(str:String):void
		{
			jsonObj = new Object(); 
			jsonObj = JSON.parse(str);
			var text:String = jsonObj.msg;
			BarrageManager.getInstance().addBarrageMessage(text);
			if(this.containerBarrage != null)
			{
				stage.addChildAt(this.containerBarrage, stage.getChildIndex(this.containerUI));
			}
			setContainerIndex();
		}
		
		private function LiveRoomUIHide(event:TimerEvent):void
		{
			super.UIHide(event);
		}
		
		override protected function liveEnd(isLive:int = -1):void
		{
			DebugInfoManager.getInstance().log("[-->JS-->]" + "[LiveRoomPlayer][liveEnd] " + isLive);
			hideLoading();
			this.serverURL = null;
			this.streamURL = null;
			super.liveEnd(isLive);
			this.shouldShowRecommend = 1;
			this.anotherLive = 0;
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, LiveRoomUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
		}
		
		private function LiveRoomUIShow(event:MouseEvent):void
		{
			this.timerHide.stop();
			super.UIShow(event);
			this.timerHide.start();
			if(stage.focus == this.tfGetBarrage)
			{
				this.timerHide.removeEventListener(TimerEvent.TIMER_COMPLETE, LiveRoomUIHide);
				this.timerHide.stop();
			}
		}
		
		override protected function draw():void
		{
			if(this.isBarrageOn == true)
			{
				this.btnBarrageOn.visible = true;
				this.btnBarrageOff.visible = false;
			}
			else
			{
				this.btnBarrageOn.visible = false;
				this.btnBarrageOff.visible = true;
			}
			setLiveRoomPlayerUI();
		}
		
		private function setLiveRoomPlayerUI():void
		{
//			DebugInfoManager.getInstance().log("LiveRoomPlayer [setLiveRoomPlayerUI]");
			this.containerUIBackground.graphics.clear();
			this.containerUIBackground.graphics.beginFill(0x242424, 1);
			this.containerUIBackground.graphics.drawRect(0, stage.stageHeight - this.btnPlay.height, stage.stageWidth, this.btnPlay.height);
			this.containerUIBackground.graphics.endFill();
			this.containerUI.addChildAt(super.containerUIBackground, 0);
			var background:Shape = new Shape();
			background.graphics.beginFill(0xffffff, 0);
			background.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			background.graphics.endFill();
			this.containerBarrage.addChild(background);
			if(BarrageManager.getInstance().barrageContainer != null)
			{
				BarrageManager.getInstance().setupWithContainer(this.containerBarrage, this.containerBarrage.width, this.containerBarrage.height);
			}
			if(BarrageManager.getInstance().barrageContainer == null)
			{
				BarrageManager.getInstance().init();
				BarrageManager.getInstance().setupWithContainer(this.containerBarrage, this.containerBarrage.width, this.containerBarrage.height);
			}
			this.btnPlay.x = 0;
			this.btnPlay.y = stage.stageHeight - this.btnPlay.height;
			this.btnPlay.buttonMode = true;
			this.btnPause.x = 0;
			this.btnPause.y = stage.stageHeight - this.btnPlay.height;
			this.btnPause.buttonMode = true;
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
			this.btnRefresh.buttonMode = true;
			this.btnFullScreen.x = stage.stageWidth - this.btnFullScreen.width - INTERVAL / 2;
			this.btnFullScreen.y = stage.stageHeight - this.btnFullScreen.height;
			this.btnFullScreen.visible = true;
			this.btnHalfScreen.x = stage.stageWidth - this.btnFullScreen.width - INTERVAL / 2;;
			this.btnHalfScreen.y = stage.stageHeight - this.btnHalfScreen.height;
			this.btnHalfScreen.visible = false;
			this.slider.x = this.btnFullScreen.x - this.buttom.width - INTERVAL * 5 / 2;
			this.slider.y = stage.stageHeight - this.btnPlay.height / 2 - this.slider.height / 2;
			this.slider.visible = true;
			this.btnMute.x = this.slider.x - this.btnMute.width;
			this.btnMute.y = stage.stageHeight - this.btnMute.height;
			this.btnUnmute.x = this.slider.x - this.btnUnmute.width;
			this.btnUnmute.y = stage.stageHeight - this.btnUnmute.height;
			if(this.btnMute.visible == false && this.btnUnmute.visible == false)
			{
				this.btnMute.visible = false;
				this.btnUnmute.visible = true;
			}
			this.btnBarrageOff.x = this.btnMute.x - this.btnBarrageOff.width - INTERVAL * 3 / 2;
			this.btnBarrageOff.y = stage.stageHeight - this.btnPlay.height / 2 - this.btnBarrageOff.height / 2;
			this.btnBarrageOn.x = this.btnMute.x - this.btnBarrageOn.width - INTERVAL * 3 / 2;
			this.btnBarrageOn.y = stage.stageHeight - this.btnPlay.height / 2 - this.btnBarrageOn.height / 2;
			if(this.isBarrageOn == true)
			{
				this.btnBarrageOn.visible = true;
				this.btnBarrageOff.visible = false;
			}
			else
			{
				this.btnBarrageOn.visible = false;
				this.btnBarrageOff.visible = true;
			}
			this.containerDebufInfo.width = stage.stageWidth;
			this.containerDebufInfo.height = stage.stageHeight;
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight;
			super.addVideo();
			this.addFunctions();
			this.btnLiveButtom.visible = false;
			this.btnLiveMain.visible = false;
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
			if(stage.displayState == StageDisplayState.FULL_SCREEN_INTERACTIVE && this.isLoggedin == 0)
			{
				this.tfGetBarrage.height = 30;
				if(this.btnBarrageOn != null && this.btnRefresh != null)
				{
					if(this.btnBarrageOn.x - this.btnRefresh.x - this.btnRefresh.width >= 832 + 100 + 10)
					{
						this.tfGetBarrage.width = 832;
						this.tfGetBarrage.x = (this.btnMute.x - (this.btnRefresh.x + this.btnRefresh.width) - this.tfGetBarrage.width) / 2;
						this.tfGetBarrage.y = this.btnRefresh.y + 6 + 3;
					}
					else
					{
						this.tfGetBarrage.width = this.btnBarrageOn.x - this.btnRefresh.x - this.btnRefresh.width - 100 - 10;
						this.tfGetBarrage.x = (this.btnMute.x - (this.btnRefresh.x + this.btnRefresh.width) - this.tfGetBarrage.width) / 2;
						this.tfGetBarrage.y = this.btnRefresh.y + 6 + 3;
					}
				}
				this.tfGetBarrage.visible = true;
				this.tfGetBarrage.text = "";
				this.tfGetBarrage.maxChars = this.MaxCharacterNumber;
				this.backgroundGetBarrage.graphics.clear();
				this.backgroundGetBarrage.graphics.beginFill(0xffffff);
				this.backgroundGetBarrage.graphics.drawRect(this.tfGetBarrage.x, this.btnRefresh.y + 6, 832, 30);
				this.backgroundGetBarrage.graphics.endFill();
				this.containerUI.addChild(this.backgroundGetBarrage);
				this.containerUI.addChild(this.tfGetBarrage);
				this.btnSendBarrage.x = this.tfGetBarrage.x + this.tfGetBarrage.width + 10;
				this.btnSendBarrage.y = this.btnRefresh.y + 6;
				this.btnSendBarrage.visible = true;
				this.btnSendBarrage.addEventListener(MouseEvent.CLICK, sendBarrage);
				this.countdown.x = this.btnSendBarrage.x;
				this.countdown.y = this.btnSendBarrage.y;
				this.containerUI.addChild(this.countdown);
			}
			if(this.isLoggedin == 1)
			{
				if(this.tfGetBarrage != null)
				{
					this.tfGetBarrage.visible = false;
				}
				if(this.btnSendBarrage != null)
				{
					this.btnSendBarrage.visible = false;
				}
				if(this.backgroundGetBarrage != null)
				{
					this.backgroundGetBarrage.graphics.clear();
				}
			}
			if(this.liveEndType == 0 || this.liveEndType == 1)
			{
				if(this.isAlreadyRecommend == 0)
				{
					setRecommend();
				}
				else
				{
					if(this.firstRecommend != null && this.secondRecommend != null)
					{
						this.firstRecommend.x = int(Math.floor( (stage.stageWidth - 576) / 2));
						this.firstRecommend.y = int(Math.floor( (stage.stageHeight - 240 - 32) / 2 ));
						this.firstRecommend.setData();
						this.secondRecommend.x = int(Math.floor( this.firstRecommend.x + 280 + 16 ));
						this.secondRecommend.y = int(Math.floor( this.firstRecommend.y ));
						this.secondRecommend.setData();
						this.tfInfo.x = this.firstRecommend.x + 168;
						this.tfInfo.y = this.firstRecommend.y - 20 - this.tfInfo.height;
						this.tfMore.x = this.secondRecommend.x + 280 - this.tfMore.width;
						this.tfMore.y = this.firstRecommend.y + 240 + 20;
					}
				}
			}
			this.percentLoaded.x = (stage.stageWidth - 500) / 2 + 550 / 2 - this.percentLoaded.width / 2;//(stage.stageWidth - 500) / 2 - 20;//+ 500 / 2 - 396 / 2;
			this.percentLoaded.y = (stage.stageHeight - 220) / 2 + 220 + 20;
			setContainerIndex();
		}
		
		private function sendBarrage(event:MouseEvent = null):void
		{
			if(this.tfGetBarrage != null && this.tfGetBarrage.text != "" && this.countdown.finish && this.countdown.totalTime <= 0)
			{
				this.countdown.totalTime = this.MinTimeInterval;
				ExternalInterface.call("fullScreenSendMessage", this.tfGetBarrage.text);
				this.tfGetBarrage.text = "";
				this.countdown.x = this.btnSendBarrage.x;
				this.countdown.y = this.btnSendBarrage.y;
				this.countdown.visible = true;
				this.countdown.totalTime = this.MinTimeInterval;
				this.containerUI.addChild(this.countdown);
				this.countdown.setup(this.MinTimeInterval, this.btnSendBarrage.width, this.btnSendBarrage.height);
			}
		}
		
		private function hideTFInfo():void
		{
			if(this.connectIntervalId != 0)
			{   
				clearTimeout(this.connectIntervalId);   
			}
		}
		
		override protected function resizeHandler(event:Event):void
		{
//			DebugInfoManager.getInstance().log("LiveRoomPlayer [RESIZE]");
			if(this.btnTip != null)
			{
				this.btnTip.x = (stage.stageWidth - this.btnTip.width ) / 2;
				this.btnTip.y = (stage.stageHeight - this.btnTip.height ) / 2;
			}
			if(this.btnStartLive != null)
			{
				this.btnStartLive.x = (stage.stageWidth - this.btnStartLive.width) / 2;
				this.btnStartLive.y = (stage.stageHeight - this.btnStartLive.height) / 2;
			}
			if(this.isBarrageOn == true && this.btnBarrageOff != null && this.btnBarrageOn != null)
			{
				this.btnBarrageOn.visible = true;
				this.btnBarrageOff.visible = false;
			}
			if(this.isBarrageOn == false && this.btnBarrageOff != null && this.btnBarrageOn != null)
			{
				this.btnBarrageOn.visible = false;
				this.btnBarrageOff.visible = true;
			}
			if(stage.displayState == StageDisplayState.FULL_SCREEN || stage.displayState == StageDisplayState.FULL_SCREEN_INTERACTIVE)
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
			this.redraw();
			setContainerIndex();
		}
		
		override protected function redraw():void
		{
//			DebugInfoManager.getInstance().log("LiveRoomPlayer [REDRAW]");
			if(this.isBarrageOn == true)
			{
				this.btnBarrageOn.visible = true;
				this.btnBarrageOff.visible = false;
			}
			else
			{
				this.btnBarrageOn.visible = false;
				this.btnBarrageOff.visible = true;
			}
			setLiveRoomPlayerUI();
		}
		
		override protected function addFunctions():void
		{
			super.addFunctions();
			this.btnBarrageOff.addEventListener(MouseEvent.CLICK, BarrageOnFunction);
			this.btnBarrageOn.addEventListener(MouseEvent.CLICK, BarrageOffFunction);
		}
		
		private function BarrageOffFunction(event:MouseEvent):void
		{
			this.isBarrageOn = false;
			this.btnBarrageOff.visible = true;
			this.btnBarrageOn.visible = false;
			BarrageManager.getInstance().barrageOff();
		}
		
		private function BarrageOnFunction(event:MouseEvent):void
		{
			this.isBarrageOn = true;
			this.btnBarrageOff.visible = false;
			this.btnBarrageOn.visible = true;
			BarrageManager.getInstance().barrageOn();
		}
	}
}