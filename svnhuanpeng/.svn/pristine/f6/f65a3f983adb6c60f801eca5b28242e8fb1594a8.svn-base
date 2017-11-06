<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/4
 * Time: 17:31
 */
include __DIR__."/../../../include/init.php";

$db = new DBHelperi_huanpeng();

$sql = "select * from exchange_detail_201706 WHERE ctime > '2017-07-01 00:00:00'";

$res = $db->query($sql);

$db->query('begin');
$db->autocommit(false);

while($row = $res->fetch_assoc())
{

	file_put_contents(LOG_DIR."fixExchangeOtid.source.data", json_encode($row)."\n", FILE_APPEND);
	$time = substr($row['otid'],0,10);
	$time = $time -3600;

	$append = substr($row['otid'],10);

	$newOtid = $time.$append;

//	var_dump($time . $append);
//	var_dump($row['otid']);


	if(fixExchange_201706Data($row['id'], $newOtid, $db) <= 0)
	{
		$db->rollback();
	}
}

$db->commit();
$db->autocommit(true);

function fixExchange_201706Data($id,$newOtid,\DBHelperi_huanpeng $db)
{
	$sql = "update exchange_detail_201706 set otid=$newOtid WHERE id=$id";
	var_dump($db->query($sql));

	return $db->affectedRows;
}

