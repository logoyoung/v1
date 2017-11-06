<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/6
 * Time: 下午1:58
 */

include __DIR__."/../../../include/init.php";
//include INCLUDE_DIR."SDK"

use service\due\rongCloud\RongUserService;
//use \SDK\rongCloud\RongCloud;

//$rongCloud = new \SDK\rongCloud\RongCloud();

//exit;

$uid=  1860;
$userService = new RongUserService($uid);

$user = new lib\User($uid);

$detail = $user->getUserInfo();
$token = $userService->getToken();

var_dump($token);