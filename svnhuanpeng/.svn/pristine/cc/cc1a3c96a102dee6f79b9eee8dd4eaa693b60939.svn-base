package
{
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
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
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.ui.Keyboard;
	import flash.utils.Timer;
	
	import hpPlayer.CommonPlayer;
	import hpPlayer.DebugInfoManager;
	import hpPlayer.RTMPPlayer;
	
	public class IndexPlayer extends RTMPPlayer
	{
		private var backgroundR:Shape;							//推荐背景
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		//构造函数
		public function IndexPlayer()
		{
			super();
			stage.addEventListener(Event.RESIZE, resizeHandler);
			ExternalInterface.addCallback("inputURL", inputURL);
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight;
			this.containerRecommend.mouseEnabled = true;
			this.containerRecommend.mouseChildren = true;
			this.backgroundR = new Shape();
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
					stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
				}
				liveEnd();
			}
		}
		
		protected function inputURL(roomID:String):void
		{
			this.liveroomURL = roomID;
			DebugInfoManager.getInstance().log("[-->JS-->]" + " [this.liveroomURL] " + this.liveroomURL);
		}
		
		override protected function liveEnd(isLive:int = -1):void
		{
			this.anotherLive = 0;
			DebugInfoManager.getInstance().log("[-->JS-->]" + "[IndexPlayer][liveEnd] " + isLive);
			hideLoading();
			this.serverURL = null;
			this.streamURL = null;
			this.liveroomURL = null;
			super.liveEnd(isLive);
			this.btnLiveMain.visible = false;
			this.shouldShowRecommend = 1;
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, RTMPUIShow);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, UIShow);
		}
		
		override protected function showLoading():void
		{
			super.showLoading();
			if(this.btnLiveMain != null)
			{
				this.btnLiveMain.visible = false;
			}
		}
		
		//设置容器深度
		private function setContainerIndex():void
		{
			if(this.containerUI != null)
			{
				stage.addChild(this.containerUI);
			}
			if(this.containerRecommend != null)
			{
				stage.addChild(this.containerRecommend);
			}
			if(this.containerDebufInfo != null)
			{
				stage.addChild(this.containerDebufInfo);
			}
		}
		
		override protected function hideLoading():void
		{
			super.hideLoading();
			if(this.btnLiveMain != null)
			{
				this.btnLiveMain.visible = true;
			}
		}
		
		override protected function draw():void
		{
			setIndexPlayerUI();
		}
		
		private function setIndexPlayerUI():void
		{
			this.containerUIBackground.graphics.clear();
			this.containerUIBackground.graphics.beginFill(0x242424, 1);
			this.containerUIBackground.graphics.drawRect(0, stage.stageHeight - this.btnPlay.height, stage.stageWidth, this.btnPlay.height);
			this.containerUIBackground.graphics.endFill();
			this.containerUI.addChildAt(this.containerUIBackground, 0);
			this.btnFullScreen.visible = false;
			this.btnHalfScreen.visible = false;
			this.btnBarrageOff.visible = false;
			this.btnBarrageOn.visible = false;
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
			this.btnLiveButtom.width = 70;
			this.btnLiveButtom.height = 21;
			this.btnLiveButtom.x = stage.stageWidth - super.btnLiveButtom.width - INTERVAL / 2;
			this.btnLiveButtom.y = stage.stageHeight - super.btnPlay.height / 2 - super.btnLiveButtom.height / 2;
			this.btnLiveButtom.visible = true;
			this.btnLiveButtom.buttonMode = true;
			this.btnLiveMain.x = Math.floor(( stage.stageWidth - this.btnLiveMain.width ) / 2);
			this.btnLiveMain.y = Math.floor((stage.stageHeight - this.btnLiveMain.height ) / 2);
			if(this.mcLoading != null)
			{
				if(this.mcLoading.visible == false)
				{
					this.btnLiveMain.visible = true;
				}
				else
				{
					this.btnLiveMain.visible = false;
				}
			}
			this.btnLiveMain.buttonMode = true;
			this.slider.x = this.btnLiveButtom.x - this.buttom.width - INTERVAL * 5 / 2;
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
			this.containerDebufInfo.width = stage.stageWidth;
			this.containerDebufInfo.height = stage.stageHeight;
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight;
			super.addVideo();
			this.addFunctions();
			this.backgroundR.graphics.clear();
			this.backgroundR.graphics.beginFill(0xffffff, 0);
			this.backgroundR.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight - 48);
			this.backgroundR.graphics.endFill();
			this.containerRecommend = new Sprite();
			this.containerRecommend.addChild(this.backgroundR);
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
		
		override protected function redraw():void
		{
			setIndexPlayerUI();
		}
		
		override protected function resizeHandler(event:Event):void
		{
//			DebugInfoManager.getInstance().log("IndexPlayer [resizeHandler]");
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
		
		//添加功能
		override protected function addFunctions():void
		{
			super.addFunctions();
			this.btnLiveButtom.addEventListener(MouseEvent.CLICK, toLiveRoomFunction);
			this.btnLiveMain.addEventListener(MouseEvent.CLICK, toLiveRoomFunction);
		}
		
		private function toLiveRoomFunction(event:MouseEvent):void
		{
			DebugInfoManager.getInstance().log("toLiveRoomFunction() 跳转到" + this.liveroomURL);
			if(this.liveroomURL != null)
			{
				var LiveRoomURLRequest:URLRequest = new URLRequest(this.liveroomURL);
				navigateToURL(LiveRoomURLRequest, "_self");
			}
		}
	}
}