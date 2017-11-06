<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/11
 * Time: 17:45
 */

include __DIR__."/../../include/init.php";

use lib\Finance;
use lib\FinanceBase;

//$sqlFilePath = __DIR__."/../../docs/sql_table/finance.sql";
$db = new DBHelperi_huanpeng();

$sql = "show tables like '%hpf_%'";
$res = $db->query($sql);

while ($row = $res->fetch_row())
{
//	echo $row[0];
	$tablename =$row[0];
	if($tablename != "hpf_rate")
	{
		$drop = "truncate $tablename";
		echo $drop."\n";
		var_dump($db->query($drop));
	}
}

var_dump("truncate tmp_user_balance".$db->query("truncate tmp_user_balance"));
var_dump("truncate tmp_anchor_balance".$db->query("truncate tmp_anchor_balance"));

$sql = "select uid,hpbean,hpcoin from useractive";
$res = $db->query($sql);
while ($row = $res->fetch_assoc())
{
	$uid=$row['uid'];
	$bean = $row['hpbean'];
	$coin = $row['hpcoin'];
	$sql = "insert into tmp_user_balance (uid,coin,bean) VALUE($uid,$coin,$bean)";

	var_dump($sql."===>".$db->query($sql));
}

unset($sql);
unset($res);
unset($row);

$sql = "select uid,bean,coin from anchor";
$res = $db->query($sql);
while ($row = $res->fetch_assoc())
{
	$uid=$row['uid'];
	$bean = $row['bean'];
	$coin = $row['coin'];
	$sql = "insert into tmp_anchor_balance (uid,coin,bean) VALUE($uid,$coin,$bean)";

	var_dump($sql."===>".$db->query($sql));
}

unset($sql);
unset($res);
unset($row);

//exit;
//$fp = fopen($sqlFilePath, 'rb');
//$sql = '';
//while($str = fgets($fp))
//{
//	if(!preg_match('/###/', $str))
//	{
//		$sql .= $str;
//	}
//}
//
//$sql = str_replace("\r","\n", $sql);
//foreach (explode(";\n", trim($sql)) as $query)
//{
//	$query = trim($query);
//	$query = rtrim($query,';');
////	print_r($query);
//	$db->query($query);
//}

//TODO:这里边有两个主播有问题，需要确认下，2055。18575
$sql = "select uid from anchor where cid !=0 and cid!=15";
$res = $db->query($sql);
while ($row = $res->fetch_assoc())
{
	$rate = '0.7';
	$type = FinanceBase::EXC_HB_GB;
	$uid = $row['uid'];
	$sql = "insert into hpf_rate(rate,`type`,`uid`) VALUE ($rate,$type,$uid)";
	$db->query($sql);
}

//print_r($sql);

$cmd = 'php /usr/local/huanpeng/bin/finance/syncToFinanceTable.php '.$GLOBALS['env'];

print_r(`$cmd`);