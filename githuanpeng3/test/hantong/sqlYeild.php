<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/5/3
 * Time: 15:31
 */


include __DIR__."/../../../include/init.php";


$db = new \DBHelperi_huanpeng();

function yieldGet(\DBHelperi_huanpeng $db)
{
	$sql = "select * from userstatic where uid=0";
	$res = $db->query($sql);
	return [];
	while($row=  $res->fetch_assoc())
	{
		yield $row['uid'];
	}
}


foreach (yieldGet($db) as $value)
{
	print_r($value);
}