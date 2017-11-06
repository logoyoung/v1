package TEST
{
	
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.filters.GlowFilter;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.utils.Timer;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import hpPlayer.Barrage.BarrageManager;
	
	[SWF(width = "550", height = "450", frameRate = "30")]
	public class BarrageManagerTestCase extends Sprite
	{
		private var intervalId:int;								//时间间隔ID
		private var jsonObj:Object; 								//JSON对象
		private var strlog:String;									//JS输出到控制台文本
		private var pool:Array;									//缓冲池
		private var barrageItemList:Array;							//弹幕列表
		private static var MAX_VALUE:int = 100; 					//列表最大值
		private var timer:Timer;									//计时器，定时自动添加弹幕
		private var testBarrageContainer:Sprite = new Sprite();	//弹幕容器
		private var timerGetBarrage:Timer = new Timer(100);		//计时器，定时获取弹幕
		private var testTime:int = 1;
		private var add1:Sprite;
		private var add2:Sprite;
		private var add3:Sprite;
		private var add4:Sprite;
		
		//构造函数
		public function BarrageManagerTestCase()
		{
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;
			stage.color = 0x00CC00;
			var background:Shape = new Shape();
			background.graphics.beginFill(0x676767, 0);
			background.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
			background.graphics.endFill();
			this.testBarrageContainer.addChild(background);
			stage.addChild(this.testBarrageContainer);
			ExternalInterface.addCallback("JSaddBarrage",JSaddBarrage);
			ExternalInterface.addCallback("barrageOff",barrageOff);
			ExternalInterface.addCallback("barrageOn",barrageOn);
			BarrageManager.getInstance().init();
			BarrageManager.getInstance().setupWithContainer(this.testBarrageContainer, this.testBarrageContainer.width, this.testBarrageContainer.height);
//			for(testTime = 1; testTime < 25; testTime++)
//			{
//				bm.addBarrageMessage("弹幕测试：第 " + testTime + " 条 " );//+ testTime * testTime + " " + testTime * testTime * testTime);
//			}
//			timer = new Timer(2500,1);	
//			timer.addEventListener(TimerEvent.TIMER,add);
//			timer.start();
			this.add1 = new Sprite();
			this.add1.graphics.beginFill(0x990000);
			this.add1.graphics.drawCircle(30, 330, 10);
			this.add1.graphics.endFill();
			this.testBarrageContainer.addChild(add1);
			this.add2 = new Sprite();
			this.add2.graphics.beginFill(0x009900);
			this.add2.graphics.drawCircle(60, 330, 10);
			this.add2.graphics.endFill();
			this.testBarrageContainer.addChild(add2);
			this.add3 = new Sprite();
			this.add3.graphics.beginFill(0x000099);
			this.add3.graphics.drawCircle(90, 330, 10);
			this.add3.graphics.endFill();
			this.testBarrageContainer.addChild(add3);
			this.add4 = new Sprite();
			this.add4.graphics.beginFill(0x660000);
			this.add4.graphics.drawCircle(120, 330, 10);
			this.add4.graphics.endFill();
			this.testBarrageContainer.addChild(add4);
			this.add1.addEventListener(MouseEvent.CLICK, add1ClickFunction);
			this.add2.addEventListener(MouseEvent.CLICK, add2ClickFunction);
			this.add3.addEventListener(MouseEvent.CLICK, add3ClickFunction);
			this.add4.addEventListener(MouseEvent.CLICK, add4ClickFunction);
		}
		
		private function add1ClickFunction(event:MouseEvent):void
		{
			BarrageManager.getInstance().addBarrageMessage("弹幕测试：第 1 条 " );
		}
		private function add2ClickFunction(event:MouseEvent):void
		{
			BarrageManager.getInstance().addBarrageMessage("弹幕测试：第 2 条 " );
		}
		private function add3ClickFunction(event:MouseEvent):void
		{
			BarrageManager.getInstance().addBarrageMessage("弹幕测试：第 3 条 " );
		}
		private function add4ClickFunction(event:MouseEvent):void
		{
			BarrageManager.getInstance().addBarrageMessage("弹幕测试：第 4 条 " );
		}
		
		//添加弹幕
		public function add(event:TimerEvent):void
		{
			for(testTime = 25; testTime < 50; testTime++)
			{
				BarrageManager.getInstance().addBarrageMessage("弹幕测试：第 " + testTime + " 条 " );//+ testTime * testTime + " " + testTime * testTime * testTime);
			}
		}
		
		//JS添加弹幕
		public function JSaddBarrage(str:String):void
		{
			jsonObj = new Object(); 
			jsonObj = JSON.parse(str);
			var text:String = jsonObj.msg;
			BarrageManager.getInstance().addBarrageMessage(text);
		}
		
		//弹幕关
		public function barrageOff():void
		{
			//重置当前
			BarrageManager.getInstance().barrageOff();
			//不添加新弹幕
			timer.stop();
		}
		
		//弹幕开
		public function barrageOn():void
		{
			BarrageManager.getInstance().barrageOn();
			timer.start();
		}
	}
}