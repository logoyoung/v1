<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/25
 * Time: 上午10:18
 */

include __DIR__."/../../../include/init.php";


$uid = "258397";


$userNick = new \service\user\UserNickService();
$userNick->setUid($uid);

$oldNick = "hpxa921axk";

$userNick->alterByThreeSideLogin($oldNick);

changeIsfreeStatus( $uid, 1, $db );
$event = new \service\event\EventManager();
$event->trigger( \service\event\EventManager::ACTION_USER_REGISTER, [ 'uid' => $uid ] );

