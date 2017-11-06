package TEST
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.system.Security;
	import flash.text.Font;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	
	import hpPlayer.Recommend;
	import hpPlayer.ResourceLoadManager;
	import hpPlayer.VideoScreenDirection;

	[SWF(  backgroundColor = "0xffffff")]//, width = "900", height = "600")]
	public class RecommendTest extends Sprite
	{
		private var firstRecommend:Recommend;
		private var secondRecommend:Recommend;
		private var rlm:ResourceLoadManager;
		private var listTest:Array;	
		private var recommendmc:MovieClip;
		private var jumpmc:MovieClip;
		private var tfInfo:TextField;
		private var tfMore:TextField;
		private var moreLiveURL:String;
		private var fontSelectedList:Array;			//命中的字体
		private const fontCompareList:Array = new Array("Microsoft YaHei UI",	//预订字体
			"Microsoft YaHei", "STHeiti", "PingFang SC", "PingFang", "Helvetica");
		private var recommendFont:String;				//所用字体
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function RecommendTest()
		{
			stage.color = 0x000000;
			stage.addEventListener(Event.RESIZE, resizeHandler);
			checkFont();
			init();
		}
		
		//尺寸变化，更新组件大小及坐标
		protected function resizeHandler(event:Event):void
		{	
			if(this.firstRecommend != null)
			{
				this.firstRecommend.x = (stage.stageWidth - 576) / 2;
				this.firstRecommend.y = (stage.stageHeight - 240 - 40 - 32) / 2;
			}
			if(this.secondRecommend != null)
			{
				this.secondRecommend.x = this.firstRecommend.x + 280 + 16;
				this.secondRecommend.y = this.firstRecommend.y;
			}
			if(this.tfInfo != null)
			{
				this.tfInfo.x = this.firstRecommend.x + 168;
				this.tfInfo.y = this.firstRecommend.y - 20 - this.tfInfo.height;
			}
			if(this.tfMore != null)
			{
				this.tfMore.x = this.secondRecommend.x + 280 - this.tfMore.width;
				this.tfMore.y = this.firstRecommend.y + 240 + 20;
			}
		} 
		
		private function checkFont():void
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
					trace("字体： " + this.recommendFont);
					break;
				}
			}
		}
		
		private function init():void
		{
			this.listTest = new Array();
			this.listTest.push("LiveRecommend.swf");
			this.rlm = new ResourceLoadManager();
			this.rlm.loadWithArray(this.listTest, onProgress, callSuccess, callFailure, callAll);
		}
		
		//加载过程中
		private function onProgress(strResource:String):void
		{
			trace("  当前加载资源  " + strResource);
		}
		
		//加载成功
		private function callSuccess(strResource:String):void
		{
			trace("  " + strResource +"  加载成功！");
		}
		
		//加载失败
		private function callFailure(strResource:String):void
		{
			trace("  " + strResource +"  加载失败！");
		}
		
		//全部加载完成
		private function callAll(isCancelled:Boolean):void
		{
			if(isCancelled == true)
			{
				trace("  上一个加载被取消");
			}
			else
			{
				trace("  全部加载完成");
				setRecommend();
			}
		}
		
		private function setRecommend():void
		{
			this.recommendmc = ResourceLoadManager.getExportMovieClip("SingleRecommend");
			this.jumpmc = ResourceLoadManager.getExportMovieClip("btnJump");
			if(this.recommendmc != null && this.jumpmc != null)
			{
				trace("成功找到mc！");
				this.firstRecommend = new Recommend();
				this.firstRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
				this.firstRecommend.setup(VideoScreenDirection.HORIZONTAL, "MMM", 256, "王者荣耀", "./2.jpg", "http://www.baidu.com", this.recommendFont);
				this.addChild(this.firstRecommend);
				this.firstRecommend.x = (stage.stageWidth - 576) / 2;
				this.firstRecommend.y = (stage.stageHeight - 240 - 40 - 32) / 2;
				this.secondRecommend = new Recommend();
				this.secondRecommend.setupMC(duplicateDisplayObject(this.recommendmc) as MovieClip, duplicateDisplayObject(this.jumpmc) as MovieClip);
				this.secondRecommend.setup(VideoScreenDirection.VERTICAL, "KL", 56, "王者荣耀", "./3.jpg", "http://www.baidu.com", this.recommendFont);
				this.addChild(this.secondRecommend);
				this.secondRecommend.x = this.firstRecommend.x + 280 + 16;
				this.secondRecommend.y = this.firstRecommend.y;
				var tformat:TextFormat = new TextFormat();
				tformat.size = 16;
				tformat.color = 0xffffff;
				tformat.font = this.recommendFont;
				this.tfInfo = new TextField();
				this.tfInfo.defaultTextFormat = tformat;
				this.tfInfo.autoSize = TextFieldAutoSize.CENTER;
				this.tfInfo.text = "主播休息了，看看其他精彩直播吧";
				this.tfInfo.x = this.firstRecommend.x + 168;
				this.tfInfo.y = this.firstRecommend.y - 20 - this.tfInfo.height;
				this.tfInfo.selectable = false;
				this.tfInfo.alpha = 0.5;
				this.addChild(this.tfInfo);
				tformat.color = 0xff6c00;
				this.tfMore = new TextField();
				this.tfMore.defaultTextFormat = tformat;
				this.tfMore.autoSize = TextFieldAutoSize.CENTER;
				this.tfMore.text = "更多直播 >>";
				this.tfMore.x = this.secondRecommend.x + 280 - this.tfMore.width;
				this.tfMore.y = this.firstRecommend.y + 240 + 20;
				this.tfMore.selectable = false;
				this.tfMore.alpha = 1;
				this.addChild(this.tfMore);
				var sprite:Sprite = new Sprite();
				sprite.graphics.beginFill(0xffffff, 0);
				sprite.graphics.drawRect(this.tfMore.x, this.tfMore.y, this.tfMore.width, this.tfMore.height);
				sprite.graphics.endFill();
				sprite.addEventListener(MouseEvent.CLICK, toMoreLive);
				sprite.buttonMode = true;
				this.addChild(sprite);
			}
			else
			{
				trace("未找到mc！");
			}
			
		}
		
		private function toMoreLive(event:MouseEvent):void
		{
			this.moreLiveURL = "http://www.baidu.com";
			if(this.moreLiveURL != null)
			{
				var LiveRoomURLRequest:URLRequest = new URLRequest(this.moreLiveURL);
				navigateToURL(LiveRoomURLRequest, "_self");
			}
		}
		
		//深拷贝
		private static function duplicateDisplayObject(target:DisplayObject, autoAdd:Boolean = false):DisplayObject
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