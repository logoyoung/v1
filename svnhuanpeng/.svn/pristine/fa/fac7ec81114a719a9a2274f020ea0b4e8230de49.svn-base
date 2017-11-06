package TEST
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	
	import hpPlayer.ResourceLoadManager;
	import hpPlayer.giftBanner.GiftBannerItem;
	
	public class gbiTest extends Sprite
	{
		private var gbi:GiftBannerItem;
		private var container:Sprite;
		private var giftmc:MovieClip;
		private var bmc:MovieClip;
		private var resourceLoadManager:ResourceLoadManager;	//资源管理
		private var listResource:Array;						//资源列表
		
		public function gbiTest()
		{
			init();
		}
		
		private function init():void
		{
			this.resourceLoadManager = new ResourceLoadManager();
			this.listResource = new Array();
			this.listResource.push("./gift.swf");
			this.resourceLoadManager.loadWithArray(this.listResource, onProgress, callSuccess, callFailure, callAll);
//			this.giftmc = ResourceLoadManager.getExportMovieClip("mcGift");
//			this.bmc = ResourceLoadManager.getExportMovieClip("mcBanner");
//			this.gbi = new GiftBannerItem();
//			if(this.giftmc != null && this.bmc != null)
//			{
//				this.gbi.setup("飞船", this.giftmc, this.bmc);
//				this.gbi.setText("FFF", "MMM", "http://www.baidu.com");
//				this.gbi.startMoving(500, 10);
//			}
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
				trace("[Flash] " + "上一个加载被取消");
			}
			else
			{
				trace("[Flash] " + "全部加载完成");
				this.giftmc = ResourceLoadManager.getExportMovieClip("mcGift");
				this.bmc = ResourceLoadManager.getExportMovieClip("mcBanner");
				this.gbi = new GiftBannerItem();
				if(this.giftmc != null && this.bmc != null)
				{
					trace("mc均不为空");
					this.gbi.setup(this.giftmc, this.bmc, callFinish);
					this.gbi.setText(666, 909, "123456", "ASDFGHJKL", "http://www.baidu.com");
					this.gbi.startMoving(500, 10);
					stage.addChild(this.gbi);
				}
			}
		}
		
		//运行完成回调
		private function callFinish(UID:int, receiverID:int):void
		{
			trace("  " + UID + " " + receiverID +"  运行完成！");
		}
	}
}