package TEST
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestHeader;
	import flash.net.URLRequestMethod;
	import flash.net.URLStream;
	import flash.net.URLVariables;
	import flash.utils.ByteArray;
	
	import hpPlayer.postRequest;
	
	public class PostRequestTest extends Sprite
	{
		private var urlvar:URLVariables;
		private var urlstring:String;
		private var pr:postRequest;
		public function PostRequestTest()
		{
			this.pr = new postRequest();
			this.pr.setup("http://dev.huanpeng.com/main/api/other/flashRecommend.php", URLRequestMethod.POST, 336, this.getData);
		}
		//e://flashRecommend.php
		//http://dev.huanpeng.com/main/api/other/flashRecommend.php
		private function getData(result:String):void
		{
			trace("data = " + result);
//			result = '{"status":1,"content":{"list":[{"firstPicURL":"http:\/\/dev-img.huanpeng.com\/b\/4\/b452fa8609f1bbe12006916a2197e0d9.jpg",
//			"firstLiveRoomURL":"http:\/\/dev.huanpeng.com\/main\/room.php?luid=370","firstHostName":"\u4e48\u4e48\u54d2","firstAudienceNumber":22,
//			"firstGameName":"\u7403\u7403\u5927\u4f5c\u6218","firstScreenDirection":"horizontal"},
//			{"secondPicURL":"http:\/\/dev-img.huanpeng.com\/b\/4\/b452fa8609f1bbe12006916a2197e0d9.jpg",
//			"secondLiveRoomURL":"http:\/\/dev.huanpeng.com\/main\/room.php?luid=90","secondHostName":"\u8682\u8681\u7259\u9ed1",
//			"secondAudienceNumber":33,"secondGameName":"\u738b\u8005\u8363\u8000","secondScreenDirection":"vertical"}],"
//			moreLive":"http:\/\/dev.huanpeng.com\/main\/LiveHall.php"}}';
			var obj:Object = JSON.parse(result);
			trace("解析JSON 1  status = " + obj.status);
			trace("解析JSON 2  content moreLive = " + obj["content"]["moreLive"]);
			trace("解析JSON 3  content list = " + obj["content"]["list"]);
			trace("解析JSON 4  content list firstPicURL = " + obj["content"]["list"][0]["firstPicURL"]);
			trace("解析JSON 4  content list firstLiveRoomURL = " + obj["content"]["list"][0]["firstLiveRoomURL"]);
			trace("解析JSON 4  content list firstHostName = " + obj["content"]["list"][0]["firstHostName"]);
			trace("解析JSON 4  content list firstAudienceNumber = " + obj["content"]["list"][0]["firstAudienceNumber"]);
			trace("解析JSON 4  content list firstGameName = " + obj["content"]["list"][0]["firstGameName"]);
			trace("解析JSON 4  content list firstScreenDirection = " + obj["content"]["list"][0]["firstScreenDirection"]);
			var content1:Object = obj["content"];
			trace("content1 = " + content1);
			var obj2:Object = content1.list;
			trace("obj2 = " + obj2[1]["secondPicURL"]);
		}
	}
}