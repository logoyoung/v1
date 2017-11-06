<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/21
 * Time: 下午2:23
 */


include __DIR__ . "/../../../include/init.php";

use lib\AnchorExchange;

class CompanyWithdraw
{
	private $_db;
	private $_redis;
	private $_exchangeService;
	private $_failedList;
	private $_companyAnchorObj;

	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		$this->_db      = $db;
		$this->_redis   = $redisHelp;
		$this->_exchangeService = new AnchorExchange();
		$this->_failedList = [];
		$this->_finance = new \lib\Finance();
		$this->_companyAnchorObj = new lib\anchor\CompanyAnchor();
	}

	public function getCompanyAnchor($uidList)
	{
		$sql = "select uid from anchor where cid !=0 and cid !=15 AND cid NOT IN (SELECT id as cid FROM company WHERE `type`=2)";
		$res = $this->_db->query($sql);

		if(!$uidList)
		{
			$uidList = [];

			while ($row = $res->fetch_assoc())
			{
				array_push($uidList, $row['uid']);
			}
		}

		$userProperty = $this->_finance->getBalanceByUids($uidList);

		$anchor = [];
//		var_dump($userProperty);
		foreach ($userProperty as $uid => $property)
		{
			if($property['gb'] >0 || $property['gd'] >0)
			{
				//TODO 取整是为了 目前底层设计不支持小数，exchange_detail_template number
				$anchor[$uid]['bean'] = intval($property['gd']);
				$anchor[$uid]['coin'] = intval($property['gb']);
			}
		}

		return $anchor;
	}

	public function getCompanyAnchorForWithdraw()
	{
		$companyAnchorList = $this->_companyAnchorObj->getCompanyUserIdList();

		if(!is_array($companyAnchorList))
		{
			$this->_log(__FUNCTION__."get company AnchorList failed");

			return false;
		}

		$companyTotalIncome = [];
		$userPropertyList = [];

		foreach ($companyAnchorList as $cid => $uidList)
		{
			$userProperty = $this->_finance->getBalanceByUids($uidList);

			foreach ($userProperty as $uid => $property )
			{
				if(!isset($companyTotalIncome[$cid]))
				{
					$companyTotalIncome[$cid] = 0;
				}

				$companyTotalIncome[$cid] += intval($property['gd']) + intval($property['gb']);
				$userPropertyList[$uid] = $property;
			}
		}

		$resultList = [];

		foreach ($companyTotalIncome as $cid => $total)
		{
			if($total >= 100)
			{
//				array_push($resultList, $companyAnchorList[$cid]);
				foreach ($companyAnchorList[$cid] as $uid)
				{
					$resultList[$uid]['bean'] = intval($userPropertyList[$uid]['gd']);
					$resultList[$uid]['coin'] = intval($userPropertyList[$uid]['gb']);
				}
			}
		}

		return $resultList;
	}

	public function withdraw( $uidList=[])
	{

//		$list=$this->getCompanyAnchor($uidList);
//		var_dump($list);
//		echo json_encode($list);

		$list = $this->getCompanyAnchorForWithdraw();

		foreach ( $list as $uid => $property )
		{
			if($property['coin'] <=0)
			{
				continue;
			}

			$otid = $this->_getOtid();
			$ret = $this->_exchangeService->coinToCNY($uid,$property['coin'],$otid);

			if(!$ret)
			{
				$log['uid'] = $uid;
				$log['property'] = $property;
				$log['action'] = __FUNCTION__;
				$this->_log(json_encode($log));

				unset($log);
			}
		}
	}

	public function excGD2GB($uidList=[])
	{
//		$list=$this->getCompanyAnchor($uidList);
//		var_dump($list);
//		echo json_encode($list);
//		return;

		$list = $this->getCompanyAnchorForWithdraw();
		foreach ( $list as $uid => $property )
		{
			if($property['bean'] <=0)
			{
				continue;
			}

			//TODO gd => gb
			$otid = $this->_getOtid();
			$ret = $this->_exchangeService->beanToCoin($uid,$property['bean'],$otid);

			if(!$ret)
			{

				$log['uid'] = $uid;
				$log['property'] = $property;
				$log['action'] = __FUNCTION__;
				$this->_log(json_encode($log));

				unset($log);
			}
		}
	}

	private function addToFailedList($func, $uid)
	{
		if(!isset($this->_failedList[$func]))
		{
			$this->_failedList[$func] = [];
		}

		array_push($this->_failedList[$func], $uid);
	}

	public function doFailedList()
	{

	}

	private function _log($msg)
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
	private function _getOtid()
	{
		//获取上个月的时间为基准的otid
		$day = date('d');

		$timestamp =time() - 24*3600 *$day;

		return intval($timestamp.rand(10000,99999).rand(1000,9999));
	}
}


$db = new DBHelperi_huanpeng();
$redis = new RedisHelp();

echo "runtime ".date("Y-m-d H:i:s");

$obj = new CompanyWithdraw($db,$redis);

//var_dump($obj->getCompanyAnchorForWithdraw());


$obj->excGD2GB();
$obj->withdraw();


