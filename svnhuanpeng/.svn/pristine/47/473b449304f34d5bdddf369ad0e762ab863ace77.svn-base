<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午2:33
 */

include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : 0;

if(!$uid || !$enc){
	error(-4013);
}

$user = new UserHelp($uid, $db);

if($loginErr = $user->checkStateError($enc)){
	error($loginErr);
}

$costArray['hpBean'] = (int)$user->todaySendHpBeanCount();
$costArray['hpCoin'] = (int)$user->todaySendHpCoinCount();

$costArray = toString($costArray);

exit(json_encode($costArray));
