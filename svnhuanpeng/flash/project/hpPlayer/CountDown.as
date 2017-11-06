package hpPlayer
{
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.utils.Timer;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;

	public class CountDown extends Sprite
	{
		private var container:Sprite;						//容器
		private var tfTime:TextField;						//textf对象
		private var updateIntervalId:int;					//刷新时间间隔ID
		private var endIntervalId:int;						//结束时间间隔ID
		public var totalTime:int;							//秒数
		private var containerWidth:Number;					//容器的宽度
		private var containerHeight:Number;				//容器的高度
		public var finish:Boolean = true;					//是否结束
		
		public function CountDown()
		{

		}
		
		private function init():void
		{
			this.container = new Sprite();
			var background:Shape = new Shape();
			background.graphics.beginFill(0xa9a9a9, 1);
			background.graphics.drawRect(0, 0, this.containerWidth, this.containerHeight);
			background.graphics.endFill();
			this.container.addChild(background);
			this.tfTime = new TextField();
			var tformat:TextFormat = new TextFormat();
			tformat.size = 16;
			tformat.color = 0xffffff;
			tformat.font = "Microsoft YaHei UI";
			this.tfTime.defaultTextFormat = tformat;
			this.tfTime.selectable = false;
			this.tfTime.text = this.totalTime + "s";
			this.tfTime.autoSize = TextFieldAutoSize.CENTER;
			this.tfTime.x = this.containerWidth / 2 - this.tfTime.width / 2;
			this.tfTime.y = this.containerHeight / 2 - this.tfTime.height / 2;
			this.container.addChild(this.tfTime);
			this.addChild(this.container);
			this.updateIntervalId = setTimeout(update, 100);
			this.finish = false;
		}
		
		public function setup(totalTime:Number, containerWidth:Number, containerHeight:Number):void
		{
			this.totalTime = totalTime;
			this.containerWidth = containerWidth;
			this.containerHeight = containerHeight;
			init();
		}
		
		private function update():void
		{
			if(this.totalTime >= 0)
			{
				this.tfTime.text = this.totalTime + "s";
				this.totalTime--;
				this.updateIntervalId = setTimeout(update, 1000);
			}
			if(this.totalTime < 0)
			{
				this.endIntervalId = setTimeout(end, 500);
			}
		}
		
		private function end():void
		{
			if(this.updateIntervalId != 0)
			{
				clearTimeout(this.updateIntervalId);
			}
			if(this.endIntervalId != 0)
			{
				clearTimeout(this.endIntervalId);
			}
			this.visible = false;
			this.finish = true;
		}
	}
}