package	 hpPlayer
{
	import flash.media.Video;
	
	
	public class CoreVideo extends Video
	{		
		private var CurrentScreenDirection:String;					//横屏或竖屏
		private const RATIO:Number = 9 / 16;						//视频的宽高比
		
		//构造函数
		public function CoreVideo()
		{
			this.smoothing = true;
			this.deblocking = 2;		
		}
		
		//根据横竖屏，进行界面布局
		public function drawWithDirection(screenDirection:String, containerWidth:Number, containerHeight:Number):void
		{
			this.CurrentScreenDirection = screenDirection;
			//横屏
			if(this.CurrentScreenDirection == VideoScreenDirection.HORIZONTAL)
			{				
				this.width = containerWidth;
				this.height = RATIO * this.width;
			}
			//竖屏
			if(this.CurrentScreenDirection == VideoScreenDirection.VERTICAL)
			{
				this.height = containerHeight;
				this.width = RATIO * this.height;				
			}
			this.x = (containerWidth - this.width) / 2;
			this.y = (containerHeight - this.height) / 2;
		}
		
		//获得当前横竖屏信息
		public function getCurrentScreenDirection():String
		{
			return this.CurrentScreenDirection;
		}
	}
}