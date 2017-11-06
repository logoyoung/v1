package hpPlayer
{
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.filters.DropShadowFilter;
	import flash.text.TextField;
	import flash.text.TextFormat;
	
	//采用单件模式
	//输出调试文本信息
	public class DebugInfoManager extends Object
	{
		
		private static var _instance:DebugInfoManager;			//单件实例
		private var tfDebug:TextField;							//文本对象
		private var container:Sprite;							//容器
		private var background:Sprite;							//背景
		
		//单件构造函数
		public function DebugInfoManager()
		{
			if(_instance == null)
			{
				_instance = this;
			}
			else
			{
				throw new Error("Instance has already existed");
			}
		}
		
		//单件构造方法
		public static function getInstance():DebugInfoManager
		{
			if(_instance != null)
			{
				return _instance;
			}
			else
			{
				return new DebugInfoManager();
			}
		}
		
		//在容器中设置调试文本对象
		public function setupWithContainer(container:Sprite, width:Number, height:Number):void
		{
			if(this.background != null)
			{
				if(this.background.parent != null)
				{
					this.background.parent.removeChild(background);
				}
			}
			else
			{
				var tformatDebug:TextFormat = new TextFormat();
				tformatDebug.size = 18;
				tformatDebug.color = 0xffffff;	
				var shadow:Array = [new DropShadowFilter(2, 45, 0x000000, 1, 4, 4, 1, 1, false, false)];
				this.tfDebug = new TextField();
				this.tfDebug.defaultTextFormat = tformatDebug;
				this.tfDebug.filters = shadow;
				this.tfDebug.selectable = true;
				this.tfDebug.visible = true;
				this.tfDebug.multiline = true; 
				this.tfDebug.wordWrap = true;	
				this.background = new Sprite();
				this.background.addChild(this.tfDebug);
			}
			this.background.graphics.clear();
			this.background.graphics.beginFill(0x000000, 0.3);
			this.background.graphics.drawRect(0, 0, width, height);
			this.background.graphics.endFill();
			this.tfDebug.x = 0;
			this.tfDebug.y = 0;
			this.tfDebug.width = width;
			this.tfDebug.height = height;
			this.container = container;
			this.container.addChild(this.background);	
			this.container.mouseChildren = false;
			this.container.mouseEnabled = false;
		}
		
		//显示调试信息
		public function show():void
		{
			container.visible = true;
		}
		
		//隐藏调试信息
		public function hide():void
		{
			container.visible = false;
		}
		
		//设置调试文本内容
		public function log(strLog:String):void
		{
			if(this.tfDebug.text == null || this.tfDebug.text == "")
			{
				this.tfDebug.text = strLog;
			}
			else
			{
				this.tfDebug.text = strLog + "\n" + this.tfDebug.text;
			}
		}
		
		public function changeMouseEnabled():void
		{
			var enable:Boolean = this.container.mouseEnabled;
			this.container.mouseChildren = !enable;
			this.container.mouseEnabled = !enable;
		}
	}
}