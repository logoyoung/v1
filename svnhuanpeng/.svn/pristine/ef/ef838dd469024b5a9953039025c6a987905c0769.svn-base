package hpPlayer
{
	import flash.display.Sprite;
	import flash.events.*;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLRequestHeader;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.net.navigateToURL;
	import flash.system.Security;
	import flash.utils.ByteArray;
	
	public class sendHttpRequest extends Sprite
	{
		
		private var urlLoader:URLLoader;				//URLLoader
		private var urlStr:String;						//php地址
		private var urlRequest:URLRequest;				//URLRequest
		private var urlVariables:URLVariables;			//URLVariables
		private var method:String;						//method
		private var resultStr:String;					//结果返回值URLRequestMethod.POST或者URLRequestMethod.GET
		private var callbackFunc:Function;				//回调函数
		
		flash.system.Security.allowDomain("*");
		flash.system.Security.allowInsecureDomain("*");
		
		public function sendHttpRequest() 
		{
			
		}
		
		public function init(url:String, callback:Function):void
		{
			this.callbackFunc = callback;
			this.urlLoader = new URLLoader();
			this.urlLoader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
			this.urlLoader.addEventListener(Event.COMPLETE, completeHandler);
			this.urlLoader.addEventListener(Event.COMPLETE, completeHandler);
			this.urlLoader.addEventListener(Event.OPEN, openHandler);
			this.urlLoader.addEventListener(ProgressEvent.PROGRESS, progressHandler);
			this.urlLoader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			this.urlLoader.addEventListener(HTTPStatusEvent.HTTP_STATUS, httpStatusHandler);
			this.urlRequest = new URLRequest("http://sdkbilibili.wscdns.com/?" + "up_rtmp=" + url);
			this.urlRequest.method = URLRequestMethod.GET;
			this.urlVariables = new URLVariables();
			this.urlRequest.data = this.urlVariables;
			for (var prop:String in this.urlVariables)
			{
				trace("Sent " + prop + " as: " + this.urlVariables[prop]);
			}
			this.urlLoader.load(this.urlRequest);
		}
		
		private function myUrlEncode(str:String, code:String):String
		{
			var stringresult:String = "";
			var byte:ByteArray = new ByteArray();
			byte.writeMultiByte(str, code);
			for (var i:int; i<byte.length; i++)
			{
				stringresult +=  escape(String.fromCharCode(byte[i]));
			}
			return stringresult;
		}
		
		private function completeHandler(event:Event):void
		{
			this.resultStr = this.urlLoader.data;
			this.callbackFunc.call(null, this.resultStr);
			trace("completeHandler: " + this.resultStr);
		}
		
		private function openHandler(event:Event):void 
		{
			trace("openHandler: " + event);
		}
		
		private function progressHandler(event:ProgressEvent):void 
		{
			trace("progressHandler loaded:" + event.bytesLoaded + " total: " + event.bytesTotal);
		}
		
		private function securityErrorHandler(event:SecurityErrorEvent):void 
		{
			trace("securityErrorHandler: " + event);
		}
		
		private function httpStatusHandler(event:HTTPStatusEvent):void 
		{
			trace("httpStatusHandler: " + event);
		}
		
		private function ioErrorHandler(event:IOErrorEvent):void 
		{
			trace("ioErrorHandler: " + event);
		}
	}
}