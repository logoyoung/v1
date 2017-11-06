package hpPlayer
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	
	public class Slider extends Sprite
	{
		private var buttom:MovieClip;					//底部
		private var selected:MovieClip;				//已选中部分
		private var dragSlider:MovieClip;				//拖动的滑块
		private var value:Number;						//Slider值
		private var returnValueCallback:Function;		//返回值的回调函数
		
		public function Slider():void
		{
			
		}
		
		//设置
		public function setup(buttom:MovieClip, selected:MovieClip, dragSlider:MovieClip, initValue:Number, returnValueCallback:Function):void
		{
			this.buttom = buttom;
			this.buttom.buttonMode = true;
			this.buttom.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.selected = selected;
			this.selected.buttonMode = true;
			this.selected.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.dragSlider = dragSlider;
			this.dragSlider.buttonMode = true;
			this.dragSlider.addEventListener(MouseEvent.MOUSE_DOWN, sliderFunction);
			this.value = initValue;
			this.buttom.x = this.dragSlider.width / 2;
			this.buttom.y = this.dragSlider.height / 2 - this.buttom.height / 2;
			this.addChild(this.buttom);
			this.selected.x = this.buttom.x;
			this.selected.y = this.buttom.y;
			this.selected.width = this.buttom.width * this.value - this.dragSlider.width;
			this.addChild(this.selected);
			this.dragSlider.x = this.x + this.selected.width + this.dragSlider.width / 2;
			this.dragSlider.y = 0;
			this.addChild(this.dragSlider);
			this.returnValueCallback = returnValueCallback;
		}
		
		//鼠标点击事件
		public function mouseClickFunction(event:MouseEvent):void
		{
			setValue();
		}
		
		//鼠标点击事件，拖拽
		private function sliderFunction(event:MouseEvent):void
		{
			//设置拖拽范围
			var sliderRectangle:Rectangle = new Rectangle(this.buttom.x - this.dragSlider.width / 2, 
					this.buttom.y + this.buttom.height / 2 - this.dragSlider.height / 2, 
					this.buttom.width - this.dragSlider.width / 2, 
					0);
			this.dragSlider.startDrag(false,sliderRectangle);	
			stage.addEventListener(MouseEvent.MOUSE_UP, sliderUp);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, sliderMove);
		}
		
		//取消事件侦听
		public function removeSliderEventListener():void
		{
			this.buttom.removeEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.selected.removeEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.dragSlider.removeEventListener(MouseEvent.MOUSE_DOWN, sliderFunction);
			this.buttom.buttonMode = false;
			this.selected.buttonMode = false;
			this.dragSlider.buttonMode = false;
			stage.removeEventListener(MouseEvent.MOUSE_UP, sliderUp);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE,sliderMove);
		}
		
		//设置事件侦听
		public function addSliderEventListenerFunc():void
		{
			this.buttom.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.selected.addEventListener(MouseEvent.CLICK, mouseClickFunction);
			this.dragSlider.addEventListener(MouseEvent.MOUSE_DOWN, sliderFunction);
			this.buttom.buttonMode = true;
			this.selected.buttonMode = true;
			this.dragSlider.buttonMode = true;
		}
		
		//拖拽后松开鼠标
		private function sliderUp(event:MouseEvent):void
		{
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, sliderMove);
			this.dragSlider.stopDrag();
		}
		
		//拖拽时移动鼠标
		private function sliderMove(event:MouseEvent):void
		{
			setValue();
		}
		
		//设置值，返回给回调函数
		public function setValue(value:Number = -1):void
		{
			if(value == -1)
			{
				this.value = ( stage.mouseX - this.x) / this.buttom.width;
				if(this.value < 0)
				{
					this.value = 0;
				}
				else if(this.value > 1)
				{
					this.value = 1;
				}
			}
			else
			{
				if(value < 0)
				{
					this.value = 0;
				}  
				else if(value > 1)
				{
					this.value = 1;
				}
				else
				{
					this.value = value;
				} 
			}
			this.selected.width = this.buttom.width * this.value;
			this.dragSlider.x = this.selected.x + this.selected.width - this.dragSlider.width;
			this.returnValueCallback.call(null, this.value);
		}
		
		//获取值
		public function getValue():Number
		{
			return this.value;
		}
	}
}