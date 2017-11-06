<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/6/2
 * Time: 11:50
 */


include '../../../include/init.php';


use lib\Anchor;
use lib\Live;


class AdminStopLive
{
	private $_uid;
	private $_liveID;
	private $_db;
	private $_order;
	private $_reason;

	static $msgTypeGroup = array('notice' => 1, 'stop'=>2, 'kill'=>3);

	public function __construct()
	{
		$this->_uid = isset($_POST['luid']) ? (int)($_POST['luid']) : 0;
		$this->_liveID = isset($_POST['liveID']) ? (int)($_POST['liveID']) : 0;
		$this->_order = isset($_POST['order'])?(int)$_POST['order']:0;
		$this->_reason = isset($_POST['reason'])?trim($_POST['reason']):'';
		$this->_db = new DBHelperi_huanpeng();
	}

	private function _checkUser()
	{
		if( empty( $this->_uid ) || empty( $this->_liveID ) ||empty($this->_order) || empty( $this->_reason ) )
		{
			error2( -4013, 2 );
		}
		//用户类型
		if(!Anchor::isAnchor($this->_uid, $this->_db))
		{
			error2( -4057, 2 );
		}
		if( !Live::checkLiveExistByUid($this->_uid,$this->_liveID, $this->_db) )
		{
			error2( -993, 2 );
		}
	}
	public function exec()
	{
		//主播检测
		$this->_checkUser();
		//do
		if( $this->_order == self::$msgTypeGroup['notice'] )
		{

		}
		if( $this->_order == self::$msgTypeGroup['stop'] )
		{
			$Live = new Live($this->_uid,$this->_db);
			$r = $Live->adminStopLive();
			mylog("管理员停止了用户{$this->_uid}的直播：{$this->_liveID}",LOG_DIR.'Live.error.log');
			succ();
		}
		if( $this->_order == self::$msgTypeGroup['kill'] )
		{

		}
	}
}

$AdminStopLive = new AdminStopLive();
$AdminStopLive->exec();