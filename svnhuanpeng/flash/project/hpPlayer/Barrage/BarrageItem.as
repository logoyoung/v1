package hpPlayer.Barrage
{
	
	import com.gskinner.motion.GTween;
	import com.gskinner.motion.GTweenTimeline;
	import com.gskinner.motion.easing.*;
	
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.filters.GlowFilter;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.text.TextFormatAlign;
	
	public class BarrageItem extends Sprite
	{
		public var tfBarrage:TextField;						//弹幕文本对象
		public var status:String;							//弹幕状态
		private var gtween:GTween;							//动画
		private var gtweentimeline:GTweenTimeline;			//动画时间轴
		private const offset:Number = 50;					//弹幕对象间隔
		private const duration:Number = 8;					//时长
		private const distancePerFrame:Number = 125;		//速度
		private var containerWidth:Number;					//容器宽度
		
		//构造函数，单条弹幕基本设置
		public function BarrageItem(fontName:String)
		{
			this.tfBarrage = new TextField();
			var fm:TextFormat = new TextFormat();
			fm.size = 20;
			fm.color = 0xFFFFFF;
			fm.font = fontName;
			
			var filter:Array = [ new GlowFilter(0x404040, 1.0, 2.0, 2.0, 10, 1, false, false) ];  
			this.tfBarrage.defaultTextFormat = fm;
			this.tfBarrage.filters = filter; 
			this.tfBarrage.cacheAsBitmap = true;
			this.tfBarrage.autoSize = TextFieldAutoSize.CENTER;
			this.tfBarrage.selectable = false;
			this.tfBarrage.background = false;
			this.tfBarrage.mouseEnabled = false;
			this.tfBarrage.wordWrap = false;
			this.tfBarrage.multiline = false;
			this.tfBarrage.visible = true;
			this.status = BarrageItemStatus.READY;
			this.addChild(this.tfBarrage);	
		}
		
		//开始移动
		public function startMoving(locationX:Number, locationY:Number):void
		{
			this.x = locationX;
			this.y = locationY;
			this.containerWidth = locationX;
			this.status = BarrageItemStatus.MOVING;
			this.gtween = new GTween(this, (this.x + this.tfBarrage.width) / distancePerFrame, { x:- this.tfBarrage.width - offset }, { autoPlay:false, ease:Linear.easeNone }, { MotionBlurEnabled:false });
			this.gtweentimeline = new GTweenTimeline();
			this.gtweentimeline.addTween(0, this.gtween);
			this.gtweentimeline.calculateDuration();
			this.gtween.onComplete = gtweenComplete;
			this.addChild(this.tfBarrage);	
		}
		
		//运行完成
		private function gtweenComplete(tween:GTween):void
		{
			this.status = BarrageItemStatus.READY;
			this.gtween = null;
			this.gtweentimeline = null;
			this.x = this.containerWidth + 1;
			this.y = 0;
			this.tfBarrage.text = "";
		}
		
		//重置
		public function reset():void
		{
			if(this.gtweentimeline != null)
			{
				this.gtweentimeline.end();
			}
		}
		
		//设置弹幕文字
		public function setText(strBarrage:String):void
		{
			strBarrage = strBarrage.replace('\n','');
			strBarrage = strBarrage.replace('\r','');
			this.tfBarrage.text = strBarrage;
			this.tfBarrage.width = this.tfBarrage.textWidth + 4;
			this.tfBarrage.height = this.tfBarrage.textHeight + 4;
			this.width = this.tfBarrage.width + 2;
			this.height = this.tfBarrage.height + 2;
			this.tfBarrage.x = 1;
			this.tfBarrage.y = 1;
		}
	}
}