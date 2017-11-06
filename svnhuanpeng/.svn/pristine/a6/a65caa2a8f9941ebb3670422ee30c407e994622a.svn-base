package
{
	import flash.display.Bitmap;
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
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.geom.Rectangle;
	import flash.media.SoundTransform;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.system.Security;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.ui.Keyboard;
	import flash.utils.Timer;
	
	import hpPlayer.CommonPlayer;
	import hpPlayer.DebugInfoManager;
	import hpPlayer.Slider;
	
	public class multipleVideo extends CommonPlayer
	{
		private var tfTotalTime:TextField;			//总时间显示框
		private var tfCurrentTime:TextField;		//当前时间显示框
		private var isTimeDrag:Boolean = false;	//时间滑块是否被按下
		private var videoList:Array;				//视频列表
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function multipleVideo()
		{
			//设置舞台属性，事件侦听
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;
			stage.color = 0x1a1a1a;
			stage.addEventListener(Event.RESIZE, resizeHandler);
			stage.addEventListener(KeyboardEvent.KEY_UP, keyUpHandler); 
			stage.addEventListener(MouseEvent.MOUSE_MOVE, UIShow);
//			ExternalInterface.addCallback("inputURL", inputMultipleURL);
			this.videoList = new Array();
			this.videoList.push("e://000111.flv");
			this.videoList.push("e://MJ.mp4");
			stage.addEventListener(Event.RESIZE, resizeHandler);
			stage.removeEventListener(Event.MOUSE_LEAVE, UIHide);
			this.tfTotalTime = new TextField();
			var tfrmTotalTime:TextFormat = new TextFormat();
			tfrmTotalTime.color = 0xaeaeae;
			tfrmTotalTime.size = 12;
			this.tfTotalTime.defaultTextFormat = tfrmTotalTime;
			this.containerUI.addChild(this.tfTotalTime);
			this.tfCurrentTime = new TextField();
			var tfrmCurrentTime:TextFormat = new TextFormat();
			tfrmCurrentTime.color = 0xaeaeae;
			tfrmCurrentTime.size = 12;
			this.tfCurrentTime.defaultTextFormat = tfrmCurrentTime;
			this.tfCurrentTime.autoSize = TextFieldAutoSize.LEFT;
			this.containerUI.addChild(this.tfCurrentTime);
			this.tfCurrentTime.visible = false;
			this.tfTotalTime.visible = false;
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight - this.toolBarHieght;
			
//			this.inputMultipleURL(this.videoList);
		}
		
//		//输入地址
//		override protected function inputMultipleURL(videolist:Array):void
//		{	
//			DebugInfoManager.getInstance().log("[VideoPlayer]");
////			DebugInfoManager.getInstance().log("[-->JS-->]" + " [ServerURL] " + ServerURL + " [videoURL] " + StreamURL + " [LiveRoomURL] " + LiveRoomURL);
//			this.stopVideo();
//			this.videoList = videolist;
//			//设置URL
//			super.inputMultipleURL(this.videoList);
//		}
		
		private function timeDragFunction(event:MouseEvent):void
		{
			this.isTimeDrag = true;
			stage.removeEventListener(Event.ENTER_FRAME, enterFrameFunction);
			var prorect:Rectangle = new Rectangle(this.Timebuttom.x - this.TimedragSlider.width / 2, 
				this.Timebuttom.y + this.Timebuttom.height / 2 - this.TimedragSlider.height / 2, 
				this.Timebuttom.width, 
				0);  //播放进度滑块拖动范围			
			this.TimedragSlider.startDrag(false, prorect);
			stage.addEventListener(MouseEvent.MOUSE_UP, dragSeekTime);
			this.TimedragSlider.addEventListener(MouseEvent.MOUSE_UP, dragSeekTime);
		}	
		
		public function dragSeekTime(event:MouseEvent):void    //滑块控制播放进度函数
		{
			this.TimedragSlider.stopDrag();
			this.isTimeDrag = false;
			stage.removeEventListener(MouseEvent.MOUSE_UP, dragSeekTime);
			var ratio:Number = (this.TimedragSlider.x - this.Timebuttom.x) / this.Timebuttom.width;
			var current:Number = ratio * duration;			
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.seek(current);	
			}
			var timer:Timer = new Timer(100, 1);
			timer.addEventListener(TimerEvent.TIMER, addEnterFrameFunction);
			timer.start();
		}
		
		private function addEnterFrameFunction(event:TimerEvent):void
		{
			stage.addEventListener(Event.ENTER_FRAME, enterFrameFunction);
		}
		
		//全屏方法
		override protected function fullScreenFunction(event:MouseEvent):void
		{
			stage.displayState = StageDisplayState.FULL_SCREEN;
			this.btnFullScreen.visible = false;
			this.btnHalfScreen.visible = true;
		}
		
		//输入地址
//		override protected function inputURL(StreamURL:String, ServerURL:String = null, LiveRoomURL:String = null):void
//		{	
//			DebugInfoManager.getInstance().log("[VideoPlayer]");
//			DebugInfoManager.getInstance().log("[-->JS-->]" + " [ServerURL] " + ServerURL + " [videoURL] " + StreamURL + " [LiveRoomURL] " + LiveRoomURL);
//			this.stopVideo();
//			//设置URL
//			this.streamURL = StreamURL;
//			this.serverURL = ServerURL;
//			this.liveroomURL = LiveRoomURL;
//			super.inputURL(this.streamURL, this.serverURL, this.liveroomURL);
//		}
		
		override protected function draw():void
		{
			super.draw();
			setVideoPlayer();
		}
		
		override protected function playFunction(event:MouseEvent):void
		{
			this.isPlay = true;
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.resume();
			}
			this.btnPause.visible = true;
			this.btnPlay.visible = false;
		}
		
		override protected function pauseFunction(event:MouseEvent):void
		{
			this.isPlay = false;
			if(this.playerCore.netStream != null)
			{
				this.playerCore.netStream.pause();
			}
			hideLoading();
			this.btnPause.visible = false;
			this.btnPlay.visible = true;
			this.playerCore.cancelDelayToConnect();
		}
		
		private function setVideoPlayer():void
		{
			this.containerUIBackground.graphics.clear();
			this.containerUIBackground.graphics.beginFill(0x242424, 1);
			this.containerUIBackground.graphics.drawRect(0, stage.stageHeight - this.btnPlay.height, stage.stageWidth, this.btnPlay.height);
			this.containerUIBackground.graphics.endFill();
			this.containerUI.addChildAt(super.containerUIBackground, 0);
			this.TimeSlider.x = 0;
			this.TimeSlider.y = stage.stageHeight - this.btnPlay.height - this.Timebuttom.height;
			this.Timebuttom.width = stage.stageWidth - this.dragSlider.width;
			this.TimeSelected.width = this.Timebuttom.width * this.TimeSlider.getValue();
			this.TimedragSlider.x = this.TimeSlider.x + this.TimeSelected.width;
			this.TimeSlider.visible = true;
			this.TimedragSlider.addEventListener(MouseEvent.MOUSE_DOWN, timeDragFunction);
			this.Timebuttom.removeEventListener(MouseEvent.CLICK, this.TimeSlider.mouseClickFunction);
			this.TimeSelected.removeEventListener(MouseEvent.CLICK, this.TimeSlider.mouseClickFunction);
			this.slider.x = this.btnFullScreen.x - this.buttom.width - INTERVAL * 5 / 2;
			this.slider.y = stage.stageHeight - this.btnPlay.height / 2 - this.slider.height / 2;
			this.slider.visible = true;
			this.btnMute.x = this.slider.x - this.btnMute.width;
			this.btnMute.y = stage.stageHeight - this.btnMute.height;
			this.btnMute.visible = false;
			this.btnUnmute.x = this.slider.x - this.btnUnmute.width;
			this.btnUnmute.y = stage.stageHeight - this.btnUnmute.height;
			this.btnUnmute.visible = true;
			this.containerDebufInfo.width = stage.stageWidth;
			this.containerDebufInfo.height = stage.stageHeight;
			this.videoWidth = stage.stageWidth;
			this.videoHeight = stage.stageHeight - this.toolBarHieght;
			super.addVideo();
			this.addFunctions();
			this.btnBarrageOff.visible = false;
			this.btnBarrageOn.visible = false;
			this.btnLiveButtom.visible = false;
			this.btnLiveMain.visible = false;
			DebugInfoManager.getInstance().log("setVideoPlayer()");
			setTotalTime();
			this.TimeSelected.removeEventListener(MouseEvent.CLICK, this.TimeSlider.mouseClickFunction);
			this.Timebuttom.removeEventListener(MouseEvent.CLICK, this.TimeSlider.mouseClickFunction);
			this.Timebuttom.buttonMode = false;
			this.TimeSelected.buttonMode = false;
			this.tfTotalTime.visible = true;
			this.tfCurrentTime.visible = true;
		}
		override protected function redraw():void
		{
			super.redraw();
			setVideoPlayer();
		}
		
		override protected  function getInfoCallback(duration:Number):void
		{
			this.duration = duration;
			DebugInfoManager.getInstance().log("总时长：" + this.duration.toFixed(2));
			setTotalTime();
			stage.addEventListener(Event.ENTER_FRAME, enterFrameFunction);
		}
		
		override protected function resizeHandler(event:Event):void
		{	
			DebugInfoManager.getInstance().log("[RESIZE]");
			DebugInfoManager.getInstance().log("[设置video]videoWidth = " + this.videoWidth + " videoHeight = " + this.videoHeight);
			if(this.playerCore.video != null)
			{
				DebugInfoManager.getInstance().log("[实际playerCore]videoWidth = " + this.playerCore.video.width + " videoHeight = " + this.playerCore.video.height);
			}
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
		
		private function setTotalTime():void
		{
			DebugInfoManager.getInstance().log("setTotalTime()");
			if(this.duration == 0)
				return;
			if(Math.floor(this.duration%60) < 10)
			{
				this.tfTotalTime.text = "/   " + Math.floor(this.duration/60) + ":0" + Math.floor(this.duration%60);
			}
			else
			{
				this.tfTotalTime.text = "/   " + Math.floor(this.duration/60) + ":" + Math.floor(this.duration%60);
			}
			this.tfTotalTime.autoSize = TextFieldAutoSize.LEFT;
			if(this.btnRefresh != null)
			{
				this.tfTotalTime.x = 188;
				this.tfTotalTime.y = stage.stageHeight - this.tfTotalTime.height * 5 / 3;
			}
			this.tfTotalTime.selectable = false;
			this.tfCurrentTime.text ="0:00";
			this.tfCurrentTime.x = this.tfTotalTime.x - this.tfCurrentTime.width - 8;
			this.tfCurrentTime.y = this.tfTotalTime.y;
			this.containerUI.addChild(this.tfCurrentTime);
			this.containerUI.addChild(this.tfTotalTime);
		}
		
		private function enterFrameFunction(event:Event):void
		{
			//实时更新时间滑块位置
			if(this.playerCore.netStream != null && this.duration > 0 && this.playerCore.netStream.time > 0)
			{
				if(this.isTimeDrag == false)
				{
					if(this.TimedragSlider != null)
					{
						this.TimedragSlider.x = this.playerCore.netStream.time / duration * ( stage.stageWidth - this.TimedragSlider.width );
						this.TimeSelected.width = this.TimedragSlider.x - this.TimeSlider.x;
					}
				}
				else
				{
					if(this.TimedragSlider != null)
					{
						this.TimeSelected.width = this.TimedragSlider.x - this.TimeSlider.x;
					}
				}
			}
			//实时更新当前时间
			if(this.Timebuttom != null)
			{
				var frame:Number = this.TimedragSlider.x / this.Timebuttom.width;
				var current:Number = frame * this.duration;	
				if(Math.floor(current%60) < 10)
				{
					this.tfCurrentTime.text = Math.floor(current/60) + ":0" + Math.floor(current%60);
				}
				else
				{
					this.tfCurrentTime.text = Math.floor(current/60) + ":" + Math.floor(current%60);
				}
			}
			this.tfCurrentTime.x = this.tfTotalTime.x - this.tfCurrentTime.width - 8;
			this.tfCurrentTime.y = this.tfTotalTime.y;
			this.tfCurrentTime.selectable = false;
		}
	}
}