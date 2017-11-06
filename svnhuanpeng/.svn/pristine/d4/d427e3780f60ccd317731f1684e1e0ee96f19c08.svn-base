//CoreVideo测试
package TEST 
{
	import hpPlayer.VideoScreenDirection;
	
	import flash.display.Sprite;
	import flash.events.AsyncErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import hpPlayer.CoreVideo;
	
	[SWF(height=500,width=400)] 
	
	public class CoreVideoTestCase extends Sprite
	{
		private var video:CoreVideo;
		private var nc:NetConnection;
		private var ns:NetStream;
		private var customClient:Object;
		
		//构造函数
		public function CoreVideoTestCase()
		{
			stage.color = 0x676767;
			video = new CoreVideo();
			nc = new NetConnection();
			customClient = new Object();
			nc.addEventListener(NetStatusEvent.NET_STATUS,netStatusHandler);
			nc.addEventListener(AsyncErrorEvent.ASYNC_ERROR, asyncErrorHandler); 
			nc.connect("rtmp://live.hkstv.hk.lxdns.com/live/");
			nc.client = customClient;
			video.drawWithDirection(VideoScreenDirection.HORIZONTAL, stage.stageWidth, stage.stageHeight);
		}
		
		//连接NetScream
		private function connectNS():void
		{
			ns = new NetStream(nc);						
			ns.client = customClient;			
			ns.bufferTime = 2;			
			ns.bufferTimeMax=6;
			video.attachNetStream(ns);
			addChild(video);	
			video.visible=true;
			ns.play("hks");	
		}	
		
		//状态处理
		private function netStatusHandler(event:NetStatusEvent):void 
		{
			switch (event.info.code) 
			{
				case "NetConnection.Connect.Success":
					trace("NetConnection.Connect.Success");
					connectNS();
					break;
				case "NetConnection.Connect.Failed":
					trace("NetConnection.Connect.Failed");
					break;
				case "NetConnection.Connect.Rejected":
					trace("NetConnection.Connect.Rejected");
					break;
				case "NetConnection.Connect.InvalidApp":
					trace("NetConnection.Connect.InvalidApp");
					break;
				case "NetStream.Play.StreamNotFound":
					trace("NetStream.Play.StreamNotFound");
					break;
				case "NetStream.Buffer.Empty" :	
					trace("NetStream.Buffer.Empty");
					break;
				case "NetStream.Buffer.Full" :	
					trace("NetStream.Buffer.Full");
					break;
				case "NetStream.Buffer.Flush" :
					trace("NetStream.Buffer.Flush");
					break;
				case "NetStream.Publish.Start" :
					trace("NetStream.Publish.Start");
					break;
				case "NetStream.Publish.BadName" :
					trace("NetStream.Publish.BadName");
					break;
				case "NetStream.Publish.Idle" :
					trace("NetStream.Publish.Idle");
					break;
				case "NetStream.Unpublish.Success" :
					trace("NetStream.Unpublish.Success");
					break;
				case "NetStream.Play.Stop" :
					trace("NetStream.Play.Stop");
					break;
				case "NetStream.Play.Start" :
					trace("NetStream.Play.Start");
					break;
				case "NetStream.Play.Failed" :
					trace("play failed");
					break;
				case "NetStream.Play.Reset" :
					trace("NetStream.Play.Reset");
					break;
				case "NetStream.Pause.Notify" :
					trace("NetStream.Pause.Notify");
					break;
				case "NetStream.Unpause.Notify" :
					trace("NetStream.Unpause.Notify");
					break;
				case "NetStream.Seek.InvalidTime" :	
					trace("NetStream.Seek.InvalidTime");
					break;
				case "NetConnection.Connect.Closed" :
					trace("NetConnection.Connect.Closed");
					break;
				case "NetConnection.Connect.NetworkChange" :
					trace("NetConnection.Connect.NetworkChange");
				case "NetStream.Play.FileStructureInvalid" :
					trace("NetStream.Play.FileStructureInvalid");
				case "NetStream.Play.NoSupportedTrackFound" :
					trace("NetStream.Play.NoSupportedTrackFound");
					break;
				case "NetStream.Play.UnpublishNotify":
					trace("NetStream.Play.UnpublishNotify");
					break;
			}		
		}	
		
		//错误处理  
		private function asyncErrorHandler(event:AsyncErrorEvent):void
		{
			trace("AsyncError", event.text);
		}	
	}
}