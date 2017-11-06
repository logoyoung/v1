<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/20
 * Time: 下午8:07
 */

include __DIR__."/../../../include/init.php";

$db = new DBHelperi_huanpeng(true);

$sql = "show tables like 'hpf_withdrawRecord%'";
$res = $db->query($sql);

while($row = $res->fetch_row())
{
	$sql = "alter table {$row[0]} change status `type` int(10) unsigned not null default 0";
	var_dump($db->query($sql));

	$sql = "update {$row[0]} set `type`=1 where id >0";
	var_dump($db->query($sql));
}