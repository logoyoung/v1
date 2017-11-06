<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../include/init.php';
use service\common\PcCommon;
/**
 * 
 * ===============
 * @author yalong
 * @time 2017-5-26 上午11:01:40
 * @version v1.0.0
 * @desc 游戏专区 前端交互层 
 * ===============
 */
class GameZoneV2 extends PcCommon{
	
	/**
	 * @desc 去服务层拉去数据
	 * @return datas | array
	 */
	private function get_datas(){
		return [];
	}
	public function display(){  
		$datas = $this->get_datas();
		$this->smarty->assign("datas",$datas);
		$this->smarty->clearCache("gamezone.tpl");
		$this->smarty->display("gamezone1.tpl");
	}
}

$gameZone = new GameZoneV2();
$gameZone->display();

?>