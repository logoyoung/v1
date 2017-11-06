<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/18
 * Time: 下午5:41
 */

include __DIR__."/../../../include/init.php";

$service = new \service\room\RoomGiftService();


$configid = $argv[1] ?? null;

var_dump($configid);

if(is_string($configid))
{
	$configid = intval($configid);
}

$service->update($configid);

