package TEST
{
	import flash.display.Sprite;
	
	import hpPlayer.CountDown;
	
	public class countdownTEST extends Sprite
	{
		private var countdown:CountDown;
		
		public function countdownTEST()
		{
			this.countdown = new CountDown();
			this.countdown.setup(3, 100, 30);
			stage.addChild(this.countdown);
			this.countdown.x = 50;
			this.countdown.y = 60;
		}
	}
}