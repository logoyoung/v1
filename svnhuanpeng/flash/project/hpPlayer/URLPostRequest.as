package hpPlayer
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.system.LoaderContext;
	
	public class URLPostRequest extends Object
	{
		private var urlLoader:URLLoader;				//URLLoader
		private var urlStr:String;						//php地址
		private var urlRequest:URLRequest;				//URLRequest
		private var urlVariables:URLVariables;			//URLVariables
		private var userID:int;						//用户ID
		private var method:String;						//method
		private var resultStr:String;					//结果返回值URLRequestMethod.POST或者URLRequestMethod.GET
		private var callbackFunc:Function;				//回调函数
		
		public function URLPostRequest()
		{

		}
		
		private function init():void
		{
			this.urlLoader = new URLLoader();
			this.urlLoader.addEventListener(IOErrorEvent.IO_ERROR, IOErrorFun);
			this.urlRequest = new URLRequest(this.urlStr);
			this.urlRequest.method = this.method;
			this.urlVariables = new URLVariables();
			this.urlVariables.luid = this.userID;
			this.urlRequest.data = this.urlVariables;
			this.urlLoader.addEventListener(Event.COMPLETE,LoadCompleted);
			this.urlLoader.load(this.urlRequest);
		}
		
		private function IOErrorFun(event:IOErrorEvent):void
		{
			
		}
		
		public function setup(urlString:String, method:String, uid:int, callback:Function):void
		{
			this.urlStr = urlString;
			this.method = method;
			this.userID = uid;
			this.callbackFunc = callback;
			init();
		}
			
		private function LoadCompleted(event:Event):void
		{
			this.resultStr = this.urlLoader.data;
			//将结果返回
			this.callbackFunc.call(null, this.resultStr);
		}
	}
}