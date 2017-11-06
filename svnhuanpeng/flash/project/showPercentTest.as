package
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.net.URLRequest;
	import flash.system.Security;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	import hpPlayer.ResourceLoadManager;
	import hpPlayer.showPercent;
	
	public class showPercentTest extends Sprite
	{
		private var percentLoaded:showPercent;
		
		private var rlm:ResourceLoadManager;
		private var listTest:Array;	
		private var location:int = 1;
		private var intervalId:int;
		private var count:Number;
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function showPercentTest():void
		{
			init();
		}
		
		private function init():void
		{
			this.listTest = new Array();
			listTest.push("percentLoading.swf");
			this.rlm = new ResourceLoadManager();
			this.rlm.loadWithArray(this.listTest, onProgress, callSuccess, callFailure, callAll);
			this.percentLoaded = new showPercent();
			this.addChild(this.percentLoaded);
			this.count = 0;
		}
		
		//加载过程中
		private function onProgress(strResource:String, value:Number):void
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
				var mcBackground:MovieClip;
				var mcPercent:MovieClip;
				var mcSpot:MovieClip;
				getClass = ResourceLoadManager.getExportClass("percentBkg");
				if (getClass != null)
				{
					mcBackground = (new getClass()) as MovieClip;
				}
				getClass = ResourceLoadManager.getExportClass("percentLoaded");
				if (getClass != null)
				{
					mcPercent = (new getClass()) as MovieClip;
				}
				this.percentLoaded.setup(mcBackground, mcPercent);
				this.intervalId = setTimeout(setValue, 500);
			}
		}
		
		public function setValue():void
		{
			if(this.count < 100)
			{
				this.count = this.count + 3.5;
				this.percentLoaded.setValue(this.count);
				this.intervalId = setTimeout(setValue, 50);
			}
			else
			{
				clearTimeout(this.intervalId);
			}
		}
	}
}