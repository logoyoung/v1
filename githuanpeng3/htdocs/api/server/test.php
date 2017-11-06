<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/21
 * Time: 下午6:02
 */



include __DIR__.'/../../../include/init.php';

$db = new DBHelperi_huanpeng(true);

$sql = "select * from gift";

$res = $db->query($sql);

print_r($GLOBALS['env']);

while($row = $res->fetch_assoc())
{
	print_r($row);
}
