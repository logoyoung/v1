package hpPlayer.giftBanner
{
	import com.gskinner.motion.GTween;
	import com.gskinner.motion.GTweenTimeline;
	import com.gskinner.motion.easing.*;
	
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.text.TextFormatAlign;
	
	import hpPlayer.giftBanner.GiftBannerItemStatus;
	
	public class GiftBannerItem extends Sprite
	{
		public var UID:int;									//发送方ID
		public var receiverID:int;							//接收方ID
		public var sender:String;							//发送方
		public var receiver:String;							//接收方
		private var liveroomURL:String;					//跳转链接
		private var giftMovieClip:MovieClip;				//礼物影片剪辑
		private var bannerMovieClip:MovieClip;				//横幅
		public var tfGift:TextField;						//礼物横幅文本对象
		public var tfType:TextField;						//礼物横幅文本对象
		private var gtween:GTween;							//动画
		private var gtweentimeline:GTweenTimeline;			//动画时间轴
		private const offset:Number = 80;					//横幅对象间隔
		private const distancePerFrame:Number = 80;		//速度
		public var status:String;							//状态
		private var callbackFinishFunc:Function;			//运行完成回调函数
		private var tfNumber:TextField;					//礼物数量文本对象
		private var _sprite:Sprite;						//手型
		
		//构造函数
		public function GiftBannerItem()
		{
			this.addEventListener(MouseEvent.CLICK, toLiveRoomFunction);
			this.status = GiftBannerItemStatus.READY;
			this.buttonMode = true;
		}
		
		//设置
		public function setup(giftMC:MovieClip, bannerMovieClip:MovieClip, callbackFinish:Function):void
		{
			this.giftMovieClip = giftMC;
			this.bannerMovieClip = bannerMovieClip;
			this.callbackFinishFunc = callbackFinish;
			init();
		}
		
		//初始化
		private function init():void
		{
			this.giftMovieClip.x = 0;
			this.giftMovieClip.y = 0;
			this.addChild(this.giftMovieClip);
			this.bannerMovieClip.x = this.giftMovieClip.x + this.giftMovieClip.width - 11;
			this.bannerMovieClip.y = this.giftMovieClip.y + ( this.giftMovieClip.height - this.bannerMovieClip.height ) / 2;
			this.giftMovieClip.visible = true;
			this.bannerMovieClip.visible = true;
			this.addChildAt(this.bannerMovieClip, 0);
		}
		
		//开始移动
		public function startMoving(locationX:Number, locationY:Number):void
		{
			this.x = locationX;
			this.y = locationY;
			this.gtween = new GTween(this, (this.x + this.width) / distancePerFrame, { x:- this.giftMovieClip.width - this.bannerMovieClip.width - offset }, { autoPlay:false, ease:Linear.easeNone }, { MotionBlurEnabled:false });
			this.gtweentimeline = new GTweenTimeline();
			this.gtweentimeline.addTween(0, this.gtween);
			this.gtweentimeline.calculateDuration();
			this.gtween.onComplete = gtweenComplete;
			this.status = GiftBannerItemStatus.MOVING;
		}
		
		//运行完成
		private function gtweenComplete(tween:GTween):void
		{
			this.gtween = null;
			this.gtweentimeline = null;
			this.status = GiftBannerItemStatus.READY;
			this.UID = 0;//undefined;
			this.receiverID = 0;//undefined;
			callbackFinishFunc.call(null, this.UID, this.receiverID);
		}
		
		public function setText(UID:int, receiverID:int, sender:String, receiver:String, liveroomURL:String):void
		{
			this.UID = UID;
			this.receiverID = receiverID;
			this.sender = sender;
			this.receiver = receiver;
			this.liveroomURL = liveroomURL;
			this.tfGift = new TextField();
			this.tfGift.selectable = false;
			this.tfGift.autoSize = TextFieldAutoSize.CENTER;
			this.tfGift.htmlText = '<FONT FACE="Microsoft YaHei UI" SIZE="16" COLOR="#ffec4d"><b>' + this.sender + " " + '</b></FONT>' 
								 + '<FONT FACE="Microsoft YaHei UI" SIZE="16" COLOR="#ffffff"><b>' + "送给" + '</b></FONT>'
								 + '<FONT FACE="Microsoft YaHei UI" SIZE="16" COLOR="#ffec4d"><b>' + " " + this.receiver + " " + '</b></FONT>';
			this.tfGift.x = this.giftMovieClip.x + this.giftMovieClip.width;
			this.tfGift.y = this.bannerMovieClip.y + this.bannerMovieClip.height / 2 - this.tfGift.height / 2;
			this.addChild(this.tfGift);
			this.tfNumber = new TextField();
			this.tfNumber.selectable = false;
			this.tfNumber.autoSize = TextFieldAutoSize.CENTER;
			this.tfNumber.htmlText = '<FONT FACE="Microsoft YaHei UI" SIZE="34" COLOR="#ffec4d"><b>' + "1" + '</b></FONT>' 
			this.tfNumber.x = this.tfGift.x + this.tfGift.width;
			this.tfNumber.y = 20;
			this.addChild(this.tfNumber);
			this.tfType = new TextField();
			this.tfType.selectable = false;
			this.tfType.autoSize = TextFieldAutoSize.CENTER;
			this.tfType.htmlText = '<FONT FACE="Microsoft YaHei UI" SIZE="16" COLOR="#ffffff"><b>' + " 艘飞船，快来抢宝箱啊！" + '</b></FONT>';
			this.tfType.x = this.tfNumber.x + this.tfNumber.width;
			this.tfType.y = this.tfGift.y;
			this.addChild(this.tfType);
			this.bannerMovieClip.width = this.tfGift.width + this.tfNumber.width + this.tfType.width + 50;
			var shape:Shape = new Shape();
			shape.graphics.beginFill(0xffffff, 0);
			shape.graphics.drawRect(0, 0, this.width, this.height);
			shape.graphics.endFill();
			this._sprite = new Sprite();
			this._sprite.addChild(shape);
			this._sprite.buttonMode = true;
			this.addChild(this._sprite);
		}
		
		//跳转链接
		private function toLiveRoomFunction(event:MouseEvent):void
		{
			if(this.liveroomURL != null)
			{
				var LiveRoomURLRequest:URLRequest = new URLRequest(this.liveroomURL);
				navigateToURL(LiveRoomURLRequest, "_blank");
			}
		}
		
		//重置
		public function reset():void
		{
			if(this.gtweentimeline != null)
			{
				this.gtweentimeline.end();
			}
		}
	}
}