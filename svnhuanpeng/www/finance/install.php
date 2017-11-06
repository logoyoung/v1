<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/11
 * Time: 17:45
 */

include __DIR__."/../include/init.php";

use lib\Finance;


$sqlFilePath = __DIR__."/../docs/sql_table/finance.sql";
$db = new DBHelperi_huanpeng(true);

$sql = "show tables like '%hpf_%'";
$res = $db->query($sql);

while ($row = $res->fetch_row())
{
//	echo $row[0];
	$tablename =$row[0];
	$drop = "drop table $tablename";
	echo $drop."\n";
	var_dump($db->query($drop));
}

//exit;
$fp = fopen($sqlFilePath, 'rb');
$sql = '';
while($str = fgets($fp))
{
	if(!preg_match('/###/', $str))
	{
		$sql .= $str;
	}
}

$sql = str_replace("\r","\n", $sql);
foreach (explode(";\n", trim($sql)) as $query)
{
	$query = trim($query);
	$query = rtrim($query,';');
//	print_r($query);
	$db->query($query);
}

//TODO:这里边有两个主播有问题，需要确认下，2055。18575
$sql = "select uid from anchor where cid !=0 and cid!=15";
$res = $db->query($sql);
while ($row = $res->fetch_assoc())
{
	$rate = '0.7';
	$type = Finance::EXC_HB_GB;
	$uid = $row['uid'];
	$sql = "insert into hpf_rate(rate,`type`,`uid`) VALUE ($rate,$type,$uid)";
	$db->query($sql);
}

//print_r($sql);