package TEST 
{
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	
	import hpPlayer.DebugInfoManager;
	import hpPlayer.ResourceLoadManager;
	import hpPlayer.giftBanner.GiftBannerManager;
	[SWF(  backgroundColor = "0x000000", width = "900", height = "350")]
	public class gMTest extends Sprite
	{
		private var container:Sprite;
		private var giftmc:MovieClip;
		private var bmc:MovieClip;
		private var resourceLoadManager:ResourceLoadManager;	//资源管理
		private var listResource:Array;						//资源列表
		private var add1:Sprite;
		private var add2:Sprite;
		private var add3:Sprite;
		private var add4:Sprite;
		private var add5:Sprite;
		
		public function gMTest()
		{
			init();
		}
		
		private function init():void
		{
			GiftBannerManager.getInstance().init();
			stage.color = 0xa1a1a1;
			this.container = new Sprite();
			var background:Shape = new Shape();
			background.graphics.beginFill(0x771177, 0);
			background.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			background.graphics.endFill();
			this.container.addChild(background);
			stage.addChild(this.container);
			this.resourceLoadManager = new ResourceLoadManager();
			this.listResource = new Array();
			this.listResource.push("./gift.swf");
			this.resourceLoadManager.loadWithArray(this.listResource, onProgress, callSuccess, callFailure, callAll);
			this.add1 = new Sprite();
			this.add1.graphics.beginFill(0x990000);
			this.add1.graphics.drawCircle(30, 330, 10);
			this.add1.graphics.endFill();
			this.container.addChild(add1);
			this.add2 = new Sprite();
			this.add2.graphics.beginFill(0x009900);
			this.add2.graphics.drawCircle(60, 330, 10);
			this.add2.graphics.endFill();
			this.container.addChild(add2);
			this.add3 = new Sprite();
			this.add3.graphics.beginFill(0x000099);
			this.add3.graphics.drawCircle(90, 330, 10);
			this.add3.graphics.endFill();
			this.container.addChild(add3);
			this.add4 = new Sprite();
			this.add4.graphics.beginFill(0x660000);
			this.add4.graphics.drawCircle(120, 330, 10);
			this.add4.graphics.endFill();
			this.container.addChild(add4);
			this.add5 = new Sprite();
			this.add5.graphics.beginFill(0x676767);
			this.add5.graphics.drawCircle(150, 330, 10);
			this.add5.graphics.endFill();
//			this.container.addChild(add5);
			this.add1.addEventListener(MouseEvent.CLICK, add1ClickFunction);
			this.add2.addEventListener(MouseEvent.CLICK, add2ClickFunction);
			this.add3.addEventListener(MouseEvent.CLICK, add3ClickFunction);
			this.add4.addEventListener(MouseEvent.CLICK, add4ClickFunction);
			this.add5.addEventListener(MouseEvent.CLICK, add4ClickFunction);
		}
		
		private function add1ClickFunction(event:MouseEvent):void
		{
//			trace("【add1】");
			for(var i:int = 0; i < 15; i++)
			{
				GiftBannerManager.getInstance().addGiftBannerMessage(123, 444, "sender1", "receiver1", "http://www.baidu.com");
				GiftBannerManager.getInstance().addGiftBannerMessage(456, 777, "sender2", "receiver2", "http://www.baidu.com");
				GiftBannerManager.getInstance().addGiftBannerMessage(223, 444, "sender3", "receiver3", "http://www.baidu.com");
				GiftBannerManager.getInstance().addGiftBannerMessage(283, 888, "sender4", "receiver4", "http://www.baidu.com");
			}
		}
		private function add2ClickFunction(event:MouseEvent):void
		{
//			trace("【add2】");
			GiftBannerManager.getInstance().addGiftBannerMessage(456, 777, "sender2", "receiver2", "http://www.baidu.com");
		}
		private function add3ClickFunction(event:MouseEvent):void
		{
//			trace("【add3】");
			GiftBannerManager.getInstance().addGiftBannerMessage(223, 444, "sender3", "receiver3", "http://www.baidu.com");
		}
		private function add4ClickFunction(event:MouseEvent):void
		{
//			trace("【add4】");
			GiftBannerManager.getInstance().addGiftBannerMessage(666, 777, "sender4", "receiver4", "http://www.baidu.com");
		}
		//加载过程中
		private function onProgress(strResource:String):void
		{
			trace("[Flash] " + "当前加载资源  " + strResource);
		}
		
		//加载成功
		private function callSuccess(strResource:String):void
		{
			trace("[Flash] " + strResource + "  加载成功");
		}
		
		//加载失败
		private function callFailure(strResource:String):void
		{
			trace("[Flash] " + strResource + "  加载失败");
		}
		
		//全部加载完成
		private function callAll(isCancelled:Boolean):void
		{
			if(isCancelled == true)
			{
//				trace("[Flash] " + "上一个加载被取消");
			}
			else
			{
//				trace("[Flash] " + "全部加载完成");
				this.giftmc = ResourceLoadManager.getExportMovieClip("mcGift");
				this.bmc = ResourceLoadManager.getExportMovieClip("mcBanner");
				if(this.giftmc != null && this.bmc != null)
				{
					testGBIM();
				}
			}
		}
		
		private function testGBIM():void
		{
			GiftBannerManager.getInstance().setupWithContainer(this.container, stage.stageWidth, stage.stageHeight, this.giftmc, this.bmc);
//			GiftBannerManager.getInstance().addGiftBannerMessage(123, 444, "sender1", "receiver1", "http://www.baidu.com");
//			GiftBannerManager.getInstance().addGiftBannerMessage(234, 555, "sender2", "receiver2", "http://www.baidu.com");
//			GiftBannerManager.getInstance().addGiftBannerMessage(345, 666, "sender3", "receiver3", "http://www.baidu.com");
			
		}
		
		private function GBMCallback(info:String):void
		{
			trace("GBM返回信息：" + info);
		}
	}
}