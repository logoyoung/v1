<?php


include '../../../include/init.php';


use lib\Anchor;
use lib\Live;
use lib\live\LiveLog;

class AdminStopLive
{
	private $_uid;
	private $_liveID;
	private $_db;

	public function __construct()
	{
		$this->_uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
		$this->_liveID = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';
		$this->_db = new DBHelperi_huanpeng();
	}

	private function _checkUser()
	{
		if( empty( $this->_uid ) || empty( $this->_liveID ) )
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
		$Live = new Live($this->_uid,$this->_db);
		$r = $Live->adminStopLive();
		//mylog("管理员停止了用户{$this->_uid}的直播：{$this->_liveID}",LOG_DIR.'Live.error.log');
		LiveLog::applog("record:管理员停止了用户{$this->_uid}的直播：{$this->_liveID}");
		succ();
	}
}

$AdminStopLive = new AdminStopLive();
$AdminStopLive->exec();

/*


$uid = 		isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = 	isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$liveid = 	isset($_POST['liveID']) ? trim($_POST['liveID']) : '';

if( empty( $uid ) || empty( $encpass ) )
{
	error2( -4013, 2 );
}

$db = new DBHelperi_huanpeng();

//用户类型
if(!Anchor::isAnchor($uid, $db))
	error2(-4057,2);
//登录检测
$Anchor = new Anchor($uid,$db);
$loginErrCode = $Anchor->checkStateError($encpass);
if($loginErrCode!==true)
{
	error2($loginErrCode,2);
}
$Live = new Live($uid,$db);
$r = $Live->adminStopLive();
mylog("管理员停止了用户{$uid}的直播：{$liveid}",LOG_DIR.'Live.error.log');
succ();*/
