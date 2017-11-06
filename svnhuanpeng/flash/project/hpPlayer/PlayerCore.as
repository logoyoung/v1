package hpPlayer
{
	import flash.display.Sprite;
	import flash.events.AsyncErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.media.SoundTransform;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	import hpPlayer.CoreVideo;
	import hpPlayer.PlayerSituation;
	import hpPlayer.VideoScreenDirection;
	
	public class PlayerCore extends Object
	{
		public var netConnection:NetConnection;  			//NetConnection对象
		public var netStream:NetStream;						//NS对象
		public var customClient:Object;						//包含各个元数据项的某个/些属性的对象
		private var videoURL:String;						//流地址
		private var serverURL:String;						//服务器地址
		public var video:CoreVideo;							//CoreVideo对象
		private var netStatus:String;						//网络状况
		private var connectIntervalId:int;					//连接时间间隔ID
		private var callbackFunction:Function;				//回调函数
		private var getInfoFunction:Function;				//回调函数
		private var containerWidth:Number;					//容器的宽度
		private var containerHeight:Number;				//容器的高度
		private var sound:SoundTransform;					//音量管理
		
		//构造函数
		public function PlayerCore()
		{
			
		}
		
		//设置URL
		public function init(serverURL:String, videoURL:String, video:CoreVideo, callback:Function, getInfo:Function, containerWidth:Number, containerHeight:Number, sound:SoundTransform):void
		{
			stop();
			this.netConnection = new NetConnection();
			this.customClient = new Object();
			this.netConnection.client = this.customClient;
			this.netConnection.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
			this.serverURL = serverURL;
			this.videoURL = videoURL;
			this.video = video;
			this.callbackFunction = callback;
			this.getInfoFunction = getInfo;
			this.containerWidth = containerWidth;
			this.containerHeight = containerHeight;
			this.sound = sound;
			callbackFunction.call(null, "创建", "this.netConnection = new NetConnection();");
		}
		
		//NC连接
		public function connectNetConncetion():void
		{
			if(this.netConnection != null)
			{
				this.netConnection.connect(this.serverURL);
				callbackFunction.call(null, "连接", "this.netConnection");
			}
		}
		
		//断开连接停止播放
		public function stop():void
		{
			if(this.callbackFunction != null)
			{
				callbackFunction.call(null, "断开", "this.netConnection");
			}
			if(this.video != null)
			{
				this.video.clear();
			}
			if(this.netStream != null)
			{
				this.netStream.close();
			}
			if(this.netConnection != null)
			{
				if(this.netConnection.hasEventListener(NetStatusEvent.NET_STATUS))
				{
					this.netConnection.removeEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
				}
				this.netConnection.close();
			}
			this.netStream = null;
			this.netConnection = null;
		}
		
		//NS连接
		private function connectNetStream():void
		{
			if(this.netConnection != null)
			{
				this.netStream = new NetStream(this.netConnection);						
				this.netStream.client = this.customClient;			
				this.netStream.addEventListener(AsyncErrorEvent.ASYNC_ERROR, asyncErrorHandler);
				this.netStream.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);			
				this.netStream.bufferTime = 3;			
				this.netStream.bufferTimeMax = 6;
				if(this.videoURL != "" && this.videoURL != null)
				{
					this.netStream.play(this.videoURL);			
					this.customClient.onMetaData = onMetaDataFunction;
				}
				this.netStream.soundTransform = this.sound;
			}
		}
		
		//延迟，准备连接
		private function delayToConnect():void
		{
			this.connectIntervalId = setTimeout(retry, 1000);
		}
		
		//取消超时
		public function cancelDelayToConnect():void
		{
			if(this.connectIntervalId != 0)
			{   
				clearTimeout(this.connectIntervalId);   
			}
		}
		
		//重新尝试连接
		public function retry():void	
		{
			callbackFunction.call(null, "retry()", "retry()");
			cancelDelayToConnect();
			if(this.netStream != null)
			{
				this.netStream.close();
			}
			if(this.netConnection != null)
			{
				callbackFunction.call(null, "this.netConnection != null", "this.netConnection != null");
				//如果关闭连接，然后要创建一个新连接，则必须创建新的 NetConnection 对象并再次调用 connect() 方法。 
				this.netConnection.close();
				this.netConnection = new NetConnection();
				this.netConnection.client = this.customClient;
				this.netConnection.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
				connectNetConncetion();
			}
		}
		
		public function onBWDone():void
		{
		}
		
		//获得描述性信息
		private function onMetaDataFunction(obj:Object):void	
		{
			getInfoFunction.call(null, obj.duration);
		}
		
		//状态监测
		public function netStatusHandler(event:NetStatusEvent)	:void
		{
			this.netStatus = event.info.code;
			callbackFunction.call(null, "加载", "百分比");
			switch (event.info.code) 
			{
				//连接成功
				case "NetConnection.Connect.Success":
					connectNetStream();
					callbackFunction.call(null, PlayerSituation.START, this.netStatus);
					break;
				case "NetStream.Play.Reset":
					break;
				case "NetStream.Play.Start":
					this.video.attachNetStream(this.netStream);
					callbackFunction.call(null, PlayerSituation.START, this.netStatus);
					break;
				case "NetStream.Buffer.Full":	
					callbackFunction.call(null, PlayerSituation.FULL, this.netStatus);
					break;
				//尝试重连
				case "NetStream.Play.StreamNotFound":
					callbackFunction.call(null, PlayerSituation.EMPTY, this.netStatus);
					break;
				case "NetConnection.Connect.Failed":
				case "NetConnection.Connect.Rejected":
				case "NetConnection.Connect.InvalidApp":
				case "NetConnection.Connect.NetworkChange":
				case "NetStream.Play.Failed":
				case "NetStream.Play.FileStructureInvalid":
				case "NetStream.Play.NoSupportedTrackFound":
					//从流取消的发布被发送到所有的订阅者
				case "NetStream.Play.UnpublishNotify":
					delayToConnect();
					callbackFunction.call(null, PlayerSituation.STOP, this.netStatus);
					break;
				//播放结束
				case "NetStream.Play.Stop":
					delayToConnect();
					callbackFunction.call(null, PlayerSituation.EMPTY, this.netStatus);
					break;
				case "NetStream.Buffer.Flush":
					//case "NetStream.Play.Stop":
					cancelDelayToConnect();
					callbackFunction.call(null, PlayerSituation.STOP, this.netStatus);
					break;
				case "NetStream.Buffer.Empty":	
					callbackFunction.call(null, PlayerSituation.EMPTY, this.netStatus);
					break;
				//流发布
				case "NetStream.Publish.Start":
					break;
				case "NetStream.Publish.BadName":
					break;
				case "NetStream.Publish.Idle":
					break;
				case "NetStream.Unpublish.Success":
					break;
				//流暂停、恢复
				case "NetStream.Pause.Notify":
				case "NetStream.Unpause.Notify":
					break;
				//非有效位置
				case "NetStream.Seek.InvalidTime":	
					break;
				//关闭连接
				case "NetConnection.Connect.Closed":
//					this.ncErrorCount++;
					delayToConnect();
					callbackFunction.call(null, PlayerSituation.EMPTY, this.netStatus);
					break;
			}		
		}
		
		//异步错误处理
		private function asyncErrorHandler(event:AsyncErrorEvent):void	
		{
			trace("asyncError " + this.netStatus, event.text);
		}
		
		//安全错误处理
		private function securityErrorHandler(event:SecurityErrorEvent):void
		{
			trace("securityError " + this.netStatus, event.text);
		}		
	}
}