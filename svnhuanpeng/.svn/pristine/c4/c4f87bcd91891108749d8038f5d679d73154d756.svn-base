package hpPlayer
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	
	public class UIButton extends Sprite
	{
		private var buttonMovieclip:MovieClip;
		
		public function UIButton():void
		{
			
		}
		
		public function setupMovieclip(mc:MovieClip):void
		{
			this.buttonMovieclip = mc;
			this.buttonMode = true;
			this.buttonMovieclip.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.DOUBLE_CLICK, mouseClickFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.MOUSE_DOWN, mouseClickFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.MOUSE_OVER, mouseMoveFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.ROLL_OVER, mouseMoveFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.MOUSE_OUT, mouseOutFunction);
			this.buttonMovieclip.addEventListener(MouseEvent.MOUSE_UP, mouseOutFunction);
			this.addChild(this.buttonMovieclip);
		}
		
		private function  mouseClickFunction(event:MouseEvent):void
		{
			this.buttonMovieclip.gotoAndStop(3);
		}
		
		private function  mouseMoveFunction(event:MouseEvent):void
		{
			this.buttonMovieclip.gotoAndStop(2);
		}
		
		private function  mouseOutFunction(event:MouseEvent):void
		{
			this.buttonMovieclip.gotoAndStop(1);
		}
	}
}