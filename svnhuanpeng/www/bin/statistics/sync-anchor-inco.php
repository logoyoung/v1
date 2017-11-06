<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/1/19
 * Time: 上午11:44
 */


//all anchor list

//get anchor companyid

//
//
//$beanlist = array('t24130'=>array('bean'=>111,'uid'=>180,'liveid'=>111),'t24131'=>array('bean'=>1121,'uid'=>180,'liveid'=>111));
//$coinlist = array('t24130'=>array('coin'=>1112,'uid'=>180,'liveid'=>111));
//
//($array = array_merge_recursive($beanlist,$coinlist));
//$back = array();
//array_walk($array,function(&$val,$key){
//	if(is_array($val['uid']))
//		$val['uid'] = $val['uid'][0];
//
//	if(is_array($val['liveid']))
//		$val['liveid'] = $val['liveid'][0];
//
//});
//
//print_r(($array));
////print_r(array_merge($array[0],$array[1]));
//
//exit;

include_once '/usr/local/huanpeng/include/init.php';

mylog("out env is ".$argv[1], LOGFN_SENDGIFT_LOG);

if(($argv[2])){
	$time = $argv[2] - 900;
}else{
	$time = time() - 900;
}

$cur_stime = date('Y-m-d H:',$time)."00:00";
$cur_etime = date('Y-m-d H:',$time)."59:59";


echo "==========  doing start ==========\n\n";
mylog("current date is ".$cur_stime, LOGFN_SENDGIFT_LOG);

if($argv[1] != 'DEV'){
	$GLOBALS['env'] = "PRO";
}

$db = new DBHelperi_huanpeng();

$list = array();
$sql = "select uid from anchor";
$res = $db->query($sql);
while ($row = $res->fetch_assoc()){
	array_push($list,$row);
}


foreach ($list as $key => $val){
	$uid = $val['uid'];
	$bean = getBeanIncomeGroupByLiveID($uid,$cur_stime,$cur_etime,$db);
	$coin = getCoinIncomeGroupByLiveID($uid,$cur_stime,$cur_etime,$db);


//	mylog("bean list is ". json_encode($bean),LOGFN_SENDGIFT_LOG);
//	mylog("coin list is ". json_encode($coin),LOGFN_SENDGIFT_LOG);
	if(!$bean && !$coin)
		continue;

	$merge_val = array_merge_recursive($bean,$coin);
	array_walk($merge_val,function(&$val,$key){
		if(is_array($val['uid']))
			$val['uid'] = $val['uid'][0];

		if(is_array($val['liveid']))
			$val['liveid'] = $val['liveid'][0];
	});

	mylog("merge list value".json_encode($merge_val), LOGFN_SENDGIFT_LOG);

	$list[$key] = $merge_val;

//	if($bean && $coin){
//		$merge_val = array_merge_recursive($bean,$coin);
//		array_walk($merge_val,function(&$val,$key){
//			if(is_array($val['uid']))
//				$val['uid'] = $val['uid'][0];
//
//			if(is_array($val['liveid']))
//				$val['liveid'] = $val['liveid'][0];
//		});
//		$list[$key] = $merge_val;
//	}else if($bean){
//		$list[$key] = $bean;
//	}else if($coin){
//		$list[$key] = $coin;
//	}else{
//		continue;
//	}

	/*
		list=array(
			0 =>array(
				1111=>array(
					liveid=>1111,
					uid=>110,
					bean=>18,
					coin=>22
				)
			)
		);
	 */

	if(!$list[$key]){
		mylog("array_merge failed ",LOGFN_SENDGIFT_LOG);
		continue;
	}

//	mylog(json_encode($list),LOGFN_SENDGIFT_LOG);
	mylog('+OK', LOGFN_SENDGIFT_LOG);

	foreach ($list[$key] as $liveInfo){
//		mylog(json_encode($liveInfo),LOGFN_SENDGIFT_LOG);
		$liveInfo['coin'] = isset($liveInfo['coin']) ? (int)$liveInfo['coin'] : 0;
		$liveInfo['bean'] = isset($liveInfo['bean']) ? (int)$liveInfo['bean'] : 0;
		$liveInfo['utime'] = date('Y-m-d H:i:s');
		$liveInfo['companyid'] = (int)getCompanyIdByUid($liveInfo['uid'],$db);
		$liveInfo['date'] = $cur_stime;

		$update['coin'] = $liveInfo['coin'];
		$update['bean'] = $liveInfo['bean'];
		$update['utime'] = $liveInfo['utime'];

		$sql = $db->insertDuplicate('log_anchor_income_day',$liveInfo,$update, true)." ,count_t=count_t+1";

		$db->query($sql);
		$affectrows = $db->affectedRows;
		mylog("liveid {$liveInfo['liveid']} is finish  \n the affectrows is $affectrows \n", LOGFN_SENDGIFT_LOG);
		echo "\n";
	}

//	$list[$key]['utime'] = date('Y-m-d H:i:s');
//	$list[$key]['date'] = $cur_stime;
//	$list[$key]['companyid'] = getCompanyIdByUid($list[$key]['uid'],$db);
//
//	$update['coin'] = $list[$key]['coin'];
//	$update['bean'] = $list[$key]['bean'];
//	$update['utime'] = $list[$key]['utime'];
//
//	$sql = $db->insertDuplicate('log_anchor_income_day',$list[$key],$update, true)." ,count_t=count_t+1";
//
//	$db->query($sql);
//
//	$insertID = $db->insertID;
//
//	echo "liveid {$list[$key]['liveid']} is finish \n result id is {$insertID}\n";
//	echo "\n";
}


