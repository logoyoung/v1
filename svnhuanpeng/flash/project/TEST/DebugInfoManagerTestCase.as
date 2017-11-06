package TEST
{
	
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import hpPlayer.DebugInfoManager;
	
	[SWF(height=500,width=400)] 
	
	public class DebugInfoManagerTestCase extends Sprite
	{
		private var containerTest:Sprite;			
		private var containerTest2:Sprite;			
		private var dim:DebugInfoManager;
		
		public function DebugInfoManagerTestCase()
		{
			var count:int;
			stage.color = 0x000066;		
			//红色方块，测试添加顺序
			var sp:Shape = new Shape();
			sp.graphics.clear();
			sp.graphics.beginFill(0xff0000);
			sp.graphics.drawRect(0,0,100,100);
			stage.addChild(sp);
			dim = DebugInfoManager.getInstance();
			//第1个容器
			containerTest = new Sprite();
			dim.setupWithContainer(containerTest,stage.stageWidth/2,stage.stageHeight/2);
			dim.log("testtesttesttesttesttest");
			for( count = 0 ; count<25 ; count++)
			{
				dim.log(count+"第1次");
			}
			containerTest.x = 0;
			containerTest.y = 0;
			containerTest.height = stage.stageHeight/2;
			containerTest.width = stage.stageWidth/2;
			containerTest.visible = true;
			stage.addChild(containerTest);	
			//第2个容器
			containerTest2 = new Sprite();
			dim.setupWithContainer(containerTest2,stage.stageWidth*3/4,stage.stageHeight*3/4);
			dim.log("abcdefghijklmnopqrstuvwxyz");
			for(count = 0 ; count<25 ; count++)
			{
				dim.log(count+"第2次");
			}
			containerTest2.x = 60;
			containerTest2.y = 60;
			containerTest2.height = stage.stageHeight*3/4;
			containerTest2.width = stage.stageWidth*3/4;
			containerTest2.visible = true;
			stage.addChild(containerTest2);
			dim.log("abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz");	
			//绿色方块，测试添加顺序
			var sp2:Shape = new Shape();
			sp2.graphics.clear();
			sp2.graphics.beginFill(0x00ff00);
			sp2.graphics.drawRect(0,400,100,100);
			stage.addChild(sp2);
		}				
	}
}