package hpPlayer
{
	import flash.display.Loader;
	import flash.display.LoaderInfo;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.SecurityDomain;
	
	//加载资源
	public class ResourceLoadManager extends Object
	{		
		private var list:Array;									//加载列表
		private var loader:Loader;									//加载器		
		private var index:int;										//索引值
		private var retryCount:int;								//加载失败重试次数
		private static const maxRetryCount:int = 10;				//最多重试次数
		private var onProgressFunc:Function;						//当前加载资源回调函数
		private var callbackSucceedFunc:Function;					//加载资源成功回调函数
		private var callbackFailedFunc:Function;					//加载资源失败回调函数
		private var callbackAllFunc:Function;						//资源全部加载回调函数
		
		//构造函数
		public function ResourceLoadManager()
		{
			
		}
		
		//调用者传入类名，返回类
		public static function getExportClass(className:String):Class
		{			
			if(ApplicationDomain.currentDomain != null)
			{
				if(ApplicationDomain.currentDomain.hasDefinition(className)) 
				{     
					var ExportClass:Class = Class (ApplicationDomain.currentDomain.getDefinition(className)); 
					return ExportClass;
				}
				else
				{
					return null;
				}			
			}
			else
			{
				return null;
			}
		}
		
		public static function getExportMovieClip(className:String):MovieClip
		{
			var getClass:Class = getExportClass(className);
			var mc:MovieClip;
			if (getClass != null)
			{
				mc = (new getClass()) as MovieClip;
			}
			return mc;
		}
		
		//外部调用，得到加载列表和回调函数
		public function loadWithArray(array:Array, onProgress:Function, callbackSucceed:Function, callbackFailed:Function, callbackAll:Function):void
		{
			cancelAll();
			onProgressFunc = onProgress;
			callbackSucceedFunc = callbackSucceed;
			callbackFailedFunc = callbackFailed; 
			callbackAllFunc = callbackAll;		
			this.index = 0;
			this.retryCount = 0;
			this.list = array;
			loadResource();			
		}
		
		//取消全部加载
		private function cancelAll():void
		{
			if(callbackAllFunc != null)
			{
				callbackAllFunc.call(null, true);
			}
			if(this.loader != null)
			{
				this.loader.close();
				this.loader = null;
			}
		}
		
		//加载函数
		private function loadResource():void
		{
			this.loader = new Loader();
//			onProgressFunc.call(null, this.list[index]);
			this.loader.load(new URLRequest(this.list[index]), new LoaderContext(false, ApplicationDomain.currentDomain));	
			this.loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadSucceed);	
			this.loader.contentLoaderInfo.addEventListener(ProgressEvent.PROGRESS, loadProgress);
			this.loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, loadFail);			
		}
		
		//加载成功调用
		private function loadSucceed(event:Event):void
		{
			this.loader = null;
			callbackSucceedFunc.call(null, this.list[index]);
			this.index++;
			if(this.index >= this.list.length)
			{
				loadFinish();
			}
			else
			{	
				loadResource();
			}
			this.retryCount = 0;
		}
		
		//加载进度
		private function loadProgress(event:ProgressEvent):void
		{
			var percentage:Number = index / this.list.length;
			var percentLoaded:Number = event.bytesLoaded / event.bytesTotal / this.list.length;
			percentage = percentage + percentLoaded;
//			var returnPercent:String = Math.round(percentage * 100).toFixed(0);
//			if(percentage == 0)
//			{
//				onProgressFunc.call(null, this.list[index], "0");
//				return;
//			}
			onProgressFunc.call(null, this.list[index], Math.round(percentage * 100));
		}
		
		//加载失败调用
		private function loadFail(event:Event):void
		{
			this.retryCount++;
			if(this.retryCount <= maxRetryCount)
			{
				loadResource();
			}
			else
			{
				this.loader = null;
				callbackFailedFunc.call(null, this.list[index]);
				this.index++;
				this.retryCount = 0;
				if(this.index >= this.list.length)
				{
					loadFinish();
				}
				else
				{
					loadResource();
				}
			}
		}
		
		//加载完成
		private function loadFinish():void
		{
			callbackAllFunc.call(null, false);
			this.loader = null;
			this.index = 0;
			this.retryCount = 0;
			this.list = null;
		}		
	}
}