echo "==========  doing end ==========\n\n\n\v";



//
//foreach ($list as $key => $val){
//	$resultList[$key]['uid'] = $val['uid'];
//
//}



function  getBeanIncomeGroupByLiveID($uid,$stime,$etime,$db){
	$sql = "select sum(giftnum) as `count`,liveid from giftrecord where luid=$uid and ctime BETWEEN '$stime' and '$etime' group by liveid";
	$res = $db->query($sql);

//	mylog("bean income group  $sql",LOGFN_SENDGIFT_LOG);

	$list = array();

	while($row = $res->fetch_assoc()){
//		mylog("while row bean income ".json_encode($row),LOGFN_SENDGIFT_LOG);
		$list['t'.$row['liveid']] = array(
			'bean'=>(int)$row['count'],
			'uid'=>$uid,
			'liveid'=>$row['liveid'],
		);
	}
//	mylog(json_encode($list),LOGFN_SENDGIFT_LOG);
//	if($list){
//		mylog(json_encode($list),LOGFN_SENDGIFT_LOG);
//		exit();
//	}

	return $list;
}


function getCoinIncomeGroupByLiveID($uid,$stime,$etime,$db){
	$sql = "select liveid,id from giftrecordcoin where luid=$uid and ctime BETWEEN '$stime' and '$etime'";
//	mylog("$sql", LOGFN_SENDGIFT_LOG);

	$res = $db->query($sql);

	$list = array();

	while($row = $res->fetch_assoc()){
//		mylog("while row coin income ".json_encode($row),LOGFN_SENDGIFT_LOG);
		$list[$row['liveid']][] = $row['id'];
	}

	$retList = array();

	foreach ($list as $key => $val){
		$retList['t'.$key] = array(
			'coin'=>(int)sumTotalCoinIncome($uid,$val,$db),
			'uid'=>$uid,
			'liveid'=>$key
		);
	}
//	mylog(json_encode($retList),LOGFN_SENDGIFT_LOG);
//	if($retList){
//		mylog(json_encode($retList),LOGFN_SENDGIFT_LOG);
//		exit();
//	}
	return $retList;
}




function sumTotalCoinIncome($uid,$info,$db){
//	mylog(json_encode($info), LOGFN_SENDGIFT_LOG);
	if(!$info)
		return 0;

	$info = "('".implode("','", $info)."')";//('123','123','123');
	$sql = "select sum(income) as coin_income from billdetail where beneficiaryid = $uid and type=0 and info in $info";

//	mylog($info, LOGFN_SENDGIFT_LOG);

//	mylog("sql =====   $sql",LOGFN_SENDGIFT_LOG);

	$res = $db->query($sql);
	if(!$res)
		return false;

	return (int)$res->fetch_assoc()['coin_income'];
}



//
//$sql = "select liveid,uid from live where  ctime BETWEEN '$cur_stime' AND '$cur_etime'";
//$res = $db->query($sql);
//while($row = $res->fetch_assoc()){
//	array_push($list, $row);
//}

//echo "count list :".count($list);


//foreach ($list as $key => $val){
//	$update = array();
//	$list[$key]['bean'] = $update['bean'] = sumTotalBeanIncome($val['liveid'],$cur_stime,$cur_etime,$db);
//	$list[$key]['coin'] = $update['coin'] = sumTotalCoinIncome($val['uid'], getBillInfoFromLiveid($val['liveid'],$cur_stime,$cur_etime,$db),$db);
//	$list[$key]['utime'] = $update['utime'] = date('Y-m-d H:i:s');
//	$list[$key]['date'] = $cur_stime;
//	$list[$key]['companyid'] = getCompanyIdByUid($val['uid'],$db);
//
////	echo "liveid {$list[$key]['liveid']} is been doing....\n";
//
//	$sql = $db->insertDuplicate('log_anchor_income_day',$list[$key],$update, true)." ,count_t=count_t+1";
//
////	echo "the sql is $sql \n";
//
////	print_r($list[$key]);
//
//	$db->query($sql);
//
//	$insertID = $db->insertID;
//
////	echo "liveid {$list[$key]['liveid']} is finish \n result id is {$insertID}\n";
////	echo "\n";
//}
//echo "==========  doing end ==========\n\n\n\v";

//加入时间校验 方便历史数据的生成
function getBillInfoFromLiveid($liveid,$stime,$etime, $db){
	$sql = "select id from giftrecordcoin where liveid=$liveid and ctime BETWEEN '$stime' and '$etime'";
	$res = $db->query($sql);
	if(!$res)
	{
		return false;
	}

	$idlist = array();
	while($row = $res->fetch_assoc()){
		array_push($idlist, $row['id']);
	}

	return $idlist;
}


function sumTotalBeanIncome($liveid,$stime,$etime,$db){
	$sql = "select sum(giftNum) as bean_income from giftrecord where liveid=$liveid and ctime BETWEEN '$stime' and '$etime'";
	$res = $db->query($sql);
	if(!$res)
		return false;

	return (int)$res->fetch_assoc()['bean_income'];
}

function getCompanyIdByUid($uid,$db){
	$sql = "select cid from company_anchor where uid=$uid";
	$res = $db->query($sql);
	if(!$res)
		return false;

	return (int)$res->fetch_assoc()['cid'];
}
?>