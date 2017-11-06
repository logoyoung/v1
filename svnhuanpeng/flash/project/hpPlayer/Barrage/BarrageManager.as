package hpPlayer.Barrage
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.text.Font;
	import flash.utils.Timer;
	
	public class BarrageManager extends Sprite
	{
		private static var _instance:BarrageManager;		//单件实例
		private var textPool:Array;						//缓冲池，存放所有未添加的弹幕文字
		private var barrageItemList:Array;					//弹幕列表
		private const INTERVAL:Number = 20;				//两条弹幕之间的间隔
		public var barrageContainer:Sprite;					//弹幕容器
		private var bck:Shape;								//弹幕容器背景
		private var timerGetBarrage:Timer;					//计时器，定时获取弹幕
		private var containerWidth:Number;					//容器的宽度，初始X位置
		private var containerHeight:Number;				//容器的高度
		private var barrageFont:String;					//弹幕字体
		private const fontCompareList:Array = new Array("Microsoft YaHei UI",	//预订字体
			"Microsoft YaHei", "STHeiti", "PingFang SC", "PingFang", "Helvetica");
		
		//构造函数
		public function BarrageManager()
		{
			if(_instance == null)
			{
				_instance = this;
			}
			else
			{
				throw Error("Instance has already existed");
			}
		}
		
		//单件构造方法
		public static function getInstance():BarrageManager
		{
			if(_instance != null)
			{
				return _instance;
			}
			else
			{
				return new BarrageManager();
			}
		}
		
		//初始化
		public function init():void
		{
			this.textPool = new Array();
			this.barrageItemList = new Array();
			this.timerGetBarrage = new Timer(100);
			this.bck = new Shape();
			this.timerGetBarrage.addEventListener(TimerEvent.TIMER, procedure);
			this.timerGetBarrage.start();
			var allFonts:Array = Font.enumerateFonts(true);
			var i:int;
			for(var iCompareList:int = 0; iCompareList <　this.fontCompareList.length; iCompareList++)
			{
				for(var index:int = 0; index < allFonts.length; index++)
				{
					if(allFonts[index].fontName == fontCompareList[iCompareList])
					{
						this.barrageFont = allFonts[index].fontName;
						break;
					}
				}
				if(this.barrageFont != null)
				{
					break;
				}
			}
		}
		
		//设置容器
		public function setupWithContainer(container:Sprite, width:Number, height:Number):void
		{
			this.barrageContainer = container;
			this.containerWidth = width;
			this.containerHeight = height;
//			var bck:Shape = new Shape();
			this.bck.graphics.clear();
			this.bck.graphics.beginFill(0xffffff, 0);
			this.bck.graphics.drawRect(0, 0, containerWidth, containerHeight);
			this.bck.graphics.endFill();
			this.barrageContainer.addChild(this.bck);
		}		
		
		//添加弹幕文字
		public function addBarrageMessage(str:String):void
		{
			if(this.textPool != null)
			{
				this.textPool.push(str);
			}
		}
		
		//全部流程
		private function procedure(event:TimerEvent):void
		{
			if(this.textPool != null)
			{
				while(this.textPool.length > 0)
				{
					var barrageItem:BarrageItem = this.getValidBarrageItem();
					var barrageMsg:String = this.textPool[0];
					barrageItem.setText(barrageMsg);
//					barrageItem.x = containerWidth;
					var locationy:Number = this.getLocationY(barrageItem.height);
					if(locationy >= 0)
					{
						this.barrageItemList.push(barrageItem);
						this.textPool.shift();
						barrageItem.startMoving(containerWidth, locationy);
						this.barrageContainer.addChild(barrageItem);
					}
					else
					{
						break;
					}
				}
			}
		}
		
		//重置
		private function reset():void
		{
			if(this.barrageItemList != null)
			{
				for(var i:int = 0; i < this.barrageItemList.length; i++)
				{
					var obj:BarrageItem = this.barrageItemList[i] as BarrageItem;
					obj.reset();
				}
			}
			this.barrageItemList = null;
			this.textPool = null;
		}
		
		//检测是否有可用的barrageItem
		private function getValidBarrageItem():BarrageItem
		{
			for(var index:int = 0; index < this.barrageItemList.length; index++)
			{
				var bi:BarrageItem = this.barrageItemList[index] as BarrageItem;
				if(bi.status == BarrageItemStatus.READY)// && bi.x <= 0)
				{
					return bi;
				}
			}
			return new BarrageItem(this.barrageFont);
		}
		
		//弹幕关
		public function barrageOff():void
		{
			reset();
			this.timerGetBarrage.stop();
		}
		
		//弹幕开
		public function barrageOn():void
		{
			this.textPool = new Array();
			this.barrageItemList = new Array();
			this.timerGetBarrage.start();
		}
		
		//计算locationY值
		private function getLocationY(barrageitemHeight:Number):Number
		{
			var list:Array = new Array();
			var locationY:Number = -1;
			//将barrageItemList中所有压边的弹幕放入list中
			for(var indexlist:int = 0; indexlist < this.barrageItemList.length; indexlist++)
			{
				var bilist:BarrageItem = this.barrageItemList[indexlist] as BarrageItem;
				if(bilist.x + bilist.width + INTERVAL > containerWidth && bilist.x > 0 && bilist.x <= containerWidth)
				{
					list.push(bilist);
				}
			}
			//如果list长度小于1，即“当前没有压边的弹幕”，则返回0
			if(list.length < 1)
			{
				return 0;
			}
			if(list.length == 1)
			{
				bilist = list[0] as BarrageItem;
				if(bilist.tfBarrage.text == "")
				{
					return 0;
				}
			}
			//list将y作为数字进行排序，升序
			list.sortOn("y", Array.NUMERIC);
			for(var index:int = 0; index <= list.length;)
			{
				var start:Number;
				var end:Number;
				var bi:BarrageItem;
				var nextbi:BarrageItem;
				//list中的第一个，检查该条弹幕上方的高度是否合适
				if(index == 0)
				{
					bi = list[index] as BarrageItem;
					start = 0;
					end = bi.y;
				}
					//list中的最后一个，检查该条弹幕下方的高度是否合适
				else	if(index == list.length)
				{
					bi = list[index -1] as BarrageItem;
					start = bi.y + bi.height;
					end = this.barrageContainer.height;
				}
					//list中的中间，检查该条弹幕和下一条弹幕的高度是否合适
				else
				{
					bi = list[index -1] as BarrageItem;
					nextbi = list[index] as BarrageItem;
					start = bi.y + bi.height;
					end = nextbi.y;
				}
				index++;
				if(end - start >= barrageitemHeight)
				{
					locationY =  start;
					break;
				}
			}
			return locationY;
		}
	}
}