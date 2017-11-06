package TEST
{
	
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.net.URLRequest;
	import flash.system.Security;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import hpPlayer.ResourceLoadManager;
	
	public class ResourceLoadManagerTestCase extends Sprite
	{
		private var rlm:ResourceLoadManager;
		private var listTest:Array = new Array();	
		private var location:int = 1;
		private var intervalId:int;
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		//构造函数
		public function ResourceLoadManagerTestCase()
		{
			listTest.push("loading2.swf");
			listTest.push("wait.swf");
			listTest.push("loadingori.swf");
			rlm = new ResourceLoadManager();
			rlm.loadWithArray(listTest, onProgress, callSuccess, callFailure, callAll);
			intervalId = setTimeout(reset, 30);
		}
		
		//重置列表
		private function reset():void
		{
			if(intervalId != 0)
			{   
				clearTimeout(intervalId);   
			} 
			location = 1;
			listTest.splice(0,listTest.length);
			listTest.push("loading.swf");
			listTest.push("wait.swf");
			rlm.loadWithArray(listTest, onProgress, callSuccess, callFailure, callAll);
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
				var getClass:Class;
				var mc:MovieClip;
				getClass = ResourceLoadManager.getExportClass("wait");
				if (getClass != null)
				{
					mc = (new getClass()) as MovieClip;
					mc.x = 50 * location;
					mc.y = 50 * location;
					stage.addChild(mc);
				}
				getClass = ResourceLoadManager.getExportClass("loading");
				if (getClass != null)
				{
					mc = (new getClass()) as MovieClip;
					mc.x = 100 * location;
					mc.y = 100 * location;
					stage.addChild(mc);
				}
				getClass = ResourceLoadManager.getExportClass("load");
				if (getClass != null)
				{
					mc = (new getClass()) as MovieClip;
					mc.x = 150 * location;
					mc.y = 150 * location;
					stage.addChild(mc);
				}
				var getMC:MovieClip;
				getMC = ResourceLoadManager.getExportMovieClip("loading");
				if(getMC != null)
				{
					getMC.x = 80 * location;
					getMC.y = 80 * location;
					stage.addChild(getMC);
					getMC.stop();
				}
			}
		}
	}
}