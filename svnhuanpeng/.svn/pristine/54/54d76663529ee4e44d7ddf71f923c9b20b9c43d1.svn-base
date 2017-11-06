package hpPlayer
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Graphics;
	import flash.display.Loader;
	import flash.display.LoaderInfo;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.system.LoaderContext;
	import flash.text.Font;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	
	import hpPlayer.VideoScreenDirection;
	
	public class Recommend extends Sprite
	{
		private var recommendPic:Bitmap;				//图片
		private var recommendPicURL:String;			//图片链接
		public var recommendURL:String;					//跳转链接
		private const picWidth:Number = 260;			//推荐图片宽度
		private const picHeight:Number = 150;			//推荐图片高度
		private var img:Bitmap;						//图片
		public var sprite:Sprite;						//容器
		public var mcRecommend:MovieClip;				//影片剪辑
		public var mcJumpTo:MovieClip;					//跳转影片剪辑
		public var direction:String;					//横竖屏
		public var hostName:String = "主播";			//主播名
		public var audienceNumber:int = 0;				//观众数量
		public var gameName:String;						//游戏类型
		private var _bitmapdata:BitmapData;			//图片数据
		private var _rect:Sprite;						//裁剪区域    
		private var newImg:BitmapData;					//新图片
		private var tfLR:TextField;					//直播间名
		private var tfHost:TextField;					//主播名
		private var tfAudienceNumber:TextField;		//观众人数
		private var tfGameType:TextField;				//游戏类型
		private var recommendFont:String;				//所用字体
		private var shape:Shape;						//遮罩
		private var background:Shape;					//背景ffffff,0.2
		private var RecommendLoader:Loader;			//Loader对象
		private var picLoader:Loader;					//图片修改
		private var maskShape:Shape;					//遮罩
		private var g:Graphics;						//图形
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function Recommend()
		{
			this.background = new Shape();
			this.mouseEnabled = true;
			this.tfLR = new TextField();
			this.tfHost = new TextField();
			this.tfAudienceNumber = new TextField();
			this.tfGameType = new TextField();
			this.maskShape = new Shape();
			this.shape = new Shape();
			this.sprite = new Sprite();
		}
		
		public function setupMC(recommendmc:MovieClip, jumpmc:MovieClip):void
		{
//			if(this.mcRecommend == null)
//			{
				this.mcRecommend = recommendmc;
//			}
//			if(this.mcJumpTo == null)
//			{
				this.mcJumpTo = jumpmc;
//			}
		}
		
		public function setup(direction:String, hostName:String, audienceNumber:int, gameType:String, picURL:String, URL:String, fontStr:String):void
		{
			this.direction = direction;
			this.hostName = hostName;
			this.audienceNumber = audienceNumber;
			this.gameName = gameType;
			this.recommendPicURL = picURL;
			this.recommendURL = URL;
			this.shape = new Shape();
			this.recommendFont = fontStr;
			load();
		}
		
		public function remove():void
		{
			if(this.sprite != null && this.contains(this.sprite))
			{
				this.sprite.visible = false;
			}
			if(this.tfHost != null)
			{
				this.tfHost.text = "";
			}
			if(this.tfGameType != null)
			{
				this.tfGameType.text = "";
			}
			if(this.tfAudienceNumber != null)
			{
				this.tfAudienceNumber.text = "";
			}
			if(this.tfLR != null)
			{
				this.tfLR.text = "";
			}
		}
		
		private function load():void
		{
			this.background.graphics.clear();
			this.background.graphics.beginFill(0xffffff, 0.2);
			this.background.graphics.drawRect(0, 0, 280, 240);
			this.background.graphics.endFill();
			this.addChild(this.background);
			var RecommendURLRequest:URLRequest = new URLRequest();
			RecommendURLRequest.url = this.recommendPicURL;
			this.RecommendLoader = new Loader();
			this.RecommendLoader.load(RecommendURLRequest);//, new LoaderContext(true));
			this.RecommendLoader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadFinished);
		}
		
		//加载成功，设置界面
		private function loadFinished(event:Event):void
		{
//			this.background.graphics.clear();
//			this.background.graphics.beginFill(0xffffff, 0.2);
//			this.background.graphics.drawRect(0, 0, 280, 240);
//			this.background.graphics.endFill();
//			this.addChild(this.background);
			if(this.mcJumpTo != null)
			{
				this.mcRecommend.x = 0;
				this.mcRecommend.y = 0;
				this.addChild(this.mcRecommend);
			}
			if(this.picLoader == null)
			{
				this.picLoader = LoaderInfo(event.target).loader;
			}
			this.addChild(this.picLoader);
			if(this.direction == VideoScreenDirection.HORIZONTAL)
			{
				this.picLoader.width = picWidth;
				this.picLoader.height = picHeight;
				this.picLoader.x = 10;
				this.picLoader.y = 10;
			}
			if(this.direction == VideoScreenDirection.VERTICAL)
			{
				var k:Number = picWidth / this.picLoader.width;
				this.picLoader.scaleX = k;
				this.picLoader.scaleY = k;
				this.picLoader.x = this.x + 10;
				this.picLoader.y = this.y + 10;
				this.g = this.maskShape.graphics;
				this.g.beginFill(0x000000, 1);
				this.g.drawRect(10, 10, picWidth, picHeight);
				this.g.endFill();
				this.picLoader.mask = this.maskShape;
			}
			setData();
		}
		
		public function setData():void
		{
			this.shape.graphics.clear();
			this.shape.graphics.beginFill(0x000000, 0.5);
			this.shape.graphics.drawRect(10, 10, picWidth, picHeight);
			this.shape.graphics.endFill();
			this.addChild(this.shape);
			this.shape.visible = false;
			if(this.mcJumpTo != null)
			{
				this.mcJumpTo.x = 115;
				this.mcJumpTo.y = 60;
				this.addChild(this.mcJumpTo);
				this.mcJumpTo.visible = false;
			}
			if(this.sprite == null)
			{
				this.sprite = new Sprite();
			}
			this.sprite.graphics.clear();
			this.sprite.graphics.beginFill(0xffffff, 0);
			this.sprite.graphics.drawRect(10, 10, picWidth, picHeight);
			this.sprite.graphics.endFill();
			this.addChild(this.sprite);
			this.sprite.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.sprite.addEventListener(MouseEvent.DOUBLE_CLICK, mouseClickFunction);
			this.sprite.addEventListener(MouseEvent.MOUSE_DOWN, mouseClickFunction);
			this.sprite.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveFunction);
			this.sprite.addEventListener(MouseEvent.MOUSE_OVER, mouseMoveFunction);
			this.sprite.addEventListener(MouseEvent.ROLL_OVER, mouseMoveFunction);
			this.sprite.addEventListener(MouseEvent.MOUSE_OUT, mouseOutFunction);
			this.sprite.addEventListener(MouseEvent.MOUSE_UP, mouseOutFunction);
			this.sprite.buttonMode = true;
			var tformat:TextFormat = new TextFormat();
			tformat.size = 14;
			tformat.color = 0xffffff;
			tformat.font = this.recommendFont;
			this.tfLR.defaultTextFormat = tformat;
			this.tfLR.text = this.hostName + "的直播间";
			this.tfLR.autoSize = TextFieldAutoSize.CENTER;
			this.tfLR.x = 10;
			this.tfLR.y = 172;
			this.tfLR.selectable = false;
			this.tfLR.alpha = 0.5;
			this.addChild(this.tfLR);
			tformat.size = 12;
			this.tfHost.defaultTextFormat = tformat;
			if(this.hostName != null && this.hostName.length >= 7)
			{
				this.tfHost.text = this.hostName.slice(0, 6) + "…";
			}
			if(this.hostName != null && this.hostName.length < 7)
			{
				this.tfHost.text = this.hostName;
			}
			this.tfHost.autoSize = TextFieldAutoSize.CENTER;
			this.tfHost.x = 36;
			this.tfHost.y = 202;
			this.tfHost.selectable = false;
			this.tfHost.alpha = 0.5;
			this.addChild(this.tfHost);
			this.tfAudienceNumber.defaultTextFormat = tformat;
			this.tfAudienceNumber.text = String(this.audienceNumber);
			this.tfAudienceNumber.width = this.tfAudienceNumber.textWidth + 10;
			this.tfAudienceNumber.height = this.tfAudienceNumber.textHeight + 10;
			this.tfAudienceNumber.x = 156;
			this.tfAudienceNumber.y = 202;
			this.tfAudienceNumber.selectable = false;
			this.tfAudienceNumber.alpha = 0.5;
			this.addChild(this.tfAudienceNumber);
			tformat.color = 0xff6c00;
			this.tfGameType.defaultTextFormat = tformat;
			if(this.gameName != null && this.gameName.length > 6)
			{
				this.tfGameType.text = this.gameName.slice(0 ,5) + "…";
			}
			if(this.gameName != null && this.gameName.length <= 6)
			{
				this.tfGameType.text = this.gameName;
			}
			this.tfGameType.autoSize = TextFieldAutoSize.CENTER;
			this.tfGameType.x = 270 - this.tfGameType.width;
			this.tfGameType.y = 202;
			this.tfGameType.selectable = false;
			this.tfGameType.alpha = 1;
			this.addChild(this.tfGameType);
			this.addEventListener(MouseEvent.CLICK, toRecommendLiveRoom);	
		}
		
		private function toRecommendLiveRoom(event:MouseEvent):void
		{
			if(this.recommendURL != null)
			{
				var LiveRoomURLRequest:URLRequest = new URLRequest(this.recommendURL);
				navigateToURL(LiveRoomURLRequest, "_self");
			}
		}
		
		private function  mouseClickFunction(event:MouseEvent):void
		{
			if(this.mcJumpTo != null)
			{
				this.mcJumpTo.visible = true;
			}
			if(this.shape != null)
			{
				this.shape.visible = true;
			}
		}
		
		private function  mouseMoveFunction(event:MouseEvent):void
		{
			if(this.mcJumpTo != null)
			{
				this.mcJumpTo.visible = true;
			}
			if(this.shape != null)
			{
				this.shape.visible = true;
			}
		}
		
		private function  mouseOutFunction(event:MouseEvent):void
		{
			if(this.mcJumpTo != null)
			{
				this.mcJumpTo.visible = false;
			}
			if(this.shape != null)
			{
				this.shape.visible = false;
			}
		}
	}
}