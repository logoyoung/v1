package hpPlayer
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.text.Font;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.utils.Timer;
	
	public class showPercent extends Sprite
	{
		public var mcBackground:MovieClip;				//背景
		public var mcPercent:MovieClip;				//已加载的部分
		public var tfPercent:TextField;				//文本对象
		private var tfFormat:TextFormat;				//格式
		private var value:Number;						//值
		private var returnValueCallback:Function;		//返回值的回调函数
		private var initValue:Number = 0;				//初始值
		private var percentFont:String;				//百分比字体
		private const fontCompareList:Array = new Array("Microsoft YaHei UI",	//预订字体
			"Microsoft YaHei");
		private var timerHide:Timer;					//计时器
		
		public function showPercent():void
		{
			var allFonts:Array = Font.enumerateFonts(true);
			var i:int;
			for(var iCompareList:int = 0; iCompareList <　this.fontCompareList.length; iCompareList++)
			{
				for(var index:int = 0; index < allFonts.length; index++)
				{
					if(allFonts[index].fontName == fontCompareList[iCompareList])
					{
						this.percentFont = allFonts[index].fontName;
						break;
					}
				}
				if(this.percentFont != null)
				{
					break;
				}
			}
		}
		
		public function setup(mcBackground:MovieClip, mcPercent:MovieClip):void
		{
			if(this.mcBackground == null)
			{
				this.mcBackground = mcBackground;
				this.mcBackground.x = 0;
				this.mcBackground.y = 0;
			}
			if(this.mcPercent == null)
			{
				this.mcPercent = mcPercent;
				this.mcPercent.x = this.mcBackground.x + 19;
				this.mcPercent.y = this.mcBackground.y + 12;
				this.mcPercent.width = (this.mcBackground.width - 19 - 9) * this.initValue / 100 + 4;
			}
			this.addChild(this.mcBackground);
			this.addChild(this.mcPercent);
			if(this.tfPercent == null)
			{
				this.tfPercent = new TextField();
			}
			if(this.tfFormat == null)
			{
				this.tfFormat = new TextFormat();
			}
			this.tfFormat.size = 12;
			this.tfFormat.color = 0xffc89a;
			this.tfFormat.font = this.percentFont;
			this.tfPercent.defaultTextFormat = this.tfFormat;
			this.tfPercent.text = this.initValue + "%";
			this.tfPercent.height = this.tfPercent.textHeight + 4;
			this.tfPercent.width = this.tfPercent.textWidth + 4;
			this.tfPercent.x = this.mcBackground.x + 19;
			this.tfPercent.y = this.mcBackground.y - 20;
			this.tfPercent.mouseEnabled = false;
			this.addChild(this.tfPercent);
		}
		
		public function setValue(value:Number):void
		{
			if(value >= 0 && value <= 100)
			{
				this.value = value;
			}
			if(value < 0)
			{
				this.value = 0;
			}
			if(value > 100)
			{
				this.value = 100;
			}
			if(this.mcPercent != null)
			{
				this.mcPercent.x = this.mcBackground.x + 19;
				this.mcPercent.width = (this.mcBackground.width - 19 - 9) * this.value / 100;
			}
			if(this.tfPercent != null)
			{
				this.tfPercent.x = this.mcPercent.x + this.mcPercent.width - this.tfPercent.width / 2;
				this.tfPercent.text = this.value + "%";
				this.tfPercent.width = this.tfPercent.textWidth + 4;
			}
			if(this.value >= 100)
			{
				this.timerHide = new Timer(10);
				this.timerHide.addEventListener(TimerEvent.TIMER, hide)
				this.timerHide.start();
			}
		}
		
		private function hide(event:TimerEvent):void
		{
			if(this.timerHide != null)
			{
				this.timerHide.stop();
			}
//			if(this.mcBackground != null && this.mcPercent != null && this.tfPercent != null)
//			{
//				this.mcBackground.visible = false;
//				this.mcPercent.visible = false;
//				this.tfPercent.visible = false;
//			}
//			for(var index:int = 0; index < this.numChildren; index++)
//			{
//				if(this.getChildAt(index) != null)
//				{
//					this.getChildAt(index).visible = false;
//				}
//			}
			if(this.mcBackground != null)
			{
				this.mcBackground.visible = true;
			}
			if(this.mcPercent != null)
			{
				this.mcPercent.visible = true;
			}
			if(this.tfPercent != null)
			{
				this.tfPercent.visible = true;
			}
		}
		
		public function show():void
		{
			if(this.mcBackground != null)
			{
				this.mcBackground.visible = true;
			}
			if(this.mcPercent != null)
			{
				this.mcPercent.visible = true;
			}
			if(this.tfPercent != null)
			{
				this.tfPercent.visible = true;
			}
//			for(var index:int = 0; index < this.numChildren; index++)
//			{
//				if(this.getChildAt(index) != null)
//				{
//					this.getChildAt(index).visible = true;
//				}
//			}
//			if(this.mcBackground != null && this.mcPercent != null && this.tfPercent != null)
//			{
//				this.mcBackground.visible = true;
//				this.mcPercent.visible = true;
//				this.tfPercent.visible = true;
//			}
		}
	}
}