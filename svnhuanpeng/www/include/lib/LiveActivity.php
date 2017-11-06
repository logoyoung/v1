<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/31
 * Time: 15:10
 */


namespace lib;
use \DBHelperi_huanpeng;


class LiveActivity{

	private $_db;

	const LIVE_TABLE = 'live';

	public function __construct($db=null)
	{
		if(!$db)
		{
			$db = self::getDB();
		}
		$this->_db = $db;
	}


	public static function getDB()
	{
		return new DBHelperi_huanpeng();
	}
	public static function getFollowLivesByUids($uids,$db=null)
	{
		if(!is_array($uids)||!count($uids))
		{
			return false;
		}
		if(!$db)
		{
			$db = self::getDB();
		}
		$uids = implode(',',$uids);
		$r = $db->where("uid in ({$uids}) and status=".LIVE)->select(self::LIVE_TABLE);
		return $r;
	}

	public static function getLiveUids($db=null,$size=100)
	{
		if(!$db)
		{
			$db = self::getDB();
		}
		$r = $db->field('uid')->where('status='.LIVE.' order by liveid desc limit '.$size)->select(self::LIVE_TABLE);
		$ret = array_map(function($user){
			return $user['uid'];
		},$r);
		/****添加测试假数据*/
		/*$count = $size-count($ret);
		if($count)
		{
			$r2 = $db->field('uid')->where('status>'.LIVE.' group by uid order by liveid desc limit '.$count)->select(self::LIVE_TABLE);
			$ret2 = array_map(function($user){
				return $user['uid'];
			},$r2);
			$ret = array_merge($ret,$ret2);
		}*/
		return $ret;
	}
}


/*************************test*******************/
/*include '/usr/local/huanpeng/include/init.php';
use \DBHelperi_huanpeng;
//use hp\lib\LiveActivity;
$uids = [1870,2220];
$db = new DBHelperi_huanpeng();
$lives = LiveActivity::getFollowLivesByUids($uids,$db);
var_dump($lives);*/
