package hpPlayer.giftBanner
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	
	import hpPlayer.giftBanner.GiftBannerItem;
	import hpPlayer.giftBanner.GiftBannerItemStatus;
	
	public class GiftBannerManager extends Sprite
	{
		private static var _instance:GiftBannerManager;	//单件实例
		private const MAXLINE:int = 3;						//最大行数
		private var giftBannerPool:Array;					//缓冲池，存放所有未添加的GiftBanner信息
		private var giftBannerList:Array;					//当前队列中的UID和receiverID
		private const INTERVAL:Number = 160;				//两个giftBannerItem之间的间隔
		private var containerGiftBanner:Sprite;			//giftBannerItem容器
		private var containerWidth:Number;					//容器的宽度，初始X位置
		private var containerHeight:Number;				//容器的高度
		private var timerGetGiftBanner:Timer;				//计时器，定时获取
		private var giftmc:MovieClip;						//礼物影片剪辑
		private var bannermc:MovieClip;					//横幅影片剪辑
		private var amountFirstLine:int = 0;				//第一行礼物个数
		private var amountSecondLine:int = 0;				//第二行礼物个数
		private var amountThirdLine:int = 0;				//第三行礼物个数
		
		//构造函数
		public function GiftBannerManager()
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
		public static function getInstance():GiftBannerManager
		{
			if(_instance != null)
			{
				return _instance;
			}
			else
			{
				return new GiftBannerManager();
			}
		}
		
		
		public function getListAmount():uint
		{	
			if(this.containerGiftBanner != null)//this.giftBannerList != null)
			{
				return this.containerGiftBanner.numChildren;//this.giftBannerList.length;
			}
			else
			{
				return 0;
			}
		}
		
		//初始化
		public function init():void
		{
			this.giftBannerPool = new Array();
			this.giftBannerList = new Array();
			this.timerGetGiftBanner = new Timer(1000);
			this.timerGetGiftBanner.addEventListener(TimerEvent.TIMER, procedure);
			this.timerGetGiftBanner.start();
		}
		
		//设置容器
		public function setupWithContainer(container:Sprite, width:Number, height:Number, giftmc:MovieClip, bannermc:MovieClip):void
		{
			this.containerGiftBanner = container;
			this.containerWidth = width;
			this.containerHeight = height;
			this.giftmc = giftmc;
			this.bannermc = bannermc;
			if(this.giftmc != null)
			{
				addChild(this.giftmc);
			}
			if(this.bannermc != null)
			{
				addChild(this.bannermc);
			}
			var bck:Shape = new Shape();
			bck.graphics.beginFill(0xffffff, 0);
			bck.graphics.drawRect(0, 0, containerWidth, containerHeight);
			bck.graphics.endFill();
			this.containerGiftBanner.addChild(bck);
			init();
		}
		
		//全部流程
		private function procedure(event:TimerEvent):void
		{
			var index:int;
			for(index = 0; index < this.giftBannerPool.length; index++)
			{
				var gbi:GiftBannerItem = this.getValidGiftBannerItem();
				gbi.setup(duplicateDisplayObject(this.giftmc) as MovieClip, duplicateDisplayObject(this.bannermc) as MovieClip, callFinish);
				var locationy:Number = this.getLocationY(this.giftBannerPool[index][0] as int, this.giftBannerPool[index][1] as int);
				//找到合适y值
				if(locationy >= 0)
				{
					this.giftBannerList.push(gbi);
					gbi.setText(this.giftBannerPool[index][0], this.giftBannerPool[index][1],
						this.giftBannerPool[index][2], this.giftBannerPool[index][3], this.giftBannerPool[index][4]);
					this.giftBannerPool.splice(index, 1);
					gbi.startMoving(containerWidth, locationy);
					this.containerGiftBanner.addChild(gbi);
//					按照下一行压住上一行、同行则后压住前的规则
//					if(locationy == 0)
//					{
//						this.containerGiftBanner.addChildAt(gbi, this.amountFirstLine + 1);
//					}
//					if(locationy == 48)
//					{
//						this.containerGiftBanner.addChildAt(gbi, this.amountFirstLine + this.amountSecondLine + 1);
//					}
//					if(locationy == 96)
//					{
//						this.containerGiftBanner.addChildAt(gbi, this.amountFirstLine + this.amountSecondLine + this.amountThirdLine + 1);
//					}
				}
				//未找到合适y值
				else
				{
					var tempUID:int = this.giftBannerPool[index][0];
					var tempreceiverID:int = this.giftBannerPool[index][1];
					var tempsenderName:String = this.giftBannerPool[index][2];
					var tempreceiverName:String = this.giftBannerPool[index][3];
					var templiveroomURL:String = this.giftBannerPool[index][4];
					this.giftBannerPool.push([tempUID, tempreceiverID, tempsenderName, tempreceiverName, templiveroomURL]);
					this.giftBannerPool.splice(index, 1);
					if(index < this.giftBannerList.length - 1)
					{
						index++;
					}
				}
			}			
		}
		
		//运行完成回调
		private function callFinish(UID:int, receiverID:int):void
		{
//			trace("  " + UID + " " + receiverID +"  运行完成！");
		}
		
		//检测是否有可用的GiftBannerItem
		private function getValidGiftBannerItem():GiftBannerItem
		{
			return new GiftBannerItem();
			if(this.giftBannerList != null)
			for(var index:int = 0; index < this.giftBannerList.length; index++)
			{
				var gbi:GiftBannerItem = this.giftBannerList[index] as GiftBannerItem;
				if(gbi.status == GiftBannerItemStatus.READY)
				{
					return gbi;
				}
			}
			return new GiftBannerItem();
		}
		
		//添加礼物横幅
		public function addGiftBannerMessage(UID:int, receiverID:int, sender:String, receiver:String, liveroomURL:String):void
		{
			if(this.giftBannerPool != null)
			{
				this.giftBannerPool.push([UID, receiverID, sender, receiver, liveroomURL]);
			}
		}
		
		//深拷贝
		private static function duplicateDisplayObject(target:DisplayObject, autoAdd:Boolean = false):DisplayObject
		{
			var targetClass:Class = Object(target).constructor;
			var duplicate:DisplayObject = new targetClass();
			duplicate.transform = target.transform;
			duplicate.filters = target.filters;
			duplicate.cacheAsBitmap = target.cacheAsBitmap;
			duplicate.opaqueBackground = target.opaqueBackground;
			if(autoAdd && target.parent)
			{
				target.parent.addChild(duplicate);
			}
			return duplicate;
		}
		
		//计算locationY值
		private function getLocationY(UID:int, receiverID:int):Number
		{
			var locationY:Number = -1;
			var index:int;
			var list:Array;
			var gbiAlreadyExisted:GiftBannerItem;
			var gbiOnList:GiftBannerItem;
			for(index = 0; index < this.giftBannerList.length; index++)
			{
				gbiAlreadyExisted = this.giftBannerList[index] as GiftBannerItem;
				if(gbiAlreadyExisted.UID == 0 && gbiAlreadyExisted.receiverID == 0)
				{
					this.giftBannerList.splice(index, 1);
					index--;
				}
			}
			list = this.giftBannerList;
			//当前没有礼物横幅
			if(list.length == 0)
			{
				return 0;
			}
			//礼物横幅按y值排序
			list.sortOn("y", Array.NUMERIC);
			//有相同的sender和receiver，则添加进同一道中
			for(index = 0; index < list.length; index++)
			{
				gbiOnList = list[index] as GiftBannerItem;
				if(gbiOnList.UID == UID && gbiOnList.receiverID == receiverID)
				{
					locationY = gbiOnList.y;
					return locationY;
				}
			}
			//检查有可用的位置
			if(locationY >= 0)
			{
//				return locationY;
			}
			else
			{
				if(list.length >= MAXLINE)
				{
					return -1;
				}
				else
				{
					var count0:int = 0;
					var count48:int = 0;
					var count96:int = 0;
					for(index = 0; index < list.length; index++)
					{
						gbiOnList = list[index] as GiftBannerItem;
						if(gbiOnList.y == 0)
						{
							trace("第1行已被占用");
							count0 = 1;
							this.amountFirstLine = this.amountFirstLine + 1;
							trace(this.amountFirstLine);
						}
						if(gbiOnList.y == 48)
						{
							trace("第2行已被占用");
							count48 = 1;
							this.amountSecondLine = this.amountSecondLine + 1;
							trace(this.amountSecondLine);
						}
						if(gbiOnList.y == 96)
						{
							trace("第3行已被占用");
							count96 = 1;
							this.amountThirdLine = this.amountThirdLine + 1;
							trace(this.amountThirdLine);
						}
					}
					trace("——————————");
					if(count0 == 0)
						return 0;
					if(count0 == 1 && count48 == 0)
						return 48;
					if(count0 == 1 && count48 == 1 && count96 == 0)
						return 96;
					if(count0 == 1 && count48 == 1 && count96 == 1)
						return -1;
				}
			}
			return locationY;
		}
	}
